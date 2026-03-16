@extends('admin.layouts.app')

@section('title', 'Quản Lí Mã Giảm Giá')
@section('page_title', 'Danh sách Mã Giảm Giá')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Mã giảm giá và voucher</h2>
                <p class="text-sm text-gray-500 mt-1">Theo dõi hiệu lực, số lượt dùng và điều kiện áp dụng của từng mã.</p>
            </div>

            <div class="flex flex-col gap-3 lg:flex-row">
                <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex flex-col gap-3 lg:flex-row">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo mã hoặc mô tả" class="w-full lg:w-72 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm">
                    <select name="status" class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm bg-white">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Đã tắt</option>
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                        Lọc
                    </button>
                </form>

                <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2.5 bg-[#C5A572] hover:bg-[#b09265] text-white rounded-lg transition-colors text-sm font-medium text-center">
                    + Tạo voucher
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Tổng coupon</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalCoupons, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Đang hoạt động</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($activeCoupons, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Đã hết hạn</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($expiredCoupons, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Đã từng được dùng</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($usedCoupons, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Mã</th>
                        <th class="px-4 py-3">Loại giảm giá</th>
                        <th class="px-4 py-3">Điều kiện</th>
                        <th class="px-4 py-3">Thời gian</th>
                        <th class="px-4 py-3 text-center">Sử dụng</th>
                        <th class="px-4 py-3 text-center">Trạng thái</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    @php
                        $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900">{{ $coupon->code }}</p>
                            <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $coupon->description ?: 'Không có mô tả' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-gray-800">
                                {{ $coupon->discount_type === 'percentage' ? number_format($coupon->discount_value, 0, ',', '.') . '%' : number_format($coupon->discount_value, 0, ',', '.') . '₫' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                @if($coupon->max_discount_amount)
                                    Tối đa {{ number_format($coupon->max_discount_amount, 0, ',', '.') }}₫
                                @else
                                    Không giới hạn trần giảm
                                @endif
                            </p>
                        </td>
                        <td class="px-4 py-4">
                            <p>Đơn từ {{ number_format($coupon->min_order_amount, 0, ',', '.') }}₫</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $coupon->usage_limit ? 'Tối đa ' . number_format($coupon->usage_limit, 0, ',', '.') . ' lượt' : 'Không giới hạn lượt dùng' }}
                            </p>
                        </td>
                        <td class="px-4 py-4">
                            <p>{{ $coupon->starts_at?->format('d/m/Y H:i') ?? 'Áp dụng ngay' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $coupon->expires_at?->format('d/m/Y H:i') ?? 'Không có hạn' }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                {{ $coupon->used_count }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if(!$coupon->is_active)
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">Đã tắt</span>
                            @elseif($isExpired)
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">Hết hạn</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Hoạt động</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md font-medium transition-colors">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa coupon này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-medium transition-colors">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">Chưa có coupon nào phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
