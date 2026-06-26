<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $customer = auth()->user()->customer;
        
        $wishlists = Wishlist::with('product.primaryImage')
            ->where('customer_id', $customer?->id)
            ->latest()
            ->get();
            
        $products = $wishlists->pluck('product')->filter();
        $wishlistIds = $products->pluck('id')->toArray();
        
        return view('pages.wishlist', compact('products', 'wishlistIds'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $customer = auth()->user()->customer;
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $wishlist = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            // Đã có -> xóa
            $wishlist->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // Chưa có -> thêm
            Wishlist::create([
                'customer_id' => $customer->id,
                'product_id' => $request->product_id
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
