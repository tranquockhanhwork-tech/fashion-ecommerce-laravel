@extends('admin.layouts.app')

@section('title', 'Cập Nhật Sản Phẩm')
@section('page_title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
@php
    $currentImage = $product->primaryImage?->image_url
        ? asset('storage/' . ltrim($product->primaryImage->image_url, '/'))
        : null;
    $variantRows = old('variants', [
        ['size' => '', 'color' => '', 'stock_quantity' => '', 'sku' => ''],
    ]);
    $nextVariantIndex = collect($variantRows)->keys()->map(fn ($key) => (int) $key)->max() + 1;
    $colorImageRows = old('color_images', [
        ['color' => '', 'alt_text' => ''],
    ]);
    $nextColorImageIndex = collect($colorImageRows)->keys()->map(fn ($key) => (int) $key)->max() + 1;
    $existingColorImages = $product->images
        ->whereNotNull('color_id')
        ->values();
    $selectedRemovalImageIds = collect(old('remove_color_image_ids', []))
        ->map(fn ($imageId) => (int) $imageId)
        ->all();
    $legacyRestockEntries = collect(old('restock_quantities', []))
        ->filter(fn ($quantity) => $quantity !== null && $quantity !== '' && (int) $quantity > 0)
        ->map(fn ($quantity, $variantId) => [
            'variant_id' => (int) $variantId,
            'quantity' => (int) $quantity,
        ])
        ->values()
        ->all();
    $restockEntries = old('restock_entries', $legacyRestockEntries);
    if (! is_array($restockEntries) || $restockEntries === []) {
        $restockEntries = [
            ['variant_id' => '', 'quantity' => ''],
        ];
    }
    $nextRestockIndex = collect($restockEntries)->keys()->map(fn ($key) => (int) $key)->max() + 1;
    $restockVariantOptions = $product->variants
        ->map(function ($variant) {
            $variantLabel = trim((string) $variant->variant_label);
            $searchLabel = $variantLabel !== ''
                ? $variantLabel
                : ('Biến thể #' . $variant->id);

            if (trim((string) $variant->sku) !== '') {
                $searchLabel .= ' - SKU: ' . $variant->sku;
            }

            return [
                'id' => $variant->id,
                'label' => $variantLabel !== ''
                    ? $variantLabel
                    : ('Biến thể #' . $variant->id),
                'sku' => (string) $variant->sku,
                'stock_quantity' => (int) $variant->stock_quantity,
                'search_label' => $searchLabel,
            ];
        })
        ->values();
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

                    <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Thêm biến thể mới</h2>
                                <p class="text-xs text-slate-500">Thêm size, màu sắc và số lượng cho các biến thể chưa có. Giá trị mới sẽ được tự lưu vào bảng size hoặc màu nếu cần.</p>
                            </div>
                            <button
                                type="button"
                                data-add-variant-row
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >
                                + Thêm biến thể
                            </button>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-100 text-slate-600">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold">Size</th>
                                            <th class="px-4 py-3 text-left font-semibold">Màu sắc</th>
                                            <th class="px-4 py-3 text-center font-semibold">Số lượng</th>
                                            <th class="px-4 py-3 text-left font-semibold">SKU</th>
                                            <th class="px-4 py-3 text-center font-semibold">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variant-rows" data-next-variant-index="{{ $nextVariantIndex }}">
                                        @foreach($variantRows as $index => $variantRow)
                                        <tr class="variant-row border-b border-slate-100 align-top last:border-0">
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    name="variants[{{ $index }}][size]"
                                                    value="{{ $variantRow['size'] ?? '' }}"
                                                    list="variant-size-suggestions"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                    placeholder="VD: S, M, L"
                                                >
                                                @error("variants.$index.size")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    name="variants[{{ $index }}][color]"
                                                    value="{{ $variantRow['color'] ?? '' }}"
                                                    list="variant-color-suggestions"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                    placeholder="VD: Đen, Trắng"
                                                >
                                                @error("variants.$index.color")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="number"
                                                    name="variants[{{ $index }}][stock_quantity]"
                                                    value="{{ $variantRow['stock_quantity'] ?? '' }}"
                                                    min="0"
                                                    step="1"
                                                    class="mx-auto block w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-center text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                    placeholder="0"
                                                >
                                                @error("variants.$index.stock_quantity")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    name="variants[{{ $index }}][sku]"
                                                    value="{{ $variantRow['sku'] ?? '' }}"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                    placeholder="Để trống để tự tạo"
                                                >
                                                @error("variants.$index.sku")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    type="button"
                                                    data-remove-variant-row
                                                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                                                >
                                                    Xóa
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <datalist id="variant-size-suggestions">
                            @foreach($sizeOptions as $sizeOption)
                                <option value="{{ $sizeOption }}"></option>
                            @endforeach
                        </datalist>

                        <datalist id="variant-color-suggestions">
                            @foreach($colorOptions as $colorOption)
                                <option value="{{ $colorOption }}"></option>
                            @endforeach
                        </datalist>

                        <p class="mt-3 text-xs text-slate-500">Các dòng trống sẽ được bỏ qua khi lưu. Biến thể trùng size và màu với biến thể hiện có sẽ bị chặn.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Ảnh theo màu</h2>
                                <p class="text-xs text-slate-500">Ảnh ở đây sẽ chỉ hiển thị khi khách chọn đúng màu tương ứng trên trang chi tiết sản phẩm.</p>
                            </div>
                            <button
                                type="button"
                                data-add-color-image-row
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >
                                + Thêm ảnh màu
                            </button>
                        </div>

                        @if($existingColorImages->isNotEmpty())
                        <div class="mt-4">
                            <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Ảnh màu hiện có</div>
                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach($existingColorImages as $image)
                                <label class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                    <img src="{{ $image->resolved_url }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-44 w-full object-cover">
                                    <div class="space-y-2 p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                {{ $image->colorOption?->name ?? 'Chưa gắn màu' }}
                                            </span>
                                            <span class="text-xs text-slate-500">#{{ $image->id }}</span>
                                        </div>
                                        <p class="text-xs leading-5 text-slate-500">{{ $image->alt_text ?: $product->name }}</p>
                                        <label class="flex items-center gap-2 text-xs font-medium text-red-600">
                                                <input
                                                    type="checkbox"
                                                    name="remove_color_image_ids[]"
                                                    value="{{ $image->id }}"
                                                {{ in_array($image->id, $selectedRemovalImageIds, true) ? 'checked' : '' }}
                                                class="h-4 w-4 rounded border-slate-300 text-red-500 focus:ring-red-400"
                                            >
                                            Xóa ảnh này khi lưu
                                        </label>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-100 text-slate-600">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold">Màu sắc</th>
                                            <th class="px-4 py-3 text-left font-semibold">Ảnh</th>
                                            <th class="px-4 py-3 text-left font-semibold">Alt text</th>
                                            <th class="px-4 py-3 text-center font-semibold">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="color-image-rows" data-next-color-image-index="{{ $nextColorImageIndex }}">
                                        @foreach($colorImageRows as $index => $colorImageRow)
                                        <tr class="color-image-row border-b border-slate-100 align-top last:border-0">
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    name="color_images[{{ $index }}][color]"
                                                    value="{{ $colorImageRow['color'] ?? '' }}"
                                                    list="variant-color-suggestions"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                    placeholder="VD: Đen, Trắng"
                                                >
                                                @error("color_images.$index.color")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="file"
                                                    name="color_images[{{ $index }}][image]"
                                                    accept="image/*"
                                                    data-optional-file
                                                    class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 shadow-sm file:mr-2 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-xs file:font-medium file:text-white hover:file:bg-slate-800"
                                                >
                                                @error("color_images.$index.image")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    type="text"
                                                    name="color_images[{{ $index }}][alt_text]"
                                                    value="{{ $colorImageRow['alt_text'] ?? '' }}"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                    placeholder="Mô tả ngắn cho ảnh"
                                                >
                                                @error("color_images.$index.alt_text")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button
                                                    type="button"
                                                    data-remove-color-image-row
                                                    class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                                                >
                                                    Xóa
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">Bạn có thể thêm nhiều ảnh cho cùng một màu bằng cách tạo nhiều dòng.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Nhập thêm hàng</h2>
                                <p class="text-xs text-slate-500">Tìm biến thể bằng ô search và chọn từ dropdown gợi ý. Hệ thống sẽ tự cộng số lượng nhập thêm vào tồn hiện tại.</p>
                            </div>
                            <div class="text-xs font-medium text-slate-500">
                                {{ $product->variants->count() }} biến thể | Tổng tồn hiện tại: <span class="text-slate-900">{{ $product->variants->sum('stock_quantity') }}</span>
                            </div>
                        </div>

                        @if($product->variants->isNotEmpty())
                        <div class="mt-4 space-y-4">
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-xs text-slate-500">
                                Gợi ý tìm kiếm: gõ màu, size hoặc SKU như <span class="font-semibold text-slate-700">Đen / M</span> hoặc <span class="font-semibold text-slate-700">SKU</span>.
                            </div>

                            <div
                                id="restock-rows"
                                data-next-restock-index="{{ $nextRestockIndex }}"
                                data-variant-options='@json($restockVariantOptions)'
                                class="space-y-3"
                            >
                                @foreach($restockEntries as $index => $restockEntry)
                                @php
                                    $selectedVariantId = (int) ($restockEntry['variant_id'] ?? 0);
                                    $restockQuantity = $restockEntry['quantity'] ?? '';
                                    $selectedVariant = $restockVariantOptions->firstWhere('id', $selectedVariantId);
                                    $currentStock = (int) ($selectedVariant['stock_quantity'] ?? 0);
                                    $nextStock = $currentStock + (int) $restockQuantity;
                                    $searchValue = $selectedVariant['search_label'] ?? '';
                                @endphp
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" data-restock-row>
                                    <div class="grid gap-4 lg:grid-cols-[minmax(0,1.6fr)_120px_150px_auto] lg:items-start">
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Biến thể</label>
                                            <input
                                                type="hidden"
                                                name="restock_entries[{{ $index }}][variant_id]"
                                                value="{{ $selectedVariantId > 0 ? $selectedVariantId : '' }}"
                                                data-restock-variant-id
                                            >
                                            <input
                                                type="text"
                                                value="{{ $searchValue }}"
                                                list="restock-variant-suggestions"
                                                data-restock-search
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                placeholder="Tìm theo màu, size hoặc SKU"
                                            >
                                            @error("restock_entries.$index.variant_id")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-600" data-restock-label>
                                                    {{ $selectedVariant['label'] ?? 'Chưa chọn biến thể' }}
                                                </span>
                                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-500" data-restock-sku>
                                                    {{ !empty($selectedVariant['sku']) ? 'SKU: ' . $selectedVariant['sku'] : 'SKU: --' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tồn hiện tại</label>
                                            <div class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700" data-restock-current-stock>
                                                {{ $selectedVariant ? $currentStock : '--' }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nhập thêm</label>
                                            <input
                                                type="number"
                                                name="restock_entries[{{ $index }}][quantity]"
                                                value="{{ $restockQuantity }}"
                                                min="0"
                                                step="1"
                                                data-restock-quantity
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                                                placeholder="0"
                                            >
                                            @error("restock_entries.$index.quantity")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex items-end justify-end">
                                            <button
                                                type="button"
                                                data-remove-restock-row
                                                class="inline-flex h-[44px] items-center justify-center rounded-xl border border-slate-200 px-4 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                                            >
                                                Xóa dòng
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                        <span class="text-slate-500">Tồn sau nhập:</span>
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700" data-restock-next-stock>
                                            {{ $selectedVariant ? $nextStock : '--' }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <datalist id="restock-variant-suggestions">
                                @foreach($restockVariantOptions as $variantOption)
                                    <option value="{{ $variantOption['search_label'] }}"></option>
                                @endforeach
                            </datalist>

                            <button
                                type="button"
                                data-add-restock-row
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >
                                + Thêm dòng nhập hàng
                            </button>
                        </div>
                        @else
                        <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-5 text-sm text-slate-500">
                            Sản phẩm này chưa có biến thể nên hiện chưa có tồn kho để cập nhật.
                        </div>
                        @endif
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
const variantRows = document.getElementById('variant-rows');
const colorImageRows = document.getElementById('color-image-rows');
const restockRows = document.getElementById('restock-rows');
let restockVariantOptions = [];

try {
    restockVariantOptions = JSON.parse(restockRows?.dataset.variantOptions || '[]');
} catch (error) {
    restockVariantOptions = [];
}

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

const getRestockVariantOption = (variantId) => {
    return restockVariantOptions.find((option) => Number(option.id) === Number(variantId)) || null;
};

const findRestockVariantBySearch = (searchValue) => {
    const normalizedSearch = String(searchValue || '').trim().toLowerCase();

    if (normalizedSearch === '') {
        return null;
    }

    return restockVariantOptions.find((option) => {
        return String(option.search_label || '').trim().toLowerCase() === normalizedSearch;
    }) || null;
};

const syncRestockRow = (row) => {
    if (!row) {
        return;
    }

    const variantIdInput = row.querySelector('[data-restock-variant-id]');
    const searchInput = row.querySelector('[data-restock-search]');
    const quantityInput = row.querySelector('[data-restock-quantity]');
    const labelNode = row.querySelector('[data-restock-label]');
    const skuNode = row.querySelector('[data-restock-sku]');
    const currentStockNode = row.querySelector('[data-restock-current-stock]');
    const nextStockNode = row.querySelector('[data-restock-next-stock]');
    const selectedVariant = getRestockVariantOption(variantIdInput?.value || 0);
    const currentStock = Number(selectedVariant?.stock_quantity || 0);
    const addedStock = Math.max(0, Number(quantityInput?.value || 0));

    if (searchInput && selectedVariant && searchInput.value.trim() === '') {
        searchInput.value = selectedVariant.search_label || '';
    }

    if (labelNode) {
        labelNode.textContent = selectedVariant?.label || 'Chưa chọn biến thể';
    }

    if (skuNode) {
        skuNode.textContent = selectedVariant?.sku ? `SKU: ${selectedVariant.sku}` : 'SKU: --';
    }

    if (currentStockNode) {
        currentStockNode.textContent = selectedVariant ? String(currentStock) : '--';
    }

    if (nextStockNode) {
        nextStockNode.textContent = selectedVariant ? String(currentStock + addedStock) : '--';
    }
};

const bindRestockRow = (row) => {
    if (!row || row.dataset.restockBound === 'true') {
        return;
    }

    row.dataset.restockBound = 'true';

    const searchInput = row.querySelector('[data-restock-search]');
    const variantIdInput = row.querySelector('[data-restock-variant-id]');
    const quantityInput = row.querySelector('[data-restock-quantity]');

    searchInput?.addEventListener('input', () => {
        const matchedVariant = findRestockVariantBySearch(searchInput.value);

        if (variantIdInput) {
            variantIdInput.value = matchedVariant ? String(matchedVariant.id) : '';
        }

        syncRestockRow(row);
    });

    searchInput?.addEventListener('change', () => {
        const matchedVariant = findRestockVariantBySearch(searchInput.value);

        if (!matchedVariant) {
            if (variantIdInput) {
                variantIdInput.value = '';
            }

            syncRestockRow(row);
            return;
        }

        searchInput.value = matchedVariant.search_label || '';

        if (variantIdInput) {
            variantIdInput.value = String(matchedVariant.id);
        }

        syncRestockRow(row);
    });

    quantityInput?.addEventListener('input', () => {
        syncRestockRow(row);
    });

    syncRestockRow(row);
};

restockRows?.querySelectorAll('[data-restock-row]').forEach((row) => {
    bindRestockRow(row);
});

document.querySelector('[data-add-restock-row]')?.addEventListener('click', () => {
    if (!restockRows) {
        return;
    }

    const index = Number(restockRows.dataset.nextRestockIndex || 0);
    restockRows.dataset.nextRestockIndex = String(index + 1);

    const row = document.createElement('div');
    row.className = 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm';
    row.setAttribute('data-restock-row', '');
    row.innerHTML = `
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.6fr)_120px_150px_auto] lg:items-start">
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Biến thể</label>
                <input type="hidden" name="restock_entries[${index}][variant_id]" value="" data-restock-variant-id>
                <input
                    type="text"
                    value=""
                    list="restock-variant-suggestions"
                    data-restock-search
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                    placeholder="Tìm theo màu, size hoặc SKU"
                >
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-600" data-restock-label>Chưa chọn biến thể</span>
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-500" data-restock-sku>SKU: --</span>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tồn hiện tại</label>
                <div class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700" data-restock-current-stock>--</div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nhập thêm</label>
                <input
                    type="number"
                    name="restock_entries[${index}][quantity]"
                    value=""
                    min="0"
                    step="1"
                    data-restock-quantity
                    class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                    placeholder="0"
                >
            </div>

            <div class="flex items-end justify-end">
                <button
                    type="button"
                    data-remove-restock-row
                    class="inline-flex h-[44px] items-center justify-center rounded-xl border border-slate-200 px-4 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Xóa dòng
                </button>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-500">Tồn sau nhập:</span>
            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700" data-restock-next-stock>--</span>
        </div>
    `;

    restockRows.appendChild(row);
    bindRestockRow(row);
});

restockRows?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-restock-row]');

    if (!button) {
        return;
    }

    const row = button.closest('[data-restock-row]');
    const totalRows = restockRows.querySelectorAll('[data-restock-row]').length;

    if (row && totalRows > 1) {
        row.remove();
        return;
    }

    const variantIdInput = row?.querySelector('[data-restock-variant-id]');

    const searchInput = row?.querySelector('[data-restock-search]');
    const quantityInput = row?.querySelector('[data-restock-quantity]');

    if (variantIdInput) {
        variantIdInput.value = '';
    }

    if (searchInput) {
        searchInput.value = '';
    }

    if (quantityInput) {
        quantityInput.value = '';
    }

    syncRestockRow(row);
});

document.querySelector('[data-add-variant-row]')?.addEventListener('click', () => {
    if (!variantRows) {
        return;
    }

    const index = Number(variantRows.dataset.nextVariantIndex || 0);
    variantRows.dataset.nextVariantIndex = String(index + 1);

    const row = document.createElement('tr');
    row.className = 'variant-row border-b border-slate-100 align-top last:border-0';
    row.innerHTML = `
        <td class="px-4 py-3">
            <input
                type="text"
                name="variants[${index}][size]"
                list="variant-size-suggestions"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                placeholder="VD: S, M, L"
            >
        </td>
        <td class="px-4 py-3">
            <input
                type="text"
                name="variants[${index}][color]"
                list="variant-color-suggestions"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                placeholder="VD: Đen, Trắng"
            >
        </td>
        <td class="px-4 py-3">
            <input
                type="number"
                name="variants[${index}][stock_quantity]"
                min="0"
                step="1"
                class="mx-auto block w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-center text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                placeholder="0"
            >
        </td>
        <td class="px-4 py-3">
            <input
                type="text"
                name="variants[${index}][sku]"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                placeholder="Để trống để tự tạo"
            >
        </td>
        <td class="px-4 py-3 text-center">
            <button
                type="button"
                data-remove-variant-row
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
            >
                Xóa
            </button>
        </td>
    `;

    variantRows.appendChild(row);
});

variantRows?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-variant-row]');

    if (!button) {
        return;
    }

    button.closest('tr')?.remove();
});

