@extends('admin.layouts.app')

@section('title', 'Cập Nhật Sản Phẩm')
@section('page_title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Cập Nhật Thông Tin: {{ $product->name }}</h2>
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-100">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Tên sản phẩm -->
            <div class="col-span-1 md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Sản Phẩm *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
            </div>

            <!-- Danh mục -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Danh Mục *</label>
                <select id="category_id" name="category_id" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border bg-white">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ảnh đại diện -->
            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">Ảnh Đại Diện (Bỏ trống nếu giữ nguyên)</label>
                <input type="file" id="image_url" name="image_url" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border p-1 rounded-md mb-2">
                @if($product->primaryImage)
                    <div class="flex items-center gap-3 mt-2 bg-gray-50 p-2 rounded border border-gray-200">
                        <span class="text-xs text-gray-600 font-medium">Ảnh hiện tại:</span>
                        <img src="{{ asset('storage/' . $product->primaryImage->image_url) }}" class="h-12 w-12 object-cover rounded border bg-white shadow-sm">
                    </div>
                @endif
            </div>

            <!-- Giá gốc -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Giá Bán (₫) *</label>
                <input type="number" id="price" name="price" value="{{ old('price', (int)$product->price) }}" required min="0" step="1"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
            </div>

            <!-- Giá khuyến mãi -->
            <div>
                <label for="promotional_price" class="block text-sm font-medium text-gray-700 mb-1">Giá Khuyến Mãi (₫)</label>
                <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price', (int)$product->promotional_price) }}" min="0" step="1"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
            </div>

            <!-- Trạng thái -->
            <div class="col-span-1 md:col-span-2 flex items-center mt-2 pl-1">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer">Hiển thị sản phẩm trên web</label>
            </div>

            <!-- Mô tả -->
            <div class="col-span-1 md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả sản phẩm</label>
                <textarea id="description" name="description" rows="5"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Hủy bỏ</a>
            <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Cập Nhật SP</button>
        </div>
    </form>
</div>
@endsection
