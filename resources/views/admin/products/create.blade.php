@extends('admin.layouts.app')

@section('title', 'Thêm Sản Phẩm Mới')
@section('page_title', 'Thêm Sản Phẩm Mới')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Thêm sản phẩm mới</h1>
            <p class="mt-1 text-sm text-slate-500">Tạo sản phẩm mới với giao diện đồng bộ cùng trang chỉnh sửa.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
        <div class="mb-2 text-sm font-semibold">Không thể tạo sản phẩm</div>
        <ul class="space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="product-create-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        @csrf

        <div class="p-6 lg:p-8">
            <div class="product-form-top-grid">
                <div class="space-y-4 product-form-media">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="aspect-square bg-gradient-to-br from-slate-100 via-white to-slate-100">
                            <img
                                id="image-preview"
                                src="https://placehold.co/480x480/f8fafc/cbd5e1?text=Preview"
                                alt="Xem trước ảnh sản phẩm"
                                class="h-full w-full object-cover"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="image_url" class="mb-2 block text-sm font-semibold text-slate-700">Ảnh đại diện</label>
                        <input
                            type="file"
                            id="image_url"
                            name="image_url"
                            accept="image/*"
                            data-optional-file
                            class="w-full rounded-2xl border border-slate-200 bg-white p-2 text-xs text-slate-500 shadow-sm file:mr-2 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-xs file:font-medium file:text-white hover:file:bg-slate-800"
                        >
                        <p class="mt-2 text-xs leading-5 text-slate-500">Có thể để trống và cập nhật ảnh sau nếu cần.</p>
                    </div>

                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50/80 p-4">
                        <h2 class="text-sm font-semibold text-slate-900">Sau khi tạo xong</h2>
                        <p class="mt-1 text-xs leading-5 text-slate-500">Bạn có thể vào trang chỉnh sửa để cập nhật thêm tồn kho, biến thể và nội dung chi tiết hơn.</p>
                    </div>
                </div>

                <div class="space-y-5 min-w-0">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Tên sản phẩm</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                placeholder="Nhập tên sản phẩm"
                            >
                        </div>

                        <div>
                            <label for="category_id" class="mb-2 block text-sm font-semibold text-slate-700">Danh mục</label>
                            <select
                                id="category_id"
                                name="category_id"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                            >
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="price" class="mb-2 block text-sm font-semibold text-slate-700">Giá bán (VND)</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                value="{{ old('price') }}"
                                required
                                min="0"
                                step="1"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                placeholder="150000"
                            >
                        </div>

                        <div>
                            <label for="promotional_price" class="mb-2 block text-sm font-semibold text-slate-700">Giá khuyến mãi (VND)</label>
                            <input
                                type="number"
                                id="promotional_price"
                                name="promotional_price"
                                value="{{ old('promotional_price') }}"
                                min="0"
                                step="1"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                placeholder="Bỏ trống nếu không giảm"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Trạng thái hiển thị</label>
                            <label class="flex items-center gap-3 px-1 py-1 text-sm text-slate-700">
                                <input
                                    id="is_active"
                                    name="is_active"
                                    type="checkbox"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-slate-300 text-[#C5A572] focus:ring-[#C5A572]"
                                >
                                <span class="font-medium">Hiển thị trên website ngay sau khi lưu</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Mô tả sản phẩm</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="8"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                            placeholder="Nhập mô tả sản phẩm"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Gợi ý thiết lập nhanh</h2>
                                <p class="text-xs text-slate-500">Điền trước tên, danh mục, giá bán và ảnh đại diện để tạo sản phẩm nhanh gọn.</p>
                            </div>
                            <div class="text-xs font-medium text-slate-500">
                                Biến thể và tồn kho: <span class="text-slate-900">thêm sau ở trang chỉnh sửa</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#C5A572] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#b28f5e]">
                            Tạo sản phẩm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const createForm = document.getElementById('product-create-form');
const imageInput = document.getElementById('image_url');
const imagePreview = document.getElementById('image-preview');
const defaultPreviewSrc = imagePreview?.getAttribute('src') || '';

createForm?.addEventListener('submit', function () {
    document.querySelectorAll('[data-optional-file]').forEach((input) => {
        if (!input.files || input.files.length === 0) {
            input.disabled = true;
        }
    });
});

imageInput?.addEventListener('change', function () {
    const [file] = this.files || [];

    if (!imagePreview) {
        return;
    }

    if (!file) {
        imagePreview.src = defaultPreviewSrc;
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    imagePreview.src = objectUrl;
});
</script>
@endpush

@push('styles')
<style>
.product-form-top-grid {
    display: grid;
    grid-template-columns: 160px minmax(0, 1fr);
    gap: 1.5rem;
    align-items: start;
}

.product-form-media {
    width: 160px;
}

@media (max-width: 560px) {
    .product-form-top-grid {
        grid-template-columns: 1fr;
    }

    .product-form-media {
        width: 100%;
        max-width: 180px;
    }
}
</style>
@endpush