document.querySelector('[data-add-color-image-row]')?.addEventListener('click', () => {
    if (!colorImageRows) {
        return;
    }

    const index = Number(colorImageRows.dataset.nextColorImageIndex || 0);
    colorImageRows.dataset.nextColorImageIndex = String(index + 1);

    const row = document.createElement('tr');
    row.className = 'color-image-row border-b border-slate-100 align-top last:border-0';
    row.innerHTML = `
        <td class="px-4 py-3">
            <input
                type="text"
                name="color_images[${index}][color]"
                list="variant-color-suggestions"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                placeholder="VD: Đen, Trắng"
            >
        </td>
        <td class="px-4 py-3">
            <input
                type="file"
                name="color_images[${index}][image]"
                accept="image/*"
                data-optional-file
                class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 shadow-sm file:mr-2 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-xs file:font-medium file:text-white hover:file:bg-slate-800"
            >
        </td>
        <td class="px-4 py-3">
            <input
                type="text"
                name="color_images[${index}][alt_text]"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-[#C5A572] focus:ring-4 focus:ring-[#C5A572]/10"
                placeholder="Mô tả ngắn cho ảnh"
            >
        </td>
        <td class="px-4 py-3 text-center">
            <button
                type="button"
                data-remove-color-image-row
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
            >
                Xóa
            </button>
        </td>
    `;

    colorImageRows.appendChild(row);
});

colorImageRows?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-color-image-row]');

    if (!button) {
        return;
    }

    button.closest('tr')?.remove();
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
