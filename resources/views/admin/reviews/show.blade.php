@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đánh Giá')
@section('page_title', 'Chi Tiết Đánh Giá')

@section('content')
@php
    $productImage = $review->product?->thumbnail;
    $productImageUrl = $productImage
        ? (str_starts_with($productImage, 'http') ? $productImage : asset('storage/' . $productImage))
        : null;
@endphp

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Đánh giá của {{ $review->customer?->full_name ?? 'Khách hàng' }}</h2>
            <p class="text-sm text-gray-500 mt-1">Gửi lúc {{ $review->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
            Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                @if($productImageUrl)
                    <img src="{{ $productImageUrl }}" alt="{{ $review->product?->name }}" class="w-full h-56 object-cover rounded-lg bg-gray-100">
                @else
                    <div class="w-full h-56 rounded-lg bg-gray-100 flex items-center justify-center text-sm text-gray-400">No image</div>
                @endif

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Sản phẩm</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $review->product?->name ?? 'Sản phẩm đã xóa' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-4 text-sm">
                <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                    <span class="text-gray-500">Khách hàng</span>
                    <span class="text-right text-gray-900">{{ $review->customer?->full_name ?? 'Không xác định' }}</span>
                </div>
                <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                    <span class="text-gray-500">Email</span>
                    <span class="text-right text-gray-900">{{ $review->customer?->user?->email ?? 'Không có email' }}</span>
                </div>
                <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                    <span class="text-gray-500">Điểm số</span>
                    <span class="text-right text-gray-900">{{ $review->rating }}/5</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-500">Trạng thái</span>
                    <span class="text-right {{ $review->is_approved ? 'text-green-700' : 'text-yellow-700' }}">
                        {{ $review->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900">Nội dung đánh giá</h3>
                <div class="mt-4 rounded-lg bg-gray-50 border border-gray-100 p-5">
                    <p class="text-base font-semibold text-gray-900">{{ $review->title ?: 'Không có tiêu đề' }}</p>
                    <p class="text-sm text-gray-600 mt-3 whitespace-pre-line">{{ $review->comment ?: 'Khách hàng không để lại nội dung chi tiết.' }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900">Thao tác quản trị</h3>
                <div class="mt-4 flex flex-wrap gap-3">
                    <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium {{ $review->is_approved ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} transition-colors">
                            {{ $review->is_approved ? 'Chuyển về chờ duyệt' : 'Duyệt đánh giá' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                            Xóa đánh giá
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
