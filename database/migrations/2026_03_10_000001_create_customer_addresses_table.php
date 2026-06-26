<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            // Người nhận (có thể khác chủ tài khoản)
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);

            // Địa chỉ Viettel Post (lưu cả ID và tên để dùng ngay không cần gọi API lại)
            $table->unsignedInteger('province_id');
            $table->string('province_name');
            $table->unsignedInteger('district_id');
            $table->string('district_name');
            $table->unsignedInteger('ward_id');
            $table->string('ward_name');
            $table->string('detailed_address'); // Số nhà, tên đường...

            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
