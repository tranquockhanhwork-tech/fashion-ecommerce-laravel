<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
                $q->whereSizeName($request->size);
            });
        }

        // Lọc theo màu sắc
        if ($request->filled('color')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->whereColorName($request->color);
            });
        }

        // Sắp xếp
        match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->orderByDesc('created_at'),
            default      => $query->orderByDesc('id'),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        $availableSizes = Size::query()
            ->whereHas('variants.product', fn ($query) => $query->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->values();

        $availableColors = Color::query()
            ->whereHas('variants.product', fn ($query) => $query->where('is_active', true))
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->values();

        $wishlistIds = [];
        if (auth()->check() && auth()->user()->customer) {
            $wishlistIds = Wishlist::where('customer_id', auth()->user()->customer->id)
                ->pluck('product_id')->toArray();
        }

        return view('pages.shop', compact('products', 'categories', 'availableSizes', 'availableColors', 'wishlistIds'));
    }

    public function show(int $id): \Illuminate\View\View
    {
        $product = Product::where('is_active', true)
            ->with([
                'category',
                'images' => fn ($query) => $query->with('colorOption'),
                'variants' => fn ($query) => $query->withOptionRelations(),
                'reviews.customer',
            ])
            ->findOrFail($id);

        // Tăng lượt xem
        $product->increment('views');

        $useOptionTables = ProductVariant::optionsAreAvailable();

        $product->setRelation(
            'variants',
            $product->variants
                ->sortBy(function ($variant) use ($useOptionTables) {
                    $sizeOrder = $useOptionTables
                        ? ($variant->sizeOption?->sort_order ?? 9999)
                        : 9999;

                    return sprintf(
                        '%s|%05d|%s',
                        Str::lower((string) $variant->color),
                        $sizeOrder,
                        Str::lower((string) $variant->size)
                    );
                })
                ->values()
        );

        // Nhóm biến thể theo màu và size để render selector + đồng bộ tồn kho ở frontend
        $variantsData = $product->variants
            ->map(fn ($variant) => [
                'id'             => $variant->id,
                'color'          => $variant->color,
                'size'           => $variant->size,
                'stock_quantity' => (int) $variant->stock_quantity,
            ])
            ->values();

        $sizes = $product->variants
            ->filter(fn ($variant) => trim((string) $variant->size) !== '')
            ->sortBy(function ($variant) use ($useOptionTables) {
                $sizeOrder = $useOptionTables
                    ? ($variant->sizeOption?->sort_order ?? 9999)
                    : 9999;

                return sprintf('%05d|%s', $sizeOrder, Str::lower((string) $variant->size));
            })
            ->pluck('size')
            ->unique()
            ->values();

        $colors = $product->variants
            ->filter(fn ($variant) => trim((string) $variant->color) !== '')
            ->sortBy(fn ($variant) => Str::lower((string) $variant->color))
            ->pluck('color')
            ->unique()
            ->values();

        $genericGalleryImages = $product->images
            ->whereNull('color_id')
            ->values();

        $galleryDefault = $this->buildGalleryPayload(
            $product,
            $genericGalleryImages->isNotEmpty() ? $genericGalleryImages : $product->images
        );

        $galleryByColor = $colors
            ->mapWithKeys(function (string $colorName) use ($product, $genericGalleryImages, $galleryDefault) {
                $specificImages = $product->images
                    ->filter(fn ($image) => $image->colorOption?->name === $colorName)
                    ->values();

                $galleryImages = $specificImages->isNotEmpty()
                    ? $specificImages->concat(
                        $genericGalleryImages->reject(
                            fn ($image) => $specificImages->contains('id', $image->id)
                        )
                    )->values()
                    : $genericGalleryImages;

                if ($galleryImages->isEmpty()) {
                    return [$colorName => $galleryDefault];
                }

                return [$colorName => $this->buildGalleryPayload($product, $galleryImages)];
            })
            ->all();

        $colorPreviewImages = $colors
            ->map(function (string $colorName) use ($galleryByColor, $galleryDefault, $product) {
                $gallery = $galleryByColor[$colorName] ?? $galleryDefault;
                $preview = collect($gallery)
                    ->first(fn ($image) => trim((string) ($image['src'] ?? '')) !== '');

                return [
                    'color' => $colorName,
                    'src' => $preview['src'] ?? $product->thumbnail,
                    'alt' => $preview['alt'] ?? ($product->name . ' - ' . $colorName),
                ];
            })
            ->values();

        // Sản phẩm liên quan (cùng danh mục, loại trừ SP hiện tại)
        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('primaryImage')
            ->take(4)
            ->get();

        $wishlistIds = [];
        if (auth()->check() && auth()->user()->customer) {
            $wishlistIds = Wishlist::where('customer_id', auth()->user()->customer->id)
                ->pluck('product_id')->toArray();
        }

        return view('pages.product-detail', compact(
            'product',
            'sizes',
            'colors',
            'variantsData',
            'galleryDefault',
            'galleryByColor',
            'colorPreviewImages',
            'relatedProducts',
            'wishlistIds'
        ));
    }

    protected function buildGalleryPayload(Product $product, Collection $images): array
    {
        $gallery = $images
            ->filter(fn ($image) => trim((string) $image->image_url) !== '')
            ->map(fn ($image) => [
                'id' => $image->id,
                'src' => $image->resolved_url,
                'alt' => trim((string) $image->alt_text) !== ''
                    ? $image->alt_text
                    : $product->name,
            ])
            ->values()
            ->all();

        if ($gallery !== []) {
            return $gallery;
        }

        return [[
            'id' => 0,
            'src' => $product->thumbnail,
            'alt' => $product->name,
        ]];
    }
}
