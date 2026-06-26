<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('sizes')->upsert(
            [
                ['name' => 'S', 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'M', 'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'L', 'sort_order' => 40, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'XL', 'sort_order' => 50, 'created_at' => $now, 'updated_at' => $now],
                ['name' => '2XL', 'sort_order' => 60, 'created_at' => $now, 'updated_at' => $now],
                ['name' => '3XL', 'sort_order' => 70, 'created_at' => $now, 'updated_at' => $now],
            ],
            ['name'],
            ['sort_order', 'updated_at']
        );
    }

    public function down(): void
    {
        DB::table('sizes')
            ->whereIn('name', ['S', 'M', 'L', 'XL', '2XL', '3XL'])
            ->delete();
    }
};
