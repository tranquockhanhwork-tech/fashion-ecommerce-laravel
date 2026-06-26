<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng sản phẩm (thông tin chung, không bao gồm biến thể).
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique(); // URL SEO-friendly
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);                       // Giá gốc
            $table->decimal('promotional_price', 10, 2)->nullable(); // Giá khuyến mãi (null = không giảm)
            $table->boolean('is_active')->default(true);            // Ẩn/hiện sản phẩm
            $table->unsignedBigInteger('views')->default(0);        // Lượt xem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
