<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng mã giảm giá (Coupon / Voucher).
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();        // Mã nhập vào. VD: SUMMER20
            $table->string('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']); // % hoặc tiền cố định
            $table->decimal('discount_value', 10, 2);   // VD: 20 (nghĩa là 20% hoặc 20,000đ)
            $table->decimal('min_order_amount', 10, 2)->default(0); // Đơn tối thiểu để áp dụng
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Giới hạn số tiền được giảm tối đa
            $table->unsignedInteger('usage_limit')->nullable();      // Giới hạn số lần dùng toàn hệ thống
            $table->unsignedInteger('used_count')->default(0);       // Đã dùng bao nhiêu lần
            $table->dateTime('starts_at')->nullable();  // Ngày bắt đầu hiệu lực
            $table->dateTime('expires_at')->nullable(); // Ngày hết hạn
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
