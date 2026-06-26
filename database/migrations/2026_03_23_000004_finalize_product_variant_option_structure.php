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
            $table->dropForeign(['size_id']);
            $table->dropForeign(['color_id']);
        });

        DB::statement('ALTER TABLE `product_variants` MODIFY `size_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `product_variants` MODIFY `color_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('size_id')->references('id')->on('sizes')->restrictOnDelete();
            $table->foreign('color_id')->references('id')->on('colors')->restrictOnDelete();
            $table->unique(
                ['product_id', 'size_id', 'color_id'],
                'product_variants_product_size_color_unique'
            );
            $table->dropIndex('product_variants_product_size_color_index');
            $table->dropColumn(['size', 'color']);
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('size', 10)->nullable()->after('sku');
            $table->string('color', 50)->nullable()->after('size');
        });

        DB::statement('
            UPDATE `product_variants` pv
            LEFT JOIN `sizes` s ON s.`id` = pv.`size_id`
            LEFT JOIN `colors` c ON c.`id` = pv.`color_id`
            SET pv.`size` = s.`name`, pv.`color` = c.`name`
        ');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('product_variants_product_size_color_unique');
            $table->dropForeign(['size_id']);
            $table->dropForeign(['color_id']);
        });

        DB::statement('ALTER TABLE `product_variants` MODIFY `size_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `product_variants` MODIFY `color_id` BIGINT UNSIGNED NULL');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('size_id')->references('id')->on('sizes')->nullOnDelete();
            $table->foreign('color_id')->references('id')->on('colors')->nullOnDelete();
            $table->index(
                ['product_id', 'size_id', 'color_id'],
                'product_variants_product_size_color_index'
            );
        });
    }
};
