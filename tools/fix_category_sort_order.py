from pathlib import Path


form_path = Path("resources/views/admin/categories/_form.blade.php")
form_text = form_path.read_text(encoding="utf-8")
old_block = """
    <div>
        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
        <input
            type="number"
            id="sort_order"
            name="sort_order"
            value="{{ old('sort_order', $category->sort_order ?? 0) }}"
            min="0"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border"
        >
    </div>
"""
form_path.write_text(form_text.replace(old_block, ""), encoding="utf-8")

migration_path = Path("database/migrations/2026_03_18_000001_drop_sort_order_from_categories_table.php")
migration_path.write_text(
    """<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('categories', 'sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('categories', 'sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedTinyInteger('sort_order')->default(0)->after('description');
            });
        }
    }
};
""",
    encoding="utf-8",
)
