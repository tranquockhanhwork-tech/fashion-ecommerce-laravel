@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Tổng Quan')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Thống kê Data --}}
    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Doanh Thu</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($revenue, 0, ',', '.') }}₫</h3>
        </div>
        <div class="p-3 bg-green-100 text-green-600 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Đơn Hàng Mới</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ $newOrdersCount }}</h3>
        </div>
        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
    </div>

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Khách Hàng</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalCustomers }}</h3>
        </div>
        <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
    </div>

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Tổng Sản Phẩm</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</h3>
        </div>
        <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Bảng Đơn Hàng Gần Đây --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 overflow-hidden flex flex-col">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">Đơn Hàng Gần Đây</h2>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 py-3 whitespace-nowrap">Mã ĐH</th>
                        <th class="px-3 py-3 whitespace-nowrap">Khách hàng</th>
                        <th class="px-3 py-3 whitespace-nowrap">Ngày</th>
                        <th class="px-3 py-3 text-right whitespace-nowrap">Tổng Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">#{{ $order->id }}</td>
                        <td class="px-3 py-3 whitespace-nowrap">{{ $order->recipient_name }}</td>
                        <td class="px-3 py-3 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-3 text-right text-green-600 font-medium whitespace-nowrap">
                            {{ number_format($order->total_amount, 0, ',', '.') }}₫
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">Chưa có đơn hàng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bảng Sản Phẩm Sắp Hết Và Hết Hàng --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 overflow-hidden flex flex-col">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">Sản Phẩm Sắp Hết Và Hết Hàng</h2>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Sản Phẩm</th>
                        <th class="px-4 py-3">Biến Thể (Màu - Size)</th>
                        <th class="px-4 py-3 text-right">Kho</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockVariants as $variant)
                    <tr class="border-b {{ $variant->stock_quantity === 0 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                        <td class="px-4 py-3 font-medium text-gray-900 line-clamp-1" title="{{ $variant->product->name }}">
                            {{ Str::limit($variant->product->name, 35) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs text-gray-800">
                                {{ $variant->color }} - {{ $variant->size }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                {{ $variant->stock_quantity === 0 ? 'bg-red-600 text-white' : ($variant->stock_quantity < 5 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $variant->stock_quantity }}
                            </span>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">Tồn kho đang ở mức an toàn.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
