<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng biến thể sản phẩm (size, màu sắc, mã SKU, tồn kho).
     * Đây là bảng cốt lõi của hệ thống quản lý kho thời trang.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku', 100)->unique(); // Mã quản lý kho duy nhất. VD: AOTHUN-RED-M
            $table->string('size', 10);           // VD: S, M, L, XL, XXL, 28, 30
            $table->string('color', 50);          // VD: Đỏ, Xanh Navy, Đen
            $table->unsignedInteger('stock_quantity')->default(0); // Số lượng tồn kho
            $table->decimal('price_override', 10, 2)->nullable();  // Giá ghi đè (nếu biến thể có giá riêng)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
