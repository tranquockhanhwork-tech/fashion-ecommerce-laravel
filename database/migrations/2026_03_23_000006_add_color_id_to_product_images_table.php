<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (! Schema::hasColumn('product_images', 'color_id')) {
                $table->foreignId('color_id')
                    ->nullable()
                    ->after('product_variant_id')
                    ->constrained('colors')
                    ->nullOnDelete();
            }
        });

        DB::table('product_images as images')
            ->join('product_variants as variants', 'variants.id', '=', 'images.product_variant_id')
            ->whereNull('images.color_id')
            ->whereNotNull('variants.color_id')
            ->update([
                'images.color_id' => DB::raw('variants.color_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'color_id')) {
                $table->dropConstrainedForeignId('color_id');
            }
        });
    }
};
