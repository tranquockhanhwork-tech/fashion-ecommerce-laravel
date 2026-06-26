<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $heroLookProduct = Product::where('slug', 'premium-streetwear-set')
            ->with('primaryImage')
            ->first();

        // Lấy 4 sản phẩm nổi bật: ưu tiên có khuyến mãi, eager load ảnh chính
        $featuredProducts = Product::where('is_active', true)
            ->with(['primaryImage', 'reviews'])
            ->orderByDesc('promotional_price') // sản phẩm đang giảm giá lên đầu
            ->take(4)
            ->get();

        $wishlistIds = [];
        if (auth()->check() && auth()->user()->customer) {
            $wishlistIds = \App\Models\Wishlist::where('customer_id', auth()->user()->customer->id)
                ->pluck('product_id')->toArray();
        }

        return view('pages.home', compact('featuredProducts', 'wishlistIds', 'heroLookProduct'));
    }

    public function about(): \Illuminate\View\View
    {
        return view('pages.about');
    }

    public function contact(): \Illuminate\View\View
    {
        return view('pages.contact');
    }
}
