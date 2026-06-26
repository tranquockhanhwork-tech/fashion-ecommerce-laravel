@extends('layouts.app')

@section('title', 'Đơn Hàng Của Tôi')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <div class="w-full md:w-1/4">
            <div class="bg-[#111] border border-[#1a1a1a] p-6 rounded">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-[#2a2a2a]">
                    <div class="w-12 h-12 bg-[#2a2a2a] rounded-full flex items-center justify-center text-[#C5A572] text-xl font-bold">
                        {{ substr(Auth::user()->customer?->full_name ?? Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Xin chào,</p>
                        <p class="font-bold text-white">{{ Auth::user()->customer?->full_name ?? Auth::user()->name }}</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('account') }}" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-[#2a2a2a] rounded transition-colors">
                        Thông tin tài khoản
                    </a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-white bg-[#2a2a2a] rounded transition-colors">
                        Đơn hàng của tôi
                    </a>
                    <a href="{{ route('wishlist') }}" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-[#2a2a2a] rounded transition-colors">
                        Sản phẩm yêu thích
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t border-[#2a2a2a]">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded transition-colors">
                            Đăng xuất
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <div class="bg-[#111] border border-[#1a1a1a] p-8 rounded">
                <h2 class="text-2xl font-bold mb-6 font-[Outfit]">Lịch Sử Đơn Hàng</h2>
                @if($orders->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    Bạn chưa có đơn hàng nào.
                </div>
                @else
                <div class="space-y-6">
                    @foreach($orders as $order)
                    <div class="border border-[#1a1a1a] bg-[#0A0A0A] rounded hover:border-[#2a2a2a] transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#1a1a1a] p-5 gap-4">
                            <div>
                                <div class="font-[Outfit] font-bold text-[#C5A572] text-lg mb-1">
                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded text-xs {{ $order->payment_status == 'paid' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' }}">
                                    {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                </span>
                                <span class="px-3 py-1 rounded text-xs bg-[#2a2a2a] text-gray-300 border border-[#333]">
                                    @if($order->status == 'pending')
                                        Chờ xác nhận
                                    @elseif($order->status == 'processing')
                                        Đang xử lý
                                    @elseif($order->status == 'shipped')
                                        Đang giao hàng
                                    @elseif($order->status == 'delivered')
                                        Đã nhận hàng
                                    @elseif($order->status == 'cancelled')
                                        Đã hủy
                                    @else
                                        {{ ucfirst($order->status) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                <div class="flex gap-4 items-start">
                                    <div class="w-16 h-20 bg-[#1a1a1a] flex-shrink-0 border border-[#222]">
                                        <img src="{{ $item->variant->product->thumbnail }}" class="w-full h-full object-cover" alt="{{ $item->variant->product->name }}">
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1">
                                        <div class="text-sm font-medium text-white line-clamp-1 mb-1">{{ $item->variant->product->name }}</div>
                                        <div class="text-xs text-gray-500 mb-1">
                                            Phân loại: @if($item->variant->size) {{ $item->variant->size }} @endif
                                            @if($item->variant->color) - {{ $item->variant->color }} @endif
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            Số lượng: {{ $item->quantity }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-[#C5A572] pt-1">
                                        {{ number_format($item->subtotal, 0, ',', '.') }}₫
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center justify-between border-t border-[#1a1a1a] p-5 bg-[#111] rounded-b gap-4">
                            <div class="text-sm text-gray-400">
                                Thành tiền: <span class="text-xl font-[Outfit] font-bold text-white ml-2">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('checkout.success', $order->id) }}" class="btn-outline text-xs px-5 py-2.5">
                                    Xem Hóa Đơn
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
