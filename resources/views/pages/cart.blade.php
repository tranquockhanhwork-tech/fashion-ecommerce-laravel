@extends('layouts.app')

@section('title', 'Giỏ Hàng')

@section('content')

<div class="min-h-screen bg-[#0A0A0A]">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="breadcrumb mb-8">
        <a href="{{ route('home') }}">Trang Chủ</a>
        <span class="breadcrumb-sep">/</span>
        <span class="text-[#C5A572]">Giỏ Hàng</span>
    </div>

    <h1 class="font-[Outfit] font-bold text-3xl text-white mb-10">Giỏ Hàng Của Bạn</h1>

    <div class="grid lg:grid-cols-3 gap-10">

        {{-- Cart Items --}}
        <div class="lg:col-span-2 space-y-4">
            <div data-cart-page-items class="space-y-4">

            @if($cartItems->isNotEmpty())

                @foreach($cartItems as $ci)
                @php
                    $basePrice = $ci->variant->product->promotional_price ?: $ci->variant->product->price;
                    $itemPrice = $ci->variant->price_override ?: $basePrice;
                    $img       = $ci->variant->product->resolveThumbnailForColor($ci->variant->color_id);
                    $prodId    = $ci->variant->product_id;
                @endphp
                <div class="bg-[#111] border border-[#1a1a1a] p-5 flex gap-5 hover:border-[#2a2a2a] transition-colors" data-cart-item="{{ $ci->id }}">
                    <a href="{{ route('shop.show', $prodId) }}" class="w-24 h-28 flex-shrink-0 bg-[#1a1a1a] overflow-hidden">
                        <img src="{{ $img }}" alt="{{ $ci->variant->product->name }}" class="w-full h-full object-cover">
                    </a>
                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('shop.show', $prodId) }}" class="font-[Outfit] font-semibold text-white hover:text-[#C5A572] transition-colors">{{ $ci->variant->product->name }}</a>
                                <div class="text-gray-500 text-xs mt-1">
                                    @if($ci->variant->size) Size: {{ $ci->variant->size }} @endif
                                    @if($ci->variant->color) | Màu: {{ $ci->variant->color }} @endif
                                </div>
                            </div>
                            <button type="button" class="cart-remove-btn" data-cart-remove="{{ $ci->id }}" title="Xóa sản phẩm khỏi giỏ hàng" aria-label="Xóa sản phẩm khỏi giỏ hàng">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <div data-qty-control class="flex items-center">
                                <button class="qty-btn" data-qty="dec">−</button>
                                <input type="number" class="qty-input" value="{{ $ci->quantity }}" min="1"
                                       onchange="updateQty({{ $ci->id }}, this.value)">
                                <button class="qty-btn" data-qty="inc">+</button>
                            </div>
                            <span class="font-[Outfit] font-bold text-[#C5A572] text-lg">
                                {{ number_format($itemPrice * $ci->quantity, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Coupon --}}
                <div class="bg-[#111] border border-[#1a1a1a] p-5">
                    <div class="text-white text-sm font-semibold uppercase tracking-wider mb-3">Mã Giảm Giá</div>
                    <div class="flex gap-3">
                        <input type="text" placeholder="Nhập mã voucher..." class="form-input flex-1 text-sm">
                        <button class="btn-outline px-6 text-sm py-3">Áp Dụng</button>
                    </div>
                </div>

            @else
                {{-- Empty state --}}
                <div class="bg-[#111] border border-[#1a1a1a] p-16 text-center flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-[#2a2a2a] mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                    <h3 class="font-[Outfit] font-bold text-xl text-white mb-2">Giỏ hàng trống</h3>
                    <p class="text-gray-500 mb-8">Bạn chưa thêm bất kỳ sản phẩm nào vào giỏ hàng.</p>
                    <a href="{{ route('shop.index') }}" class="btn-primary px-8 py-3">Tiếp Tục Mua Sắm</a>
                </div>
            @endif
            </div>

            <template id="cart-page-empty-template">
                <div class="bg-[#111] border border-[#1a1a1a] p-16 text-center flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-[#2a2a2a] mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                    <h3 class="font-[Outfit] font-bold text-xl text-white mb-2">Giỏ hàng trống</h3>
                    <p class="text-gray-500 mb-8">Bạn chưa thêm bất kỳ sản phẩm nào vào giỏ hàng.</p>
                    <a href="{{ route('shop.index') }}" class="btn-primary px-8 py-3">Tiếp Tục Mua Sắm</a>
                </div>
            </template>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('shop.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-[#C5A572] text-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Tiếp Tục Mua Sắm
                </a>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-[#111] border border-[#1a1a1a] p-6 sticky top-24">
                <h2 class="font-[Outfit] font-semibold text-lg text-white mb-6 pb-4 border-b border-[#1a1a1a]">Tóm Tắt Đơn Hàng</h2>

                <div class="space-y-3 mb-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Tạm tính (<span data-cart-page-count>{{ $cartItems->count() }}</span> sản phẩm)</span>
                        <span data-cart-page-subtotal class="text-white">{{ number_format($cartTotal, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Giảm giá</span>
                        <span class="text-red-400">−0₫</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Phí vận chuyển</span>
                        <span class="text-gray-500 text-xs italic">Tính ở bước thanh toán</span>
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-[#2a2a2a] pt-5 mb-6">
                    <span class="font-[Outfit] font-semibold text-white text-lg">Tổng Cộng</span>
                    <span data-cart-page-total class="font-[Outfit] font-bold text-[#C5A572] text-2xl">{{ number_format($cartTotal, 0, ',', '.') }}₫</span>
                </div>

                <a href="{{ route('checkout.index') }}" data-cart-checkout-action class="btn-primary w-full py-4 text-sm mb-3 flex items-center justify-center gap-2 {{ $cartItems->isEmpty() ? 'opacity-50 pointer-events-none' : '' }}">
                    Tiến Hành Thanh Toán
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>

                <div class="flex items-center gap-3 justify-center mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    <span class="text-gray-500 text-xs">Thanh toán an toàn & được mã hóa</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
function updateQty(cartItemId, quantity) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const updateUrl = `{{ url('/cart/update') }}/${cartItemId}`;

    fetch(updateUrl, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
    });
}
</script>
@endpush
@endsection
