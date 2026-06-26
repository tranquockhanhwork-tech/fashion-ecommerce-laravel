<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng hình ảnh sản phẩm.
     * Mỗi sản phẩm có thể có nhiều ảnh; 1 ảnh là ảnh đại diện chính.
     * Có thể tùy chọn liên kết ảnh với biến thể màu sắc cụ thể.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')
                  ->nullable()
                  ->constrained('product_variants')
                  ->nullOnDelete(); // Ảnh có thể gắn với 1 biến thể màu cụ thể
            $table->string('image_url');
            $table->string('alt_text')->nullable();            // Mô tả ảnh (tốt cho SEO)
            $table->boolean('is_primary')->default(false);     // Ảnh đại diện chính
            $table->unsignedTinyInteger('sort_order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
