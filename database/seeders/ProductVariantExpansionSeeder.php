<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use Carbon\Carbon;
use Database\Seeders\Concerns\ResolvesVariantOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantExpansionSeeder extends Seeder
{
    use ResolvesVariantOptions;

    /**
     * Seed additional product variants for demo/testing.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $cleanupSizeIds = $this->ensureSizeIds(['28', '29', '30', '31'])
            ->values()
            ->all();

        ProductVariant::query()
            ->where('product_id', 8)
            ->whereIn('size_id', $cleanupSizeIds)
            ->delete();

        $variants = $this->prepareVariantPayloads([
            ['product_id' => 1, 'sku' => 'AOT-BASIC-TRANG-S', 'size' => 'S', 'color' => 'Trắng', 'stock_quantity' => 24],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-TRANG-XL', 'size' => 'XL', 'color' => 'Trắng', 'stock_quantity' => 10],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-DEN-S', 'size' => 'S', 'color' => 'Đen', 'stock_quantity' => 14],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-DEN-L', 'size' => 'L', 'color' => 'Đen', 'stock_quantity' => 0],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-DEN-XL', 'size' => 'XL', 'color' => 'Đen', 'stock_quantity' => 7],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-BE-M', 'size' => 'M', 'color' => 'Be', 'stock_quantity' => 16],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-BE-L', 'size' => 'L', 'color' => 'Be', 'stock_quantity' => 6],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-BE-XL', 'size' => 'XL', 'color' => 'Be', 'stock_quantity' => 0],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-XANH-M', 'size' => 'M', 'color' => 'Xanh Navy', 'stock_quantity' => 8],
            ['product_id' => 1, 'sku' => 'AOT-BASIC-XANH-L', 'size' => 'L', 'color' => 'Xanh Navy', 'stock_quantity' => 4],

            ['product_id' => 2, 'sku' => 'POLO-HTIET-TRANG-M', 'size' => 'M', 'color' => 'Trắng Kem', 'stock_quantity' => 12],
            ['product_id' => 2, 'sku' => 'POLO-HTIET-TRANG-L', 'size' => 'L', 'color' => 'Trắng Kem', 'stock_quantity' => 5],
            ['product_id' => 2, 'sku' => 'POLO-HTIET-XANH-M', 'size' => 'M', 'color' => 'Xanh Rêu', 'stock_quantity' => 9],
            ['product_id' => 2, 'sku' => 'POLO-HTIET-XANH-XL', 'size' => 'XL', 'color' => 'Xanh Rêu', 'stock_quantity' => 0],
            ['product_id' => 2, 'sku' => 'POLO-HTIET-DEN-L', 'size' => 'L', 'color' => 'Đen', 'stock_quantity' => 11],

            ['product_id' => 3, 'sku' => 'QJN-SUONG-S', 'size' => 'S', 'color' => 'Xanh Nhạt', 'stock_quantity' => 9],
            ['product_id' => 3, 'sku' => 'QJN-SUONG-XL', 'size' => 'XL', 'color' => 'Xanh Nhạt', 'stock_quantity' => 3],
            ['product_id' => 3, 'sku' => 'QJN-SUONG-DAM-M', 'size' => 'M', 'color' => 'Xanh Đậm', 'stock_quantity' => 6],
            ['product_id' => 3, 'sku' => 'QJN-SUONG-DAM-L', 'size' => 'L', 'color' => 'Xanh Đậm', 'stock_quantity' => 0],
            ['product_id' => 3, 'sku' => 'QJN-SUONG-DEN-L', 'size' => 'L', 'color' => 'Đen Wash', 'stock_quantity' => 8],

            ['product_id' => 4, 'sku' => 'VAY-LUA-DO-M', 'size' => 'M', 'color' => 'Đỏ Đô', 'stock_quantity' => 7],
            ['product_id' => 4, 'sku' => 'VAY-LUA-DO-L', 'size' => 'L', 'color' => 'Đỏ Đô', 'stock_quantity' => 2],
            ['product_id' => 4, 'sku' => 'VAY-LUA-KEM-S', 'size' => 'S', 'color' => 'Kem', 'stock_quantity' => 5],
            ['product_id' => 4, 'sku' => 'VAY-LUA-KEM-M', 'size' => 'M', 'color' => 'Kem', 'stock_quantity' => 0],
            ['product_id' => 4, 'sku' => 'VAY-LUA-DEN-M', 'size' => 'M', 'color' => 'Đen', 'stock_quantity' => 4],

            ['product_id' => 5, 'sku' => 'BABYDOLL-HONG-S', 'size' => 'S', 'color' => 'Hồng Phấn', 'stock_quantity' => 9],
            ['product_id' => 5, 'sku' => 'BABYDOLL-HONG-M', 'size' => 'M', 'color' => 'Hồng Phấn', 'stock_quantity' => 5],
            ['product_id' => 5, 'sku' => 'BABYDOLL-TRANG-S', 'size' => 'S', 'color' => 'Trắng', 'stock_quantity' => 6],
            ['product_id' => 5, 'sku' => 'BABYDOLL-VANG-M', 'size' => 'M', 'color' => 'Vàng Bơ', 'stock_quantity' => 4],

            ['product_id' => 6, 'sku' => 'KHOAC-GIO-DEN-M', 'size' => 'M', 'color' => 'Đen', 'stock_quantity' => 13],
            ['product_id' => 6, 'sku' => 'KHOAC-GIO-DEN-L', 'size' => 'L', 'color' => 'Đen', 'stock_quantity' => 10],
            ['product_id' => 6, 'sku' => 'KHOAC-GIO-XAM-XL', 'size' => 'XL', 'color' => 'Xám Khói', 'stock_quantity' => 5],
            ['product_id' => 6, 'sku' => 'KHOAC-GIO-REU-L', 'size' => 'L', 'color' => 'Xanh Rêu', 'stock_quantity' => 0],

            ['product_id' => 7, 'sku' => 'QTAY-LUA-DEN-M', 'size' => 'M', 'color' => 'Đen', 'stock_quantity' => 12],
            ['product_id' => 7, 'sku' => 'QTAY-LUA-DEN-L', 'size' => 'L', 'color' => 'Đen', 'stock_quantity' => 7],
            ['product_id' => 7, 'sku' => 'QTAY-LUA-NAU-M', 'size' => 'M', 'color' => 'Nâu Mocha', 'stock_quantity' => 6],

            ['product_id' => 8, 'sku' => 'QTAY-DUNG-DEN-S', 'size' => 'S', 'color' => 'Đen', 'stock_quantity' => 5],
            ['product_id' => 8, 'sku' => 'QTAY-DUNG-DEN-M', 'size' => 'M', 'color' => 'Đen', 'stock_quantity' => 3],
            ['product_id' => 8, 'sku' => 'QTAY-DUNG-BE-L', 'size' => 'L', 'color' => 'Be', 'stock_quantity' => 8],
            ['product_id' => 8, 'sku' => 'QTAY-DUNG-XAM-XL', 'size' => 'XL', 'color' => 'Xám', 'stock_quantity' => 0],

            ['product_id' => 9, 'sku' => 'AOT-DTAY-TRANG-M', 'size' => 'M', 'color' => 'Trắng', 'stock_quantity' => 11],
            ['product_id' => 9, 'sku' => 'AOT-DTAY-TRANG-L', 'size' => 'L', 'color' => 'Trắng', 'stock_quantity' => 7],
            ['product_id' => 9, 'sku' => 'AOT-DTAY-HONG-M', 'size' => 'M', 'color' => 'Hồng Đất', 'stock_quantity' => 5],
            ['product_id' => 9, 'sku' => 'AOT-DTAY-XANH-S', 'size' => 'S', 'color' => 'Xanh Olive', 'stock_quantity' => 0],

            ['product_id' => 10, 'sku' => 'CV-A-DEN-S', 'size' => 'S', 'color' => 'Đen', 'stock_quantity' => 9],
            ['product_id' => 10, 'sku' => 'CV-A-DEN-L', 'size' => 'L', 'color' => 'Đen', 'stock_quantity' => 1],
            ['product_id' => 10, 'sku' => 'CV-A-NAU-M', 'size' => 'M', 'color' => 'Nâu', 'stock_quantity' => 6],
            ['product_id' => 10, 'sku' => 'CV-A-KEM-M', 'size' => 'M', 'color' => 'Kem', 'stock_quantity' => 0],
        ]);

        $payload = collect($variants)
            ->map(fn (array $variant) => array_merge($variant, [
                'price_override' => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]))
            ->all();

        DB::table('product_variants')->upsert(
            $payload,
            ['sku'],
            ['product_id', 'size_id', 'color_id', 'stock_quantity', 'price_override', 'updated_at']
        );
    }
}
