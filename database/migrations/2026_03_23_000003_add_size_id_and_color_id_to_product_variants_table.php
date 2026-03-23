<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('size_id')
                ->nullable()
                ->after('product_id')
                ->constrained('sizes')
                ->nullOnDelete();

            $table->foreignId('color_id')
                ->nullable()
                ->after('size_id')
                ->constrained('colors')
                ->nullOnDelete();

            $table->index(
                ['product_id', 'size_id', 'color_id'],
                'product_variants_product_size_color_index'
            );
        });

        if (! Schema::hasColumn('product_variants', 'size') || ! Schema::hasColumn('product_variants', 'color')) {
            return;
        }

        $now = now();

        $sizes = DB::table('product_variants')
            ->select('size')
            ->whereNotNull('size')
            ->where('size', '<>', '')
            ->distinct()
            ->pluck('size');

        $colors = DB::table('product_variants')
            ->select('color')
            ->whereNotNull('color')
            ->where('color', '<>', '')
            ->distinct()
            ->pluck('color');

        DB::table('sizes')->insertOrIgnore(
            $sizes->map(fn (string $size) => [
                'name' => $size,
                'sort_order' => $this->resolveSizeSortOrder($size),
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );

        DB::table('colors')->insertOrIgnore(
            $colors->map(fn (string $color) => [
                'name' => $color,
                'hex_code' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );

        $sizeIds = DB::table('sizes')->pluck('id', 'name');
        $colorIds = DB::table('colors')->pluck('id', 'name');

        DB::table('product_variants')
            ->select('id', 'size', 'color')
            ->orderBy('id')
            ->chunkById(100, function ($variants) use ($sizeIds, $colorIds) {
                foreach ($variants as $variant) {
                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update([
                            'size_id' => $variant->size ? $sizeIds[$variant->size] ?? null : null,
                            'color_id' => $variant->color ? $colorIds[$variant->color] ?? null : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_product_size_color_index');
            $table->dropForeign(['size_id']);
            $table->dropForeign(['color_id']);
            $table->dropColumn(['size_id', 'color_id']);
        });
    }

    private function resolveSizeSortOrder(string $size): int
    {
        $map = [
            'XS' => 10,
            'S' => 20,
            'M' => 30,
            'L' => 40,
            'XL' => 50,
            'XXL' => 60,
            'XXXL' => 70,
        ];

        $normalized = strtoupper(trim($size));

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (is_numeric($normalized)) {
            return 100 + (int) $normalized;
        }

        return 999;
    }
};
