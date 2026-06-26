@extends('layouts.app')

@section('title', 'Đặt Hàng Thành Công')

@section('content')
@php
    $orderNumber = str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
    $orderCode = 'DH' . $orderNumber;
    $transfer = $paymentQr['transfer_info'] ?? [];
@endphp
<div class="min-h-screen bg-[#0A0A0A] py-10 sm:py-14">
<div class="max-w-6xl w-full mx-auto px-4">

    <div class="confirmation-top-grid {{ $paymentQr ? 'has-qr' : '' }} mb-6">
        <div class="bg-[#111] border border-[#1a1a1a] p-8 flex items-start justify-start min-h-[320px]">
            <div class="w-full max-w-2xl">
                <div class="w-24 h-24 bg-green-500/10 border-2 border-green-500/30 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-center">
                    <h1 class="font-[Outfit] font-bold text-3xl text-white mb-3">Đặt Hàng Thành Công!</h1>
                    <p class="text-gray-400 max-w-md mx-auto">Cảm ơn bạn đã mua hàng tại CoolWear. Chúng tôi sẽ liên hệ sớm nhất!</p>
                </div>
                <div class="mt-8 border-t border-[#1f1f1f] pt-6 text-left">
                    <div class="order-meta-row grid md:grid-cols-2 gap-4 border-b border-[#1a1a1a]">
                        <div>
                            <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Mã Đơn Hàng</div>
                            <div class="font-[Outfit] font-bold text-[#C5A572] text-2xl">#{{ $orderNumber }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Ngày Đặt</div>
                            <div class="text-white text-sm font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="order-contact-row grid md:grid-cols-2 gap-4 border-b border-[#1a1a1a]">
                        <div>
                            <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Người Nhận</div>
                            <div class="text-white text-sm font-medium">{{ $order->recipient_name }}</div>
                            <div class="text-gray-400 text-xs mt-0.5">{{ $order->recipient_phone }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Thanh Toán</div>
                            <div class="text-white text-sm font-medium">{{ $paymentMethodLabel }}</div>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded {{ $order->payment_status == 'paid' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                                {{ $order->payment_status == 'paid' ? 'Đã Thanh Toán' : 'Chờ Thanh Toán' }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-5">
                        <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Địa Chỉ Giao Hàng</div>
                        <div class="text-white text-sm">{{ $order->shipping_address }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($paymentQr)
        <div class="bg-[#111] border border-[#1a1a1a] p-6">
            <div class="mb-5 pb-4 border-b border-[#1a1a1a]">
                <div>
                    <h2 class="font-[Outfit] font-semibold text-xl text-white">Thanh Toán Chuyển Khoản</h2>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[220px,1fr] items-start">
                <div class="bg-white rounded-2xl p-4">
                    @if(!empty($paymentQr['available']) && !empty($paymentQr['qr_data_url']))
                    <img src="{{ $paymentQr['qr_data_url'] }}" alt="QR thanh toán đơn hàng #{{ $order->id }}" class="w-full h-auto rounded-xl">
                    @else
                    <div class="aspect-square w-full rounded-xl border border-dashed border-gray-300 flex items-center justify-center text-center text-sm text-gray-600 px-4">
                        Chưa tạo được QR. Vui lòng chuyển khoản thủ công theo thông tin bên cạnh.
                    </div>
                    @endif
                </div>

                <div>
                    @if(!empty($paymentQr['message']))
                    <div class="mb-4 rounded-xl border border-yellow-500/20 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-300">
                        {{ $paymentQr['message'] }}
                    </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-4 border border-[#242424] bg-[#0d0d0d] px-4 py-3 rounded-xl">
                            <span class="text-xs uppercase tracking-[0.16em] text-gray-500">Ngân hàng</span>
                            <span class="text-sm font-semibold text-white text-right">{{ $transfer['bank_name'] ?: ($transfer['acq_id'] ?? 'Chưa cấu hình') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border border-[#242424] bg-[#0d0d0d] px-4 py-3 rounded-xl">
                            <span class="text-xs uppercase tracking-[0.16em] text-gray-500">Số tài khoản</span>
                            <span class="text-sm font-semibold text-[#C5A572] text-right">{{ $transfer['account_no'] ?? 'Chưa cấu hình' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border border-[#242424] bg-[#0d0d0d] px-4 py-3 rounded-xl">
                            <span class="text-xs uppercase tracking-[0.16em] text-gray-500">Tên tài khoản</span>
                            <span class="text-sm font-semibold text-white text-right">{{ $transfer['account_name'] ?? 'Chưa cấu hình' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border border-[#242424] bg-[#0d0d0d] px-4 py-3 rounded-xl">
                            <span class="text-xs uppercase tracking-[0.16em] text-gray-500">Số tiền</span>
                            <span class="text-sm font-semibold text-[#C5A572] text-right">{{ number_format($transfer['amount'] ?? $order->total_amount, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border border-[#242424] bg-[#0d0d0d] px-4 py-3 rounded-xl">
                            <span class="text-xs uppercase tracking-[0.16em] text-gray-500">Nội dung CK</span>
                            <span class="text-sm font-semibold text-white text-right">{{ $transfer['add_info'] ?? $orderCode }}</span>
                        </div>
                    </div>

                    <p class="mt-4 text-xs leading-6 text-gray-500">
                        Sau khi chuyển khoản thành công, bạn có thể theo dõi trạng thái đơn trong mục đơn hàng. Hệ thống hiện chưa tự động đối soát giao dịch.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="bg-[#111] border border-[#1a1a1a] p-6 mb-6">
        <div class="text-gray-500 text-xs uppercase tracking-wider mb-3">Sản Phẩm Đã Đặt</div>
        <div class="space-y-3 mb-4 pb-4 border-b border-[#1a1a1a]">
            @foreach($order->items as $item)
            <div class="flex gap-3 items-center">
                <img src="{{ $item->variant->product->thumbnail }}"
                     alt="{{ $item->variant->product->name }}"
                     class="w-12 h-14 object-cover bg-[#1a1a1a] flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="text-white text-xs font-medium line-clamp-1">{{ $item->variant->product->name }}</div>
                    <div class="text-gray-500 text-xs mt-0.5">
                        @if($item->variant->size) {{ $item->variant->size }} @endif
                        @if($item->variant->color) / {{ $item->variant->color }} @endif
                        × {{ $item->quantity }}
                    </div>
                </div>
                <span class="text-[#C5A572] text-sm font-bold flex-shrink-0">
                    {{ number_format($item->subtotal, 0, ',', '.') }}₫
                </span>
            </div>
            @endforeach
        </div>

        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Tạm tính</span>
                <span class="text-white">{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Vận chuyển</span>
                @if($order->shipping_fee > 0)
                    <span class="text-white">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                @else
                    <span class="text-green-400 text-xs">Miễn Phí</span>
                @endif
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-[#2a2a2a] mt-3">
                <span class="font-[Outfit] font-semibold text-white">Tổng Cộng</span>
                <span class="font-[Outfit] font-bold text-[#C5A572] text-xl">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
            </div>
        </div>
    </div>

    <div class="flex gap-4 confirmation-actions">
        <a href="{{ route('shop.index') }}" class="btn-outline flex-1 py-3 text-sm text-center">
            Tiếp Tục Mua Sắm
        </a>
        <a href="{{ route('orders.index') }}" class="btn-primary flex-1 py-3 text-sm text-center">
            Xem Đơn Hàng
        </a>
    </div>

</div>
</div>
@endsection

@push('styles')
<style>
.confirmation-top-grid {
    display: grid;
    gap: 1.5rem;
}

.order-meta-row {
    align-items: flex-start;
    padding-top: 1.6rem;
    padding-bottom: 1.6rem;
}

.order-contact-row {
    padding-top: 1.6rem;
    padding-bottom: 1.6rem;
}

@media (min-width: 1024px) {
    .confirmation-top-grid.has-qr {
        grid-template-columns: minmax(420px, 1fr) minmax(420px, 1fr);
        align-items: stretch;
    }
}

@media (max-width: 767px) {
    .confirmation-actions {
        flex-direction: column;
    }
}
</style>
@endpush
