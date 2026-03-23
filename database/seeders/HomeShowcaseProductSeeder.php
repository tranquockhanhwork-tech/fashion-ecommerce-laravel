<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class HomeShowcaseProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::query()
            ->where('slug', 'ao-khoac')
            ->first();

        if (! $category) {
            return;
        }

        $product = Product::query()->updateOrCreate(
            ['slug' => 'premium-streetwear-set'],
            [
                'category_id' => $category->id,
                'name' => 'Premium Streetwear Set',
                'description' => 'Set streetwear cao cap voi phom dang hien dai, layer ao khoac ngoai va chat lieu day dan. Mau sac trung tinh de phoi do hang ngay hoac chup lookbook.',
                'price' => 1590000,
                'promotional_price' => 1290000,
                'is_active' => true,
            ]
        );

        $variants = [
            ['sku' => 'PSS-BLACK-M', 'size' => 'M', 'color' => 'Den Midnight', 'stock_quantity' => 12],
            ['sku' => 'PSS-BLACK-L', 'size' => 'L', 'color' => 'Den Midnight', 'stock_quantity' => 10],
            ['sku' => 'PSS-BLACK-XL', 'size' => 'XL', 'color' => 'Den Midnight', 'stock_quantity' => 6],
            ['sku' => 'PSS-MOCHA-M', 'size' => 'M', 'color' => 'Mocha Gold', 'stock_quantity' => 8],
            ['sku' => 'PSS-MOCHA-L', 'size' => 'L', 'color' => 'Mocha Gold', 'stock_quantity' => 7],
        ];

        foreach ($variants as $variant) {
            ProductVariant::query()->updateOrCreate(
                ['sku' => $variant['sku']],
                array_merge($variant, ['product_id' => $product->id])
            );
        }

        ProductImage::query()->where('product_id', $product->id)->delete();

        $images = [
            [
                'image_url' => 'https://images.unsplash.com/photo-1617127365659-c47fa864d8bc?w=1200&q=80',
                'alt_text' => 'Premium Streetwear Set Den Midnight look',
                'is_primary' => true,
                'sort_order' => 0,
            ],
        ];

        foreach ($images as $image) {
            ProductImage::query()->create(array_merge($image, [
                'product_id' => $product->id,
                'product_variant_id' => null,
            ]));
        }
    }
}
