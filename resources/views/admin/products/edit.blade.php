@extends('admin.layouts.app')

@section('title', 'Cập Nhật Sản Phẩm')
@section('page_title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
@php
    $currentImage = $product->primaryImage?->image_url
        ? asset('storage/' . ltrim($product->primaryImage->image_url, '/'))
        : null;
@endphp

<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Chỉnh sửa sản phẩm</h1>
            <p class="mt-1 text-sm text-slate-500">Cập nhật thông tin sản phẩm trong biểu mẫu bên dưới.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
        <div class="mb-2 text-sm font-semibold">Không thể lưu thay đổi</div>
        <ul class="space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="product-edit-form" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        @csrf
        @method('PUT')

        <div class="p-6 lg:p-8">
            <div class="product-edit-top-grid">
                <div class="space-y-4 product-edit-media">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="aspect-square bg-gradient-to-br from-slate-100 via-white to-slate-100">
                            <img
                                id="image-preview"
                                src="{{ $currentImage ?? 'https://placehold.co/480x480/f8fafc/cbd5e1?text=No+Image' }}"
                                alt="{{ $product->name }}"
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
                        <p class="mt-2 text-xs leading-5 text-slate-500">Bỏ trống nếu muốn giữ nguyên ảnh hiện tại.</p>
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
                                value="{{ old('name', $product->name) }}"
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                value="{{ old('price', (int) $product->price) }}"
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
                                value="{{ old('promotional_price', $product->promotional_price !== null ? (int) $product->promotional_price : '') }}"
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
                                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-slate-300 text-[#C5A572] focus:ring-[#C5A572]"
                                >
                                <span class="font-medium">Hiển thị trên website</span>
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
                        >{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#C5A572] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#b28f5e]">
                            Lưu thay đổi
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
const editForm = document.getElementById('product-edit-form');
const imageInput = document.getElementById('image_url');
const imagePreview = document.getElementById('image-preview');
const currentPreviewSrc = imagePreview?.getAttribute('src') || '';

editForm?.addEventListener('submit', function () {
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
        imagePreview.src = currentPreviewSrc;
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    imagePreview.src = objectUrl;
});
</script>
@endpush

@push('styles')
<style>
.product-edit-top-grid {
    display: grid;
    grid-template-columns: 160px minmax(0, 1fr);
    gap: 1.5rem;
    align-items: start;
}

.product-edit-media {
    width: 160px;
}

@media (max-width: 560px) {
    .product-edit-top-grid {
        grid-template-columns: 1fr;
    }

    .product-edit-media {
        width: 100%;
        max-width: 180px;
    }
}
</style>
@endpush
