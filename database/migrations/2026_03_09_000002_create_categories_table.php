<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng danh mục sản phẩm, hỗ trợ phân cấp cha - con.
     * VD: "Thời trang Nam" (cha) -> "Áo" (con) -> "Áo thun" (cháu)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete(); // Nếu danh mục cha bị xóa, con sẽ thành danh mục gốc
            $table->string('name');
            $table->string('slug')->unique(); // Dùng cho URL SEO-friendly
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
