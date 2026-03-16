<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('pages.cart');
    }

    public function add(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'product_id'         => 'required|exists:products,id',
            'quantity'           => 'nullable|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $user     = \Illuminate\Support\Facades\Auth::user();
        $customer = $user->customer;

        // Tự tạo profile customer nếu chưa có
        if (!$customer) {
            $customer = $user->customer()->create([
                'full_name' => $user->name,
                'user_id'   => $user->id,
            ]);
        }

        // Lấy hoặc tạo giỏ hàng của customer
        $cart = $customer->cart()->firstOrCreate(['customer_id' => $customer->id]);

        $quantity = (int) $request->input('quantity', 1);
        $product = \App\Models\Product::with('variants')->findOrFail($request->product_id);

        // Xác định biến thể sản phẩm
        $variantId = $request->product_variant_id;
        if (!$variantId) {
            $variant = $product->variants->first(fn ($item) => $item->stock_quantity > 0);
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không có biến thể nào.']);
            }
        } else {
            $variant = $product->variants->firstWhere('id', (int) $variantId);
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Biến thể sản phẩm không hợp lệ.'], 422);
            }
        }

        if ($variant->stock_quantity < 1) {
            return response()->json(['success' => false, 'message' => 'Biến thể bạn chọn đã hết hàng.'], 422);
        }

        // Cộng dồn nếu đã có, thêm mới nếu chưa có
        $cartItem = $cart->items()->where('product_variant_id', $variant->id)->first();
        $nextQuantity = $quantity + ($cartItem?->quantity ?? 0);

        if ($nextQuantity > $variant->stock_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng vượt tồn kho. Chỉ còn ' . $variant->stock_quantity . ' sản phẩm cho biến thể này.',
            ], 422);
        }

        if ($cartItem) {
            $cartItem->quantity = $nextQuantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity'           => $quantity,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
    }

    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->customer && $user->customer->cart) {
            $item = $user->customer->cart->items()->with('variant')->find($id);
            if ($item) {
                if ($item->variant && $request->quantity > $item->variant->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Số lượng vượt tồn kho. Chỉ còn ' . $item->variant->stock_quantity . ' sản phẩm.',
                    ], 422);
                }

                $item->quantity = $request->quantity;
                $item->save();
                return response()->json(['success' => true]);
            }
        }
        return response()->json(['success' => false], 404);
    }

    public function remove(int $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->customer && $user->customer->cart) {
            $item = $user->customer->cart->items()->find($id);
            if ($item) {
                $item->delete();
                return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
            }
        }
        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ');
    }
}
