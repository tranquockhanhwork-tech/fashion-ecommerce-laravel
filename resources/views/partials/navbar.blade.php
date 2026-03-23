{{-- resources/views/partials/navbar.blade.php --}}
<nav class="navbar" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0">
                <div class="w-8 h-8 bg-[#C5A572] flex items-center justify-center">
                    <span class="text-black font-bold text-sm font-[Outfit]">CW</span>
                </div>
                <span class="text-white font-[Outfit] font-bold text-xl tracking-widest">COOL<span class="text-[#C5A572]">WEAR</span></span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Trang Chủ</a>
                <a href="{{ route('shop.index') }}" class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">Shop</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Về Chúng Tôi</a>
                <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Liên Hệ</a>
            </div>

            {{-- Right Icons --}}
            <div class="flex items-center gap-1">
                {{-- Search --}}
                <form action="{{ route('shop.index') }}" method="GET" id="global-search-form" class="hidden lg:flex items-center relative ml-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm..." 
                        class="w-48 bg-[#111] border border-[#222] text-xs px-3 py-2 outline-none transition-colors placeholder:text-gray-600 focus:border-[#C5A572] rounded-full pr-8">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#C5A572] transition-colors" title="Tìm kiếm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </button>
                </form>
                {{-- Wishlist --}}
                <a href="{{ route('wishlist') }}" class="btn-ghost p-2.5 hidden lg:flex relative" title="Yêu thích">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </a>

                {{-- Cart --}}
                <button class="btn-ghost p-2.5 relative" data-cart-open title="Giỏ hàng">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                    <span data-cart-count class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#C5A572] text-black text-[10px] font-bold rounded-full hidden items-center justify-center">0</span>
                </button>

                {{-- Theme Toggle --}}
                <button id="theme-toggle" class="btn-ghost p-2.5 hidden lg:flex relative" title="Đổi giao diện">
                    {{-- Sun Icon --}}
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{-- Moon Icon --}}
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                {{-- User --}}
                @auth
                <div class="relative group hidden lg:block">
                    <button class="btn-ghost p-2.5 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </button>
                    <div class="absolute right-0 top-full mt-1 w-48 bg-[#111] border border-[#222] py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('account') }}" class="block px-4 py-2 text-sm text-gray-400 hover:text-[#C5A572] hover:bg-[#1a1a1a] transition-colors">Tài Khoản</a>
                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-400 hover:text-[#C5A572] hover:bg-[#1a1a1a] transition-colors">Đơn Hàng</a>
                        <div class="border-t border-[#222] my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-400 hover:text-red-400 hover:bg-[#1a1a1a] transition-colors">Đăng Xuất</button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="hidden lg:inline-flex items-center gap-2 btn-ghost p-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                </a>
                @endauth

                {{-- Hamburger --}}
                <button id="mobile-menu-btn" class="btn-ghost p-2.5 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

{{-- Mobile Menu --}}
<div id="mobile-menu" class="mobile-menu flex flex-col">
    <div class="flex items-center justify-between p-6 border-b border-[#1a1a1a]">
        <span class="text-white font-[Outfit] font-bold text-xl tracking-widest">COOL<span class="text-[#C5A572]">WEAR</span></span>
        <button id="mobile-menu-close" class="btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="flex flex-col p-6 gap-1">
        <a href="{{ route('home') }}" class="py-3 text-lg font-medium text-gray-300 hover:text-[#C5A572] border-b border-[#1a1a1a] transition-colors font-[Outfit]">Trang Chủ</a>
        <a href="{{ route('shop.index') }}" class="py-3 text-lg font-medium text-gray-300 hover:text-[#C5A572] border-b border-[#1a1a1a] transition-colors font-[Outfit]">Shop</a>
        <a href="{{ route('about') }}" class="py-3 text-lg font-medium text-gray-300 hover:text-[#C5A572] border-b border-[#1a1a1a] transition-colors font-[Outfit]">Về Chúng Tôi</a>
        <a href="{{ route('contact') }}" class="py-3 text-lg font-medium text-gray-300 hover:text-[#C5A572] transition-colors font-[Outfit]">Liên Hệ</a>
    </div>
    <div class="mt-auto flex flex-col">
        <div class="p-6">
            <button id="mobile-theme-toggle" class="flex items-center gap-3 py-3 text-lg font-medium text-gray-300 font-[Outfit]">
               <svg id="mobile-theme-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
               <span id="mobile-theme-text">Chế độ Sáng</span>
            </button>
        </div>
        <div class="p-6 border-t border-[#1a1a1a] flex gap-4">
            @guest
            <a href="{{ route('login') }}" class="btn-outline flex-1 text-center">Đăng Nhập</a>
            <a href="{{ route('register') }}" class="btn-primary flex-1 text-center">Đăng Ký</a>
            @endguest
        </div>
    </div>
</div>
