@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')
@section('page_title', 'Chi Tiết Đơn Hàng #' . $order->id)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.orders.index') }}" class="px-3 py-1.5 bg-gray-50 text-gray-600 hover:bg-gray-100 rounded-md font-medium transition-colors cursor-pointer text-sm">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 text-green-800 border-l-4 border-green-500 rounded-md text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Thông tin đơn hàng & Cập nhật trạng thái -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Box 1: Tổng quan -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 border-b pb-3 mb-4">Thông Tin Chung</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Mã Đơn:</p>
                        <p class="font-medium text-lg">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Ngày Đặt:</p>
                        <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Phương thức thanh toán:</p>
                        <p class="font-medium">{{ $order->payment_method }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Khách Hàng (Tài khoản):</p>
                        <p class="font-medium w-fit px-2 py-0.5 bg-gray-100 rounded text-gray-800">{{ $order->customer->full_name ?? 'Khách lẻ' }}</p>
                    </div>
                </div>
            </div>

            <!-- Box 2: Danh sách sản phẩm -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col">
                <h3 class="font-bold text-gray-800 border-b pb-3 mb-4">Sản Phẩm Đã Đặt</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 border-y">
                            <tr>
                                <th class="px-3 py-3">Sản phẩm</th>
                                <th class="px-3 py-3 text-center">SL</th>
                                <th class="px-3 py-3 text-right">Đơn giá</th>
                                <th class="px-3 py-3 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="px-3 py-4">
                                    <div class="font-medium text-gray-800 flex items-center gap-3">
                                        @if($item->variant && $item->variant->product && $item->variant->product->primaryImage)
                                        <img src="{{ asset('storage/' . $item->variant->product->primaryImage->image_url) }}" class="w-12 h-12 object-cover rounded border">
                                        @else
                                        <div class="w-12 h-12 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-400">No img</div>
                                        @endif
                                        <div>
                                            <p class="line-clamp-2 text-wrap">{{ $item->variant->product->name ?? 'Sản phẩm đã xóa' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Phân loại: {{ $item->variant->color ?? '' }} - {{ $item->variant->size ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-3 py-4 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}₫</td>
                                <td class="px-3 py-4 text-right font-medium">{{ number_format($item->subtotal, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t">
                            <tr>
                                <td colspan="3" class="px-3 py-3 text-right text-gray-600 border-b border-gray-100">Tạm tính:</td>
                                <td class="px-3 py-3 text-right font-medium border-b border-gray-100">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-3 py-3 text-right text-gray-600 border-b border-gray-100">Phí vận chuyển:</td>
                                <td class="px-3 py-3 text-right font-medium border-b border-gray-100">+{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-3 py-3 text-right text-gray-600 border-b border-gray-100">Giảm giá:</td>
                                <td class="px-3 py-3 text-right text-green-600 font-medium border-b border-gray-100">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-right font-bold text-gray-800">Tổng thanh toán:</td>
                                <td class="px-3 py-4 text-right font-bold text-[#C5A572] text-xl">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thông tin giao hàng & Cập nhật -->
        <div class="space-y-6">
            <!-- Cập nhật trạng thái -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 border-b pb-3 mb-4">Cập Nhật Trạng Thái</h3>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái Giao hàng</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Đã giao hàng thành công</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái Thanh toán</label>
                        <select name="payment_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Đã thanh toán (Hoàn tất)</option>
                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold transition-colors cursor-pointer shadow-sm">
                        Lưu Thay Đổi
                    </button>
                </form>
            </div>

            <!-- Thông tin người nhận -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 border-b pb-3 mb-4">Thông Tin Vận Chuyển</h3>
                <div class="text-sm space-y-4">
                    <div>
                        <p class="text-gray-500 mb-1 text-xs uppercase font-semibold">Tên người nhận</p>
                        <p class="font-medium text-base">{{ $order->recipient_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1 text-xs uppercase font-semibold">Số điện thoại liên hệ</p>
                        <p class="font-medium text-base">{{ $order->recipient_phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1 text-xs uppercase font-semibold">Địa chỉ giao hàng</p>
                        <p class="font-medium leading-relaxed bg-gray-50 p-2 rounded border border-gray-100 mt-1">{{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
