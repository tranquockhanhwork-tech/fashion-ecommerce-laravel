@extends('admin.layouts.app')

@section('title', 'Quản Lí Đơn Hàng')
@section('page_title', 'Danh sách Đơn Hàng')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Chờ xác nhận',
        'processing' => 'Đang chuẩn bị',
        'shipped' => 'Đang giao',
        'delivered' => 'Đã giao',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
    ];

    $statusClasses = [
        'pending' => 'bg-amber-100 text-amber-800',
        'processing' => 'bg-sky-100 text-sky-800',
        'shipped' => 'bg-indigo-100 text-indigo-800',
        'delivered' => 'bg-emerald-100 text-emerald-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-rose-100 text-rose-800',
    ];

    $paymentLabels = [
        'unpaid' => 'Chưa thanh toán',
        'paid' => 'Đã thanh toán',
        'refunded' => 'Đã hoàn tiền',
    ];

    $paymentClasses = [
        'unpaid' => 'bg-rose-100 text-rose-800',
        'paid' => 'bg-emerald-100 text-emerald-800',
        'refunded' => 'bg-slate-200 text-slate-700',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Điều phối đơn hàng</h2>
                <p class="mt-1 text-sm text-slate-700">Theo dõi trạng thái xử lý, thanh toán và thông tin giao nhận trên cùng một màn hình.</p>
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 w-full xl:w-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Tìm mã đơn, khách, SĐT, mã vận đơn"
                    class="md:col-span-2 xl:w-80 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm"
                >
                <select name="status" class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm bg-white">
                    <option value="">Tất cả trạng thái đơn</option>
                    @foreach($statusLabels as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" {{ $status === $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <select name="payment_status" class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm bg-white">
                    <option value="">Tất cả thanh toán</option>
                    @foreach($paymentLabels as $paymentKey => $paymentLabel)
                        <option value="{{ $paymentKey }}" {{ $paymentStatus === $paymentKey ? 'selected' : '' }}>{{ $paymentLabel }}</option>
                    @endforeach
                </select>
                <div class="flex gap-3 md:col-span-2 xl:col-span-4">
                    <button type="submit" class="px-4 py-2.5 bg-[#C5A572] hover:bg-[#b09265] text-white rounded-lg transition-colors text-sm font-medium">
                        Lọc đơn hàng
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-slate-700">Tổng đơn hàng</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($totalOrders, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-slate-600">Hiển thị {{ $orders->count() }} đơn trên trang này</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-slate-700">Chờ xác nhận</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($pendingOrders, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-slate-600">Đơn cần phản hồi sớm</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-slate-700">Đang xử lý / giao</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($shippingOrders, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-slate-600">Bao gồm chuẩn bị, giao và đã giao</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-slate-700">Doanh thu đã thu</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($paidRevenue, 0, ',', '.') }}₫</p>
            <p class="mt-2 text-xs text-slate-600">Tính theo đơn đã thanh toán</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-950">Danh sách đơn hàng</h3>
                <p class="mt-1 text-sm text-slate-700">Thông tin trải đều để dễ quét nhanh, bấm vào mã đơn để xem chi tiết.</p>
            </div>
            <div class="text-sm text-slate-700">
                Trang <span class="font-semibold text-slate-950">{{ $orders->currentPage() }}</span> / {{ $orders->lastPage() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col style="width: 12%;">
                    <col style="width: 24%;">
                    <col style="width: 16%;">
                    <col style="width: 16%;">
                    <col style="width: 14%;">
                    <col style="width: 18%;">
                </colgroup>
                <thead class="bg-slate-100 text-xs uppercase tracking-wide text-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Mã hóa đơn</th>
                        <th class="px-6 py-4 text-left font-semibold">Khách hàng</th>
                        <th class="px-6 py-4 text-left font-semibold">Thời gian</th>
                        <th class="px-6 py-4 text-center font-semibold">Trạng thái</th>
                        <th class="px-6 py-4 text-center font-semibold">Thanh toán</th>
                        <th class="px-6 py-4 text-right font-semibold">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr
                        class="cursor-pointer hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-[#C5A572]/40"
                        role="link"
                        tabindex="0"
                        aria-label="Xem chi tiết đơn #{{ $order->id }}"
                        onclick="window.location.href='{{ route('admin.orders.show', $order->id) }}'"
                        onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location.href='{{ route('admin.orders.show', $order->id) }}'; }"
                    >
                        <td class="px-6 py-4 align-middle">
                            <span class="whitespace-nowrap text-base font-bold text-slate-950">#{{ $order->id }}</span>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <p class="truncate font-semibold text-slate-950">{{ $order->recipient_name }}</p>
                            <p class="mt-1 truncate text-xs font-medium text-slate-700">{{ $order->recipient_phone }}</p>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <p class="whitespace-nowrap font-medium text-slate-900">{{ $order->created_at?->format('d/m/Y') }}</p>
                            <p class="mt-1 whitespace-nowrap text-xs font-medium text-slate-700">{{ $order->created_at?->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <span class="inline-flex max-w-full items-center justify-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <span class="inline-flex max-w-full items-center justify-center rounded-full px-3 py-1 text-xs font-semibold {{ $paymentClasses[$order->payment_status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $paymentLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right align-middle">
                            <p class="whitespace-nowrap text-base font-bold text-emerald-800">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </p>
                            <p class="mt-1 whitespace-nowrap text-xs font-medium text-slate-700">{{ $order->items_count }} sản phẩm</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <p class="text-base font-semibold text-gray-800">Không có đơn hàng phù hợp</p>
                            <p class="text-sm text-gray-500 mt-2">Thử đổi bộ lọc hoặc tìm bằng mã đơn, tên khách và số điện thoại.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-6 py-3 text-xs font-medium text-slate-700">
            Mẹo: Bấm vào mã đơn để mở trang chi tiết và cập nhật trạng thái.
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
