@extends('layouts.app')

@section('title', 'Đặt Hàng Thành Công')

@section('content')
<div class="min-h-screen bg-[#0A0A0A] flex items-center justify-center py-16">
<div class="max-w-2xl w-full mx-auto px-4">

    {{-- Success Icon --}}
    <div class="text-center mb-10">
        <div class="w-24 h-24 bg-green-500/10 border-2 border-green-500/30 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="font-[Outfit] font-bold text-3xl text-white mb-3">Đặt Hàng Thành Công!</h1>
        <p class="text-gray-400">Cảm ơn bạn đã mua hàng tại CoolWear. Chúng tôi sẽ liên hệ sớm nhất!</p>
    </div>

    {{-- Order Info --}}
    <div class="bg-[#111] border border-[#1a1a1a] p-6 mb-6">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-[#1a1a1a]">
            <div>
                <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Mã Đơn Hàng</div>
                <div class="font-[Outfit] font-bold text-[#C5A572] text-xl">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="text-right">
                <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Ngày Đặt</div>
                <div class="text-white text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-[#1a1a1a]">
            <div>
                <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Người Nhận</div>
                <div class="text-white text-sm font-medium">{{ $order->recipient_name }}</div>
                <div class="text-gray-400 text-xs mt-0.5">{{ $order->recipient_phone }}</div>
            </div>
            <div>
                <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Thanh Toán</div>
                <div class="text-white text-sm font-medium">{{ $order->payment_method }}</div>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded {{ $order->payment_status == 'paid' ? 'bg-green-500/10 text-green-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                    {{ $order->payment_status == 'paid' ? 'Đã Thanh Toán' : 'Chờ Thanh Toán' }}
                </span>
            </div>
            <div class="col-span-2">
                <div class="text-gray-500 text-xs uppercase tracking-wider mb-1">Địa Chỉ Giao Hàng</div>
                <div class="text-white text-sm">{{ $order->shipping_address }}</div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="space-y-3 mb-4 pb-4 border-b border-[#1a1a1a]">
            <div class="text-gray-500 text-xs uppercase tracking-wider mb-3">Sản Phẩm Đã Đặt</div>
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

        {{-- Totals --}}
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

    {{-- Actions --}}
    <div class="flex gap-4">
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
