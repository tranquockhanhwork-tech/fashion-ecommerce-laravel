<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductDetailGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_displays_color_preview_images_on_gallery_side(): void
    {
        $category = Category::query()->create([
            'name' => 'Ao thun',
            'slug' => 'ao-thun-' . Str::random(6),
            'description' => 'Danh muc test',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Ao thun preview mau',
            'slug' => 'ao-thun-preview-' . Str::random(6),
            'price' => 190000,
            'promotional_price' => null,
            'is_active' => true,
        ]);

        $size = Size::query()->firstOrCreate([
            'name' => 'M',
        ], [
            'sort_order' => 30,
        ]);

        $black = Color::query()->create(['name' => 'Black']);
        $navy = Color::query()->create(['name' => 'Navy']);

        ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $black->id,
            'sku' => 'BLACK-M-' . strtoupper(Str::random(4)),
            'stock_quantity' => 5,
            'price_override' => null,
        ]);

        ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $navy->id,
            'sku' => 'NAVY-M-' . strtoupper(Str::random(4)),
            'stock_quantity' => 5,
            'price_override' => null,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'color_id' => $black->id,
            'image_url' => 'products/black-preview.jpg',
            'alt_text' => 'Black preview image',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'color_id' => $navy->id,
            'image_url' => 'products/navy-preview.jpg',
            'alt_text' => 'Navy preview image',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('shop.show', $product->id));

        $response->assertOk();
        $response->assertSee('data-color-preview="Black"', false);
        $response->assertSee('data-color-preview="Navy"', false);
        $response->assertSee(asset('storage/products/black-preview.jpg'), false);
        $response->assertSee(asset('storage/products/navy-preview.jpg'), false);
    }
}
