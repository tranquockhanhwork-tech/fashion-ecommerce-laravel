<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CoolWear') | CoolWear – Premium Fashion</title>
    <meta name="description" content="@yield('meta_description', 'CoolWear – Thương hiệu thời trang cao cấp. Khám phá bộ sưu tập mới nhất.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <script>
        window.appConfig = {
            urls: {
                login: @json(route('login')),
                cartAdd: @json(route('cart.add')),
                cartIndex: @json(route('cart.index')),
                cartRemoveBase: @json(url('/cart/remove')),
                checkoutIndex: @json(route('checkout.index')),
                shopIndex: @json(route('shop.index')),
                wishlistToggle: @json(route('wishlist.toggle')),
                shippingFee: @json(route('shipping.fee')),
                shippingProvinces: @json(route('shipping.provinces')),
                shippingDistrictsBase: @json(url('/shipping/districts')),
                shippingWardsBase: @json(url('/shipping/wards')),
            }
        };
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <script>
        (() => {
            const storageKey = 'color-theme';
            const savedTheme = localStorage.getItem(storageKey);
            const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
            const theme = savedTheme === 'light' || (!savedTheme && prefersLight) ? 'light' : 'dark';

            document.documentElement.classList.toggle('light-mode', theme === 'light');
            document.documentElement.dataset.theme = theme;
        })();
    </script>
</head>
<body class="bg-[#0A0A0A] text-[#F5F5F0] customer-mode">

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Cart Sidebar --}}
    @php
        $sidebarItems = collect();
        $sidebarTotal = 0;
        if (Auth::check() && Auth::user()->customer && Auth::user()->customer->cart) {
            $sidebarItems = Auth::user()->customer->cart->items()->with(['variant.product'])->get();
            foreach ($sidebarItems as $si) {
                $basePrice    = $si->variant->product->promotional_price ?: $si->variant->product->price;
                $siPrice      = $si->variant->price_override ?: $basePrice;
                $sidebarTotal += $siPrice * $si->quantity;
            }
        }
    @endphp
    <div id="cart-overlay" class="cart-overlay"></div>
    <aside id="cart-sidebar" class="cart-sidebar flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-[#1a1a1a]">
            <h2 class="font-[Outfit] font-semibold text-lg tracking-wide">Giỏ Hàng <span data-cart-sidebar-count class="text-[#C5A572] text-sm">({{ $sidebarItems->count() }})</span></h2>
            <button data-cart-close class="btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Cart Items --}}
        <div data-cart-sidebar-items class="flex-1 overflow-y-auto p-6 space-y-4">
        @if($sidebarItems->isNotEmpty())
            @foreach($sidebarItems as $si)
            @php
                $basePrice = $si->variant->product->promotional_price ?: $si->variant->product->price;
                $siPrice   = $si->variant->price_override ?: $basePrice;
                $siImg     = $si->variant->product->thumbnail;
                $siProdId  = $si->variant->product_id;
            @endphp
            <div class="flex gap-4 border-b border-[#1a1a1a] pb-4 last:border-0 last:pb-0" data-cart-item="{{ $si->id }}">
                <a href="{{ route('shop.show', $siProdId) }}" class="w-20 h-24 flex-shrink-0 bg-[#1a1a1a]">
                    <img src="{{ $siImg }}" alt="{{ $si->variant->product->name }}" class="w-full h-full object-cover">
                </a>
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <a href="{{ route('shop.show', $siProdId) }}" class="font-[Outfit] font-semibold text-white text-sm hover:text-[#C5A572] line-clamp-2 transition-colors">{{ $si->variant->product->name }}</a>
                            <button type="button" class="cart-remove-btn ml-2" data-cart-remove="{{ $si->id }}" title="Xóa sản phẩm khỏi giỏ hàng" aria-label="Xóa sản phẩm khỏi giỏ hàng">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="text-gray-500 text-xs mt-1">
                            @if($si->variant->size) Size: {{ $si->variant->size }} @endif
                            @if($si->variant->color) | {{ $si->variant->color }} @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-gray-400 text-xs">SL: {{ $si->quantity }}</span>
                        <span class="font-[Outfit] font-bold text-[#C5A572] text-sm">{{ number_format($siPrice * $si->quantity, 0, ',', '.') }}₫</span>
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="flex flex-col items-center justify-center p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-[#2a2a2a] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
            </svg>
            <p class="text-gray-500 text-sm mb-1">Giỏ hàng của bạn đang trống</p>
            <p class="text-gray-600 text-xs mb-6">Hãy thêm sản phẩm yêu thích vào giỏ!</p>
            <a href="{{ route('shop.index') }}" class="btn-primary text-xs" data-cart-close>Khám Phá Shop</a>
        </div>
        @endif
        </div>

        <div class="border-t border-[#1a1a1a] p-6 bg-[#111]">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-400">Tạm tính</span>
                <span data-cart-sidebar-total class="font-semibold text-white">{{ number_format($sidebarTotal, 0, ',', '.') }}₫</span>
            </div>
            <div class="flex justify-between text-sm mb-5">
                <span class="text-gray-400">Phí ship</span>
                <span class="text-[#C5A572] text-xs">Miễn phí từ 1.000.000₫</span>
            </div>
            <a href="{{ route('cart.index') }}" class="btn-primary w-full mb-3 text-center py-3 text-sm">Xem Giỏ Hàng</a>
            <a href="{{ route('checkout.index') }}" data-cart-checkout-action class="btn-outline w-full text-center py-3 text-sm {{ $sidebarItems->isEmpty() ? 'opacity-50 pointer-events-none' : '' }}">Thanh Toán Ngay</a>
        </div>
    </aside>
    <template id="cart-sidebar-empty-template">
        <div class="flex flex-col items-center justify-center p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-[#2a2a2a] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
            </svg>
            <p class="text-gray-500 text-sm mb-1">Giỏ hàng của bạn đang trống</p>
            <p class="text-gray-600 text-xs mb-6">Hãy thêm sản phẩm yêu thích vào giỏ!</p>
            <a href="{{ route('shop.index') }}" class="btn-primary text-xs" data-cart-close>Khám Phá Shop</a>
        </div>
    </template>

    {{-- Main Content --}}
    <main class="pt-16 lg:pt-20">
        @if(session('success'))
        <div class="toast show" id="session-toast">
            <span class="text-[#C5A572] text-lg">✓</span>
            {{ session('success') }}
        </div>
        <script>setTimeout(() => { const t = document.getElementById('session-toast'); if(t){t.classList.remove('show'); setTimeout(()=>t.remove(),400);} }, 3000);</script>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')

    <script>
        // Xử lý Thêm / Xoá Yêu Thích 
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.action-wishlist');
            if (!btn) return;
            
            e.preventDefault();
            const productId = btn.dataset.productId;
            if (!productId) return;
            
            try {
                const res = await fetch(window.appConfig?.urls?.wishlistToggle || `{{ route('wishlist.toggle') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                
                if (res.status === 401) {
                    alert('Vui lòng đăng nhập để lưu sản phẩm yêu thích!');
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                const json = await res.json();
                if (json.status === 'added') {
                    // Hiện tim màu shop (đã thêm)
                    btn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" style="color: #C5A572; fill: #C5A572;" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                    `;
                } else if (json.status === 'removed') {
                    // Hiện tim trắng rỗng (đã xoá) 
                    btn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white hover:text-[#C5A572] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                    `;
                }
            } catch(err) {
                console.log('Lỗi:', err);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
