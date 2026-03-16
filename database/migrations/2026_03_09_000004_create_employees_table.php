<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng thông tin chi tiết nhân viên.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone', 20)->nullable();
            $table->string('position', 100)->nullable(); // VD: Thu ngân, Bán hàng, Kho
            $table->decimal('salary', 12, 2)->nullable(); // Lương
            $table->date('hired_at')->nullable();         // Ngày vào làm
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
