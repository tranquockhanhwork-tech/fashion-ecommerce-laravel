<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Services\VietQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\ProductVariant;

class CheckoutController extends Controller
{
    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $cartItems = collect();
        $cartTotal = 0;

        $user = Auth::user();
        if ($user && $user->customer && $user->customer->cart) {
            $cartItems = $user->customer->cart->items()->with([
                'variant' => fn ($query) => $query->withOptionRelations()->with('product'),
            ])->get();
            foreach ($cartItems as $ci) {
                $basePrice = $ci->variant->product->promotional_price ?: $ci->variant->product->price;
                $itemPrice = $ci->variant->price_override ?: $basePrice;
                $cartTotal += $itemPrice * $ci->quantity;
            }
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $customer = $user?->customer;
        $customer?->load('addresses');

        return view('pages.checkout', compact('cartItems', 'cartTotal', 'customer'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user     = Auth::user();
        $customer = $user->customer;

        if (!$customer || !$customer->cart || $customer->cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // ===== Xác định thông tin địa chỉ =====
        if ($request->filled('address_id')) {
            $addr = CustomerAddress::where('customer_id', $customer->id)
                ->findOrFail($request->address_id);
            $recipientName  = $addr->recipient_name;
            $recipientPhone = $addr->recipient_phone;
            $shippingAddr   = $addr->full_address;
        } else {
            $request->validate([
                'recipient_name' => 'required|string|max:100',
                'phone'          => 'required|string|max:20',
                'email'          => 'required|email|max:100',
                'city'           => 'required|string|max:100',
                'district'       => 'required|string|max:100',
                'address'        => 'required|string|max:255',
            ]);
            $recipientName  = $request->recipient_name;
            $recipientPhone = $request->phone;
            $shippingAddr   = $request->address . ', ' . $request->district . ', ' . $request->city;
        }

        $request->validate(['payment_method' => 'required|in:cod,bank,momo,vnpay']);

        $shippingFee = (float) $request->input('shipping_fee', 0);

        try {
            $order = DB::transaction(function () use (
                $customer,
                $recipientName,
                $recipientPhone,
                $shippingAddr,
                $shippingFee,
                $request
            ) {
                $cart = $customer->cart()->with('items')->first();
                $cartItems = $cart?->items ?? collect();

                if ($cartItems->isEmpty()) {
                    throw new \DomainException('Giỏ hàng của bạn đang trống.');
                }

                $variantIds = $cartItems->pluck('product_variant_id')->unique()->values();
                $variants = ProductVariant::with('product')
                    ->withOptionRelations()
                    ->whereIn('id', $variantIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0;
                foreach ($cartItems as $cartItem) {
                    $variant = $variants->get($cartItem->product_variant_id);

                    if (!$variant || !$variant->product) {
                        throw new \DomainException('Một trong các sản phẩm trong giỏ hàng không còn khả dụng.');
                    }

                    if ($variant->stock_quantity < $cartItem->quantity) {
                        $variantLabel = collect([$variant->color, $variant->size])->filter()->join(' / ');
                        $productName = $variant->product->name;
                        $message = 'Sản phẩm "' . $productName . '"';
                        if ($variantLabel !== '') {
                            $message .= ' (' . $variantLabel . ')';
                        }
                        $message .= ' chỉ còn ' . $variant->stock_quantity . ' sản phẩm trong kho.';

                        throw new \DomainException($message);
                    }

                    $basePrice = $variant->product->promotional_price ?: $variant->product->price;
                    $itemPrice = $variant->price_override ?: $basePrice;
                    $subtotal += $itemPrice * $cartItem->quantity;
                }

                $order = Order::create([
                    'customer_id'       => $customer->id,
                    'recipient_name'    => $recipientName,
                    'recipient_phone'   => $recipientPhone,
                    'shipping_address'  => $shippingAddr,
                    'subtotal'          => $subtotal,
                    'shipping_fee'      => $shippingFee,
                    'discount_amount'   => 0,
                    'total_amount'      => $subtotal + $shippingFee,
                    'status'            => 'pending',
                    'payment_status'    => 'unpaid',
                    'payment_method'    => strtoupper($request->payment_method),
                    'shipping_provider' => 'Viettel Post',
                    'note'              => $request->note,
                ]);

                foreach ($cartItems as $cartItem) {
                    $variant = $variants->get($cartItem->product_variant_id);
                    $basePrice = $variant->product->promotional_price ?: $variant->product->price;
                    $unitPrice = $variant->price_override ?: $basePrice;

                    $order->items()->create([
                        'product_variant_id' => $cartItem->product_variant_id,
                        'quantity'           => $cartItem->quantity,
                        'unit_price'         => $unitPrice,
                        'subtotal'           => $unitPrice * $cartItem->quantity,
                    ]);

                    $variant->stock_quantity -= $cartItem->quantity;
                    $variant->save();
                }

                $cart->items()->delete();

                return $order;
            });
        } catch (\DomainException $exception) {
            return redirect()->route('cart.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn sớm.');
    }

    public function success(int $order, VietQrService $vietQrService): \Illuminate\View\View
    {
        $order = Order::with([
            'items.variant' => fn ($query) => $query->withOptionRelations()->with('product'),
        ])->findOrFail($order);
        if (Auth::user()->customer?->id !== $order->customer_id) {
            abort(403);
        }

        $paymentMethodLabels = [
            'COD' => 'Thanh toán khi nhận hàng (COD)',
            'BANK' => 'Chuyển khoản ngân hàng',
            'MOMO' => 'Ví MoMo',
            'VNPAY' => 'VNPay',
        ];

        $paymentQr = null;
        if ($order->payment_method === 'BANK' && $order->payment_status !== 'paid') {
            $paymentQr = $vietQrService->generateOrderQr($order);
        }

        return view('pages.order-confirmation', [
            'order' => $order,
            'paymentQr' => $paymentQr,
            'paymentMethodLabel' => $paymentMethodLabels[$order->payment_method] ?? $order->payment_method,
        ]);
    }
}
