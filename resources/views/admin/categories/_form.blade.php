@if($errors->any())
<div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-100">
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục *</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            required
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border"
        >
    </div>

    <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục cha</label>
        <select
            id="parent_id"
            name="parent_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border bg-white"
        >
            <option value="">-- Danh mục gốc --</option>
            @foreach($parentCategories as $parentCategory)
                <option value="{{ $parentCategory->id }}" {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parentCategory->id ? 'selected' : '' }}>
                    {{ $parentCategory->name }}
                </option>
            @endforeach
        </select>
    </div>

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

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border"
        >{{ old('description', $category->description ?? '') }}</textarea>
    </div>
</div>

<div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
    <a href="{{ route('admin.categories.index') }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Hủy bỏ</a>
    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#C5A572] rounded-lg hover:bg-[#b09265] transition-colors shadow-sm">
        {{ isset($category) ? 'Cập nhật danh mục' : 'Lưu danh mục' }}
    </button>
</div>
