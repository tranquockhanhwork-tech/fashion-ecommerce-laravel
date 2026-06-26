@extends('admin.layouts.app')

@section('title', 'Quản Lí Khách Hàng')
@section('page_title', 'Danh sách Khách Hàng')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Tất Cả Khách Hàng</h2>
                <p class="text-sm text-gray-500 mt-1">Theo dõi hồ sơ, địa chỉ và tần suất mua hàng của khách.</p>
            </div>

            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col gap-3 sm:flex-row">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Tìm theo tên, email hoặc số điện thoại"
                    class="w-full sm:w-80 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm"
                >
                <button type="submit" class="px-4 py-2.5 bg-[#C5A572] hover:bg-[#b09265] text-white rounded-lg transition-colors text-sm font-medium">
                    Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tổng Khách Hàng</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalCustomers, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-2">Hiện hiển thị {{ $customers->count() }} khách trên trang này</p>
            </div>
            <div class="p-3 bg-violet-100 text-violet-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m10-12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Đã Từng Mua</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($customersWithOrders, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-2">Khách có ít nhất 1 đơn hàng</p>
            </div>
            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 10H4L5 9z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Khách Mới Tháng Này</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($newCustomersThisMonth, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-2">Tính từ đầu tháng đến hôm nay</p>
            </div>
            <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v6m3-3h-6M5 19h8a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Có Địa Chỉ</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($customersWithAddresses, 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400 mt-2">{{ $search !== '' ? 'Đang có bộ lọc tìm kiếm' : 'Chưa áp dụng bộ lọc' }}</p>
            </div>
            <div class="p-3 bg-amber-100 text-amber-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.243-4.243a8 8 0 1111.313 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Khách Hàng Mới</h3>
                <span class="text-xs uppercase tracking-wider text-gray-400">5 gần nhất</span>
            </div>

            <div class="space-y-4">
                @forelse($recentCustomers as $recentCustomer)
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full bg-[#C5A572]/15 text-[#9b7f55] font-bold flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($recentCustomer->full_name ?? 'K', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 line-clamp-1">{{ $recentCustomer->full_name }}</p>
                            <p class="text-sm text-gray-500 line-clamp-1">{{ $recentCustomer->user?->email ?? 'Không có email' }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-medium text-gray-700">{{ $recentCustomer->created_at?->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $recentCustomer->orders_count }} đơn</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">Chưa có khách hàng mới.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Khách Hàng Nổi Bật</h3>
                <span class="text-xs uppercase tracking-wider text-gray-400">Theo tổng chi tiêu</span>
            </div>

            <div class="space-y-4">
                @forelse($topCustomers as $topCustomer)
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 line-clamp-1">{{ $topCustomer->full_name }}</p>
                        <p class="text-sm text-gray-500 line-clamp-1">{{ $topCustomer->user?->email ?? 'Không có email' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-semibold text-[#C5A572]">{{ number_format((float) ($topCustomer->orders_sum_total_amount ?? 0), 0, ',', '.') }}₫</p>
                        <p class="text-xs text-gray-400">{{ $topCustomer->orders_count }} đơn hàng</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">Chưa có dữ liệu mua hàng.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Danh Sách Khách Hàng</h3>
                <p class="text-sm text-gray-500 mt-1">Bấm vào mã đơn gần nhất hoặc nút chi tiết để quản lý sâu hơn.</p>
            </div>
            <div class="text-sm text-gray-500">
                Trang <span class="font-semibold text-gray-800">{{ $customers->currentPage() }}</span> / {{ $customers->lastPage() }}
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Đơn gần nhất</th>
                        <th class="px-4 py-3">Khách hàng</th>
                        <th class="px-4 py-3">Liên hệ</th>
                        <th class="px-4 py-3">Địa chỉ mặc định</th>
                        <th class="px-4 py-3 text-center">Đơn hàng</th>
                        <th class="px-4 py-3 text-center">Wishlist</th>
                        <th class="px-4 py-3 text-center">Đánh giá</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">
                            @if($customer->latestOrder)
                                <a href="{{ route('admin.orders.show', $customer->latestOrder->id) }}" class="inline-flex flex-col hover:text-[#C5A572] transition-colors">
                                    <span class="font-semibold text-gray-900">#{{ $customer->latestOrder->id }}</span>
                                    <span class="text-xs text-gray-400 mt-1">{{ $customer->latestOrder->created_at?->format('d/m/Y') }}</span>
                                </a>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-medium">Chưa có đơn</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#C5A572]/15 text-[#9b7f55] font-bold flex items-center justify-center">
                                    {{ strtoupper(substr($customer->full_name ?? 'K', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $customer->full_name }}</p>
                                    <p class="text-xs text-gray-400">#KH{{ str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-gray-800">{{ $customer->user?->email ?? 'Không có email' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $customer->phone ?: 'Chưa cập nhật số điện thoại' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            @if($customer->defaultAddress)
                                <p class="text-gray-800 line-clamp-2">{{ $customer->defaultAddress->full_address }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $customer->defaultAddress->recipient_name }} | {{ $customer->defaultAddress->recipient_phone }}</p>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-medium">Chưa có địa chỉ</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex min-w-10 justify-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                {{ $customer->orders_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex min-w-10 justify-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">
                                {{ $customer->wishlists_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex min-w-10 justify-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                {{ $customer->reviews_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="inline-flex px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md font-medium transition-colors">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-500">Không tìm thấy khách hàng phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
