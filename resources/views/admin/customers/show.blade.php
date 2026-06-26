@extends('admin.layouts.app')

@section('title', 'Chi Tiết Khách Hàng')
@section('page_title', 'Chi tiết Khách Hàng')

@section('content')
@php
    $genderLabel = match ($customer->gender) {
        'male' => 'Nam',
        'female' => 'Nữ',
        'other' => 'Khác',
        default => 'Chưa cập nhật',
    };
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $customer->full_name }}</h2>
            <p class="text-sm text-gray-500 mt-1">Mã khách hàng: #KH{{ str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
            Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-[#C5A572]/15 text-[#9b7f55] text-2xl font-bold flex items-center justify-center">
                        {{ strtoupper(substr($customer->full_name ?? 'K', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ $customer->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $customer->user?->email ?? 'Không có email' }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4 text-sm">
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <span class="text-gray-500">Số điện thoại</span>
                        <span class="text-gray-900 text-right">{{ $customer->phone ?: 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <span class="text-gray-500">Giới tính</span>
                        <span class="text-gray-900 text-right">{{ $genderLabel }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-100 pb-3">
                        <span class="text-gray-500">Ngày sinh</span>
                        <span class="text-gray-900 text-right">{{ $customer->birthday?->format('d/m/Y') ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Tạo lúc</span>
                        <span class="text-gray-900 text-right">{{ $customer->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Đơn hàng</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $customer->orders_count }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Địa chỉ</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $customer->addresses_count }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Wishlist</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $customer->wishlists_count }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Đánh giá</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $customer->reviews_count }}</p>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Địa chỉ giao hàng</h3>
                    <span class="text-sm text-gray-400">{{ $customer->addresses->count() }} địa chỉ</span>
                </div>

                <div class="space-y-4">
                    @forelse($customer->addresses as $address)
                    <div class="rounded-lg border {{ $address->is_default ? 'border-[#C5A572] bg-amber-50/60' : 'border-gray-200 bg-gray-50' }} p-4">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <p class="font-semibold text-gray-900">{{ $address->recipient_name }}</p>
                            <span class="text-sm text-gray-500">{{ $address->recipient_phone }}</span>
                            @if($address->is_default)
                                <span class="px-2 py-0.5 rounded-full bg-[#C5A572] text-white text-xs font-semibold">Mặc định</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700">{{ $address->full_address }}</p>
                    </div>
                    @empty
                    <div class="rounded-lg border border-dashed border-gray-300 p-6 text-sm text-gray-500">
                        Khách hàng này chưa lưu địa chỉ giao hàng nào.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Đơn hàng gần đây</h3>
                    <span class="text-sm text-gray-400">{{ $customer->orders->count() }} đơn hiển thị</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3">Mã đơn</th>
                                <th class="px-4 py-3">Ngày đặt</th>
                                <th class="px-4 py-3">Trạng thái</th>
                                <th class="px-4 py-3">Thanh toán</th>
                                <th class="px-4 py-3 text-right">Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders->take(8) as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-semibold text-gray-900 hover:text-[#C5A572]">
                                        #{{ $order->id }}
                                    </a>
                                    <div class="text-xs text-gray-400 mt-1">{{ $order->items_count }} sản phẩm</div>
                                </td>
                                <td class="px-4 py-3">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-[#C5A572]">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Khách hàng này chưa có đơn hàng.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Sản phẩm yêu thích</h3>
                        <span class="text-sm text-gray-400">{{ $customer->wishlists->count() }} mục</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($customer->wishlists->take(5) as $wishlist)
                        @php
                            $wishlistProduct = $wishlist->product;
                            $wishlistImage = $wishlistProduct
                                ? (str_starts_with($wishlistProduct->thumbnail, 'http')
                                    ? $wishlistProduct->thumbnail
                                    : asset('storage/' . $wishlistProduct->thumbnail))
                                : null;
                        @endphp
                        <div class="flex items-center gap-3">
                            @if($wishlistProduct && $wishlistImage)
                                <img
                                    src="{{ $wishlistImage }}"
                                    alt="{{ $wishlistProduct->name }}"
                                    class="w-14 h-14 rounded object-cover bg-gray-100"
                                >
                            @else
                                <div class="w-14 h-14 rounded bg-gray-100 flex items-center justify-center text-[10px] text-gray-400 text-center px-1">
                                    No image
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $wishlistProduct?->name ?? 'Sản phẩm không còn tồn tại' }}</p>
                                <p class="text-sm text-[#C5A572]">{{ $wishlistProduct?->formatted_price ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Khách hàng này chưa lưu sản phẩm yêu thích.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Đánh giá gần đây</h3>
                        <span class="text-sm text-gray-400">{{ $customer->reviews->count() }} đánh giá</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($customer->reviews->take(5) as $review)
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $review->product?->name ?? 'Sản phẩm đã xóa' }}</p>
                                <span class="text-sm font-semibold text-[#C5A572]">{{ $review->rating }}/5</span>
                            </div>
                            @if($review->title)
                                <p class="text-sm text-gray-700 mt-1">{{ $review->title }}</p>
                            @endif
                            @if($review->comment)
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $review->comment }}</p>
                            @endif
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Khách hàng này chưa có đánh giá nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
