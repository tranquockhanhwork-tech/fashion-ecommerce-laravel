@extends('layouts.app')

@section('title', 'Sản Phẩm Yêu Thích')

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
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-[#2a2a2a] rounded transition-colors">
                        Đơn hàng của tôi
                    </a>
                    <a href="{{ route('wishlist') }}" class="block px-4 py-2 text-white bg-[#2a2a2a] rounded transition-colors">
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
                <h2 class="text-2xl font-bold mb-6 font-[Outfit]">Sản Phẩm Yêu Thích</h2>
                
                @if($products->isEmpty())
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#1a1a1a] mb-6 border border-[#2a2a2a]">
                        <svg class="w-8 h-8 text-[#C5A572]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Chưa có sản phẩm yêu thích</h3>
                    <p class="text-gray-400 mb-6 w-full max-w-md mx-auto">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích. Hãy tiếp tục khám phá và lưu lại những sản phẩm bạn thích nhé!</p>
                    <a href="{{ route('shop.index') }}" class="inline-block bg-[#C5A572] hover:bg-[#D4B27F] text-black font-bold py-3 px-8 rounded transition-colors">
                        Tiếp Tục Mua Sắm
                    </a>
                </div>
                @else
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                    <div class="product-card group relative">
                        <div class="product-card-image">
                            <a href="{{ route('shop.show', $product->id) }}" class="block h-full">
                                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                            </a>
                            @if($product->discount_percent)
                            <span class="product-badge badge-sale">-{{ $product->discount_percent }}%</span>
                            @endif
                            <button class="action-wishlist absolute top-3 right-3 w-8 h-8 bg-black/50 flex items-center justify-center text-white hover:text-red-500 transition-colors z-10" data-product-id="{{ $product->id }}">
                                @if(in_array($product->id, $wishlistIds ?? []))
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" style="color: #C5A572; fill: #C5A572;" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white hover:text-[#C5A572] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                                </svg>
                                @endif
                            </button>
                            <div class="product-card-actions">
                                <a href="{{ route('shop.show', $product->id) }}" class="btn-primary flex-1 py-2 text-xs flex items-center justify-center">Xem Chi Tiết</a>
                            </div>
                        </div>
                        <div class="p-3">
                            <a href="{{ route('shop.show', $product->id) }}">
                                <h3 class="font-[Outfit] font-medium text-sm text-white hover:text-[#C5A572] transition-colors mb-1.5 line-clamp-1">{{ $product->name }}</h3>
                            </a>
                            <div class="flex items-center gap-2">
                                <span class="text-[#C5A572] font-bold">{{ $product->formatted_price }}</span>
                                @if($product->promotional_price)
                                <span class="text-gray-600 text-xs line-through">{{ $product->formatted_original_price }}</span>
                                @endif
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
