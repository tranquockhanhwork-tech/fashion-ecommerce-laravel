<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\CustomerAddress;

class CheckoutController extends Controller
{
    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $cartItems = collect();
        $cartTotal = 0;

        $user = Auth::user();
        if ($user && $user->customer && $user->customer->cart) {
            $cartItems = $user->customer->cart->items()->with(['variant.product'])->get();
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

        $cart      = $customer->cart->load('items.variant.product');
        $cartItems = $cart->items;

        $subtotal = 0;
        foreach ($cartItems as $ci) {
            $basePrice = $ci->variant->product->promotional_price ?: $ci->variant->product->price;
            $itemPrice = $ci->variant->price_override ?: $basePrice;
            $subtotal += $itemPrice * $ci->quantity;
        }

        $shippingFee = (float) $request->input('shipping_fee', 0);

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

        foreach ($cartItems as $ci) {
            $basePrice = $ci->variant->product->promotional_price ?: $ci->variant->product->price;
            $unitPrice = $ci->variant->price_override ?: $basePrice;
            $order->items()->create([
                'product_variant_id' => $ci->product_variant_id,
                'quantity'           => $ci->quantity,
                'unit_price'         => $unitPrice,
                'subtotal'           => $unitPrice * $ci->quantity,
            ]);
        }

        $cart->items()->delete();

        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn sớm.');
    }

    public function success(int $order): \Illuminate\View\View
    {
        $order = Order::with('items.variant.product')->findOrFail($order);
        if (Auth::user()->customer?->id !== $order->customer_id) {
            abort(403);
        }
        return view('pages.order-confirmation', compact('order'));
    }
}
