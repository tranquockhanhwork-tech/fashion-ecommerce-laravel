<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng đánh giá sản phẩm từ khách hàng.
     * Chỉ khách hàng đã mua hàng mới được đánh giá (ràng buộc ở tầng business logic).
     */
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1 - 5 sao
            $table->string('title')->nullable();   // Tiêu đề review
            $table->text('comment')->nullable();   // Nội dung review
            $table->boolean('is_approved')->default(false); // Admin duyệt trước khi hiện
            $table->timestamps();

            // Mỗi khách chỉ review 1 lần cho 1 sản phẩm
            $table->unique(['product_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
