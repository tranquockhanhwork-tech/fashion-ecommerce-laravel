<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $query = Product::where('is_active', true)
            ->with(['primaryImage', 'category']);

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc giá tối đa
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where(function($sq) use ($request) {
                    $sq->whereNotNull('promotional_price')
                       ->where('promotional_price', '<=', $request->max_price);
                })->orWhere(function($sq) use ($request) {
                    $sq->whereNull('promotional_price')
                       ->where('price', '<=', $request->max_price);
                });
            });
        }

        // Lọc theo size
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size', $request->size);
            });
        }

        // Lọc theo màu sắc
        if ($request->filled('color')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('color', $request->color);
            });
        }

        // Sắp xếp
        match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->orderByDesc('created_at'),
            default      => $query->orderByDesc('id'),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        
        $availableSizes = \App\Models\ProductVariant::whereHas('product', fn($q) => $q->where('is_active', true))->distinct()->pluck('size')->filter()->values();
        $availableColors = \App\Models\ProductVariant::whereHas('product', fn($q) => $q->where('is_active', true))->distinct()->pluck('color')->filter()->values();

        $wishlistIds = [];
        if (auth()->check() && auth()->user()->customer) {
            $wishlistIds = \App\Models\Wishlist::where('customer_id', auth()->user()->customer->id)
                ->pluck('product_id')->toArray();
        }

        return view('pages.shop', compact('products', 'categories', 'availableSizes', 'availableColors', 'wishlistIds'));
    }

    public function show(int $id): \Illuminate\View\View
    {
        $product = Product::where('is_active', true)
            ->with([
                'category',
                'images',
                'variants',
                'reviews.customer',
            ])
            ->findOrFail($id);

        // Tăng lượt xem
        $product->increment('views');

        // Nhóm biến thể theo màu và size để render selector + đồng bộ tồn kho ở frontend
        $variantsData = $product->variants
            ->map(fn ($variant) => [
                'id'             => $variant->id,
                'color'          => $variant->color,
                'size'           => $variant->size,
                'stock_quantity' => (int) $variant->stock_quantity,
            ])
            ->values();

        $sizes  = $product->variants->pluck('size')->filter()->unique()->values();
        $colors = $product->variants->pluck('color')->filter()->unique()->values();

        // Sản phẩm liên quan (cùng danh mục, loại trừ SP hiện tại)
        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('primaryImage')
            ->take(4)
            ->get();

        $wishlistIds = [];
        if (auth()->check() && auth()->user()->customer) {
            $wishlistIds = \App\Models\Wishlist::where('customer_id', auth()->user()->customer->id)
                ->pluck('product_id')->toArray();
        }

        return view('pages.product-detail', compact('product', 'sizes', 'colors', 'variantsData', 'relatedProducts', 'wishlistIds'));
    }
}
