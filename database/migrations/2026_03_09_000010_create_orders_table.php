<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng đơn hàng.
     * Tách payment_status và status để quản lý riêng biệt
     * tình trạng thanh toán và tình trạng vận chuyển.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            // Thông tin giao hàng (lưu snapshot lúc đặt hàng, không liên kết địa chỉ động)
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->text('shipping_address');

            // Tài chính
            $table->decimal('subtotal', 12, 2);          // Tổng tiền hàng
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0); // Tiền giảm từ coupon
            $table->decimal('total_amount', 12, 2);      // = subtotal + shipping_fee - discount_amount

            // Trạng thái vận chuyển / xử lý đơn
            $table->enum('status', [
                'pending',    // Chờ xác nhận
                'processing', // Đang chuẩn bị hàng
                'shipped',    // Đã giao cho vận chuyển
                'delivered',  // Đã giao tới khách
                'completed',  // Hoàn tất (khách xác nhận)
                'cancelled',  // Đã hủy
            ])->default('pending');

            // Trạng thái thanh toán (tách riêng khỏi trạng thái đơn)
            $table->enum('payment_status', [
                'unpaid',    // Chưa thanh toán
                'paid',      // Đã thanh toán
                'refunded',  // Đã hoàn tiền
            ])->default('unpaid');

            $table->string('payment_method', 50)->default('COD'); // COD, Bank Transfer, Momo, VNPay...
            $table->string('transaction_id')->nullable(); // Mã giao dịch từ cổng thanh toán

            // Vận chuyển
            $table->string('shipping_provider', 50)->nullable(); // Đơn vị vận chuyển (VD: Viettel Post)
            $table->string('tracking_number')->nullable(); // Mã vận đơn

            $table->text('note')->nullable(); // Ghi chú của khách
            $table->timestamps(); // created_at = ngày đặt hàng, updated_at = lần cập nhật gần nhất
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
