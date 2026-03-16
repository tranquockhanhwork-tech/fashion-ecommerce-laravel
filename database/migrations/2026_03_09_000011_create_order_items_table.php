<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng chi tiết đơn hàng.
     * Lưu lại giá tại thời điểm mua để tránh sai lệch lịch sử hóa đơn.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->restrictOnDelete();
            // restrictOnDelete: không cho xóa biến thể nếu đã có trong đơn hàng
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2); // Giá 1 sản phẩm tại thời điểm mua
            $table->decimal('subtotal', 12, 2);   // = unit_price * quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
