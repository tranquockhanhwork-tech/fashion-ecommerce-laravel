@extends('layouts.app')

@section('title', 'Shop')
@section('meta_description', 'Khám phá toàn bộ bộ sưu tập thời trang CoolWear.')

@section('content')
@php
    $activeFilterCount = collect([
        request('search'),
        request('category_id'),
        request('max_price'),
        request('size'),
        request('color'),
    ])->filter(fn ($v) => filled($v))->count();
@endphp
<div class="shop-page min-h-screen bg-[#0A0A0A]">

{{-- Header --}}
<div class="shop-hero border-b border-[#1a1a1a] py-12 sm:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="breadcrumb mb-4">
            <a href="{{ route('home') }}">Trang Chủ</a>
            <span class="breadcrumb-sep">/</span>
            <span class="text-[#C5A572]">Shop</span>
        </div>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="section-label">Bộ Sưu Tập</p>
                <h1 class="font-[Outfit] font-bold text-4xl md:text-5xl text-white leading-tight">Tất Cả Sản Phẩm</h1>
                <p class="mt-3 text-sm md:text-base text-gray-400 max-w-2xl">
                    Chọn nhanh theo danh mục, màu sắc, kích cỡ và mức giá để tìm đúng mẫu bạn cần.
                </p>
            </div>
            <div class="shop-hero-stat">
                <span class="block text-xs uppercase tracking-[0.16em] text-gray-500 mb-1">Kết quả hiện tại</span>
                <strong class="text-2xl text-white">{{ number_format($products->total(), 0, ',', '.') }}</strong>
                <span class="text-sm text-gray-400">sản phẩm</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
    <div class="shop-shell md:grid md:grid-cols-12 md:gap-8">

        {{-- Filter Sidebar --}}
        <aside id="filter-sidebar" class="hidden md:block md:col-span-4 lg:col-span-3 md:order-2">
            <form action="{{ route('shop.index') }}" method="GET" id="filter-form">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="category_id" id="input-category" value="{{ request('category_id') }}">
                <input type="hidden" name="size" id="input-size" value="{{ request('size') }}">
                <input type="hidden" name="color" id="input-color" value="{{ request('color') }}">
                <div class="shop-filter-shell">
                    <div class="mb-6 pb-4 border-b border-[#2b2b2b]">
                        <p class="text-xs uppercase tracking-[0.16em] text-gray-500">Bộ lọc đang dùng</p>
                        <p class="mt-2 text-white font-semibold">{{ $activeFilterCount }} tiêu chí</p>
                    </div>
                
            {{-- Search Box --}}
            <div class="filter-group mb-6">
                <div class="relative group">
                    <input type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Tìm sản phẩm..." 
                        class="w-full bg-[#111] border border-[#2a2a2a] text-gray-300 text-sm px-4 py-2.5 outline-none focus:border-[#C5A572] transition-colors placeholder:text-gray-600">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 group-hover:text-[#C5A572] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Category --}}
            <div class="filter-group">
                <div class="filter-title" data-collapse="#filter-category" data-chevron>
                    Danh Mục
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 transition-transform" data-chevron fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="filter-category" class="space-y-2">
                    <button type="button" data-category-option data-category-id="" class="filter-option-btn flex items-center gap-3 group text-left w-full">
                        <span class="text-sm category-option-label {{ !request('category_id') ? 'text-[#C5A572] font-semibold' : 'text-gray-400' }} transition-colors">Tất Cả</span>
                    </button>
                    @foreach($categories as $cat)
                    <button type="button" data-category-option data-category-id="{{ $cat->id }}" class="filter-option-btn flex items-center gap-3 group text-left w-full">
                        <span class="text-sm category-option-label {{ request('category_id') == $cat->id ? 'text-[#C5A572] font-semibold' : 'text-gray-400' }} transition-colors">{{ $cat->name }}</span>
                    </button>
                    @foreach($cat->children as $child)
                    <button type="button" data-category-option data-category-id="{{ $child->id }}" class="filter-option-btn flex items-center gap-3 pl-4 group text-left w-full">
                        <span class="text-xs category-option-label {{ request('category_id') == $child->id ? 'text-[#C5A572] font-semibold' : 'text-gray-400' }} transition-colors">↳ {{ $child->name }}</span>
                    </button>
                    @endforeach
                    @endforeach
                </div>
            </div>

            {{-- Price Range --}}
            <div class="filter-group">
                <div class="filter-title">Khoảng Giá Tối Đa</div>
                <div class="space-y-3">
                    <input type="range" name="max_price" min="50000" max="2500000" step="50000" value="{{ request('max_price', 2500000) }}" class="w-full" id="price-range">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>0₫</span>
                        <span id="price-display" class="text-[#C5A572] font-semibold">{{ number_format(request('max_price', 2500000), 0, ',', '.') }}₫</span>
                        <span>2.5Tr</span>
                    </div>
                </div>
            </div>

            {{-- Size --}}
            <div class="filter-group">
                <div class="filter-title">Kích Cỡ</div>
                <div data-size-group class="flex flex-wrap gap-2">
                    @foreach($availableSizes as $size)
                    <button type="button" data-filter-size="{{ $size }}" class="size-btn filter-chip {{ request('size') == $size ? 'bg-[#C5A572] text-black border-[#C5A572]' : '' }}">{{ $size }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Color --}}
            <div class="filter-group">
                <div class="filter-title">Màu Sắc</div>
                <div data-color-group class="flex flex-wrap gap-2">
                    @foreach($availableColors as $color)
                    <button type="button" 
                        data-filter-color="{{ $color }}"
                        class="filter-chip px-3 py-1.5 text-xs border border-[#2a2a2a] text-gray-300 hover:border-[#C5A572] hover:text-[#C5A572] transition-colors">
                        {{ $color }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Clear --}}
            <button type="button" id="clear-filters" class="block mt-4 text-center text-xs text-gray-500 hover:text-[#C5A572] uppercase tracking-wider transition-colors w-full">
                Xóa Tất Cả Bộ Lọc
            </button>
                </div>
            </form>
        </aside>

        {{-- Main --}}
        <div class="flex-1 min-w-0 mt-8 md:mt-0 md:col-span-8 lg:col-span-9 md:order-1">
            {{-- Toolbar --}}
            <div class="shop-toolbar flex items-center justify-between mb-6 flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <button id="filter-toggle" class="md:hidden btn-outline text-xs py-2 px-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Bộ Lọc
                    </button>
                    <span id="shop-result-count" class="text-gray-500 text-sm">Hiển thị <span class="text-white font-semibold">{{ $products->count() }}</span> / <span class="text-white font-semibold">{{ $products->total() }}</span> sản phẩm</span>
                </div>
                <div class="flex items-center gap-4">
                    <select id="sort-select" class="bg-[#111] border border-[#2a2a2a] text-gray-300 text-sm px-3 py-2 outline-none focus:border-[#C5A572] transition-colors">
                        <option value="{{ route('shop.index', array_merge(request()->except('sort'), [])) }}" {{ !request('sort') ? 'selected' : '' }}>Mặc định</option>
                        <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort'=>'price_asc'])) }}" {{ request('sort')=='price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort'=>'price_desc'])) }}" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="{{ route('shop.index', array_merge(request()->except('sort'), ['sort'=>'newest'])) }}" {{ request('sort')=='newest' ? 'selected' : '' }}>Mới Nhất</option>
                    </select>
                    <div class="hidden md:flex items-center border border-[#2a2a2a]">
                        <button id="btn-grid-view" class="p-2 bg-[#C5A572] text-black transition-colors" title="Dạng lưới">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                            </svg>
                        </button>
                        <button id="btn-list-view" class="p-2 text-gray-500 hover:text-[#C5A572] transition-colors" title="Dạng danh sách dọc">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div id="product-grid" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 transition-opacity duration-300">
                @forelse($products as $product)
                <div class="product-card group">
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
                            @auth
                            <button class="btn-primary flex-1 py-2 text-xs" data-add-to-cart data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">Thêm Giỏ</button>
                            @else
                            <a href="{{ route('login') }}" class="btn-primary flex-1 py-2 text-xs flex items-center justify-center">Thêm Giỏ</a>
                            @endauth
                            
                            <a href="{{ route('shop.show', $product->id) }}" class="btn-outline py-2 px-2.5 text-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </a>
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
                        <div class="text-xs text-gray-600 mt-1">{{ $product->category?->name }}</div>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-32 text-center w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-[#1a1a1a] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <p class="text-gray-500 text-lg">Không tìm thấy sản phẩm phù hợp.</p>
                    <a href="{{ route('shop.index') }}" class="text-[#C5A572] mt-3 hover:underline">Xóa tất cả bộ lọc</a>
                </div>
                @endforelse
            </div>

            {{-- Pagination thật từ Laravel --}}
            <div id="shop-pagination" class="mt-12 flex justify-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
.shop-page {
    background:
        radial-gradient(900px 380px at 8% -8%, rgba(197, 165, 114, 0.14), transparent 62%),
        radial-gradient(700px 360px at 92% 0%, rgba(197, 165, 114, 0.1), transparent 64%),
        #0A0A0A;
}

.shop-hero {
    position: relative;
    background: linear-gradient(120deg, rgba(8, 8, 8, 0.98), rgba(18, 18, 18, 0.9));
}

.shop-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(197, 165, 114, 0.08), transparent 45%);
    pointer-events: none;
}

.shop-hero-stat {
    position: relative;
    z-index: 1;
    min-width: 220px;
    border: 1px solid rgba(197, 165, 114, 0.26);
    background: linear-gradient(145deg, rgba(22, 22, 22, 0.96), rgba(10, 10, 10, 0.92));
    padding: 1rem 1.2rem;
    border-radius: 14px;
    box-shadow: 0 18px 42px rgba(0, 0, 0, 0.34);
}

.shop-filter-shell {
    position: sticky;
    top: 96px;
    border: 1px solid #2a2a2a;
    border-radius: 14px;
    background: linear-gradient(180deg, #131313 0%, #101010 100%);
    padding: 1.1rem 1rem 1rem;
    box-shadow: 0 20px 42px rgba(0, 0, 0, 0.26);
}

.shop-toolbar {
    border: 1px solid #202020;
    background: linear-gradient(180deg, rgba(16, 16, 16, 0.94), rgba(12, 12, 12, 0.94));
    border-radius: 14px;
    padding: 1rem;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.24);
}

.shop-page #product-grid:not(.list-view) .product-card {
    border-radius: 16px;
    overflow: hidden;
    background: linear-gradient(180deg, #141414, #121212);
    border: 1px solid #2a2a2a;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
}

.shop-page #product-grid:not(.list-view) .product-card:hover {
    border-color: rgba(197, 165, 114, 0.7);
    transform: translateY(-5px);
    box-shadow: 0 22px 38px rgba(0, 0, 0, 0.34);
}

.shop-page #product-grid:not(.list-view) .product-card-image {
    aspect-ratio: 3 / 4;
    background: #1c1c1c;
}

.shop-page #product-grid:not(.list-view) .product-card-image img {
    transition: transform 0.65s ease;
}

.shop-page #product-grid:not(.list-view) .product-card:hover .product-card-image img {
    transform: scale(1.065);
}

.shop-page #product-grid:not(.list-view) .product-card .p-3 {
    padding: 0.95rem 0.95rem 1rem;
}

.shop-page #product-grid:not(.list-view) .product-card .p-3 h3 {
    letter-spacing: 0.01em;
}

.filter-option-btn {
    position: relative;
    padding: 0.5rem 0.75rem;
    border-left: 2px solid transparent;
    border-radius: 0.125rem;
    transition: all 0.2s ease;
}

.filter-option-btn:hover .category-option-label {
    color: #C5A572 !important;
}

.filter-option-btn.filter-option-active {
    border-left-color: #C5A572;
    background: rgba(197, 165, 114, 0.12);
}

.filter-option-btn.filter-option-active .category-option-label {
    color: #C5A572 !important;
    font-weight: 700;
}

.filter-chip {
    position: relative;
    transition: all 0.2s ease;
}

.filter-chip:hover {
    border-color: #C5A572 !important;
    color: #C5A572 !important;
    background: rgba(197, 165, 114, 0.08);
}

.filter-chip-active {
    border-color: #C5A572 !important;
    color: #161616 !important;
    background: #C5A572 !important;
    font-weight: 700;
    box-shadow: 0 0 0 1px rgba(197, 165, 114, 0.22), 0 10px 22px rgba(197, 165, 114, 0.16);
    transform: translateY(-1px);
}

.filter-chip.filter-chip-active:hover {
    color: #161616 !important;
    background: #C5A572 !important;
}

.filter-chip-active::after {
    content: '';
    position: absolute;
    inset: 2px;
    border: 1px solid rgba(22, 22, 22, 0.18);
    pointer-events: none;
}

/* CSS cho hiển thị list ngang */
#product-grid.list-view {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

#product-grid.list-view .product-card {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 2rem;
    background: var(--color-dark-card);
    border: 1px solid var(--color-dark-border);
    overflow: hidden;
    padding: 0;
    height: 300px;
}

#product-grid.list-view .product-card-image {
    width: 100%;
    height: 100%;
}

#product-grid.list-view .p-3 {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}

#product-grid.list-view .p-3 a h3 {
    font-size: 1.5rem; 
    margin-bottom: 1rem;
    color: var(--color-text-primary);
    white-space: normal;
}

#product-grid.list-view .p-3 .flex.items-center.gap-2 {
    margin-bottom: 1rem;
}

#product-grid.list-view .p-3 .text-[#C5A572] {
    font-size: 1.25rem;
}

#product-grid.list-view .product-card-actions {
    position: static;
    opacity: 1;
    visibility: visible;
    transform: none;
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

/* Nút wishlist khi ở List View */
#product-grid.list-view .action-wishlist {
    top: 1.5rem;
    right: 1.5rem;
}

@media (max-width: 768px) {
    .shop-hero-stat {
        min-width: 0;
        width: 100%;
    }

    .shop-toolbar {
        padding: 0.85rem;
    }

    .shop-page #product-grid {
        gap: 0.9rem;
    }

    #product-grid.list-view .product-card {
        grid-template-columns: 140px 1fr;
        height: auto;
    }
    #product-grid.list-view .product-card-image {
        height: 200px;
    }
    #product-grid.list-view .p-3 a h3 {
        font-size: 1.1rem;
    }
}
</style>
@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridBtn = document.getElementById('btn-grid-view');
    const listBtn = document.getElementById('btn-list-view');
    const productGrid = document.getElementById('product-grid');
    const filterForm = document.getElementById('filter-form');
    const categoryInput = document.getElementById('input-category');
    const sizeInput = document.getElementById('input-size');
    const colorInput = document.getElementById('input-color');
    const searchInput = filterForm?.querySelector('input[name="search"]');
    const resultCount = document.getElementById('shop-result-count');
    const paginationContainer = document.getElementById('shop-pagination');
    const priceRange = document.getElementById('price-range');
    const priceDisplay = document.getElementById('price-display');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const categoryButtons = Array.from(document.querySelectorAll('[data-category-option]'));
    const sizeButtons = Array.from(document.querySelectorAll('[data-filter-size]'));
    const colorButtons = Array.from(document.querySelectorAll('[data-filter-color]'));

    // Mặc định Layout
    let currentView = localStorage.getItem('shop-view') || 'grid';
    applyView(currentView);

    updateFilterVisuals();

    gridBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        applyView('grid');
        localStorage.setItem('shop-view', 'grid');
    });

    listBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        applyView('list');
        localStorage.setItem('shop-view', 'list');
    });

    function applyView(view) {
        if (!productGrid) return;
        if (view === 'list') {
            productGrid.classList.add('list-view');
            productGrid.classList.remove('grid', 'grid-cols-2', 'lg:grid-cols-3', 'xl:grid-cols-4');
            listBtn?.classList.add('bg-[#C5A572]', 'text-black');
            listBtn?.classList.remove('text-gray-500');
            gridBtn?.classList.remove('bg-[#C5A572]', 'text-black');
            gridBtn?.classList.add('text-gray-500');
        } else {
            productGrid.classList.remove('list-view');
            productGrid.classList.add('grid', 'grid-cols-2', 'lg:grid-cols-3', 'xl:grid-cols-4');
            gridBtn?.classList.add('bg-[#C5A572]', 'text-black');
            gridBtn?.classList.remove('text-gray-500');
            listBtn?.classList.remove('bg-[#C5A572]', 'text-black');
            listBtn?.classList.add('text-gray-500');
        }
    }

    // AJAX XỬ LÝ
    let searchTimeout;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateProducts();
        }, 500); 
    });

    categoryButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!categoryInput) return;
            const nextCategory = button.dataset.categoryId || '';
            categoryInput.value = nextCategory;
            updateFilterVisuals();
            updateProducts();
        });
    });

    sizeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!sizeInput) return;
            const nextSize = button.dataset.filterSize || '';
            sizeInput.value = sizeInput.value === nextSize ? '' : nextSize;
            updateFilterVisuals();
            updateProducts();
        });
    });

    colorButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!colorInput) return;
            const nextColor = button.dataset.filterColor || '';
            colorInput.value = colorInput.value === nextColor ? '' : nextColor;
            updateFilterVisuals();
            updateProducts();
        });
    });

    let priceTimeout;
    priceRange?.addEventListener('input', function() {
        const val = parseInt(this.value || '0', 10).toLocaleString('vi-VN') + '₫';
        if (priceDisplay) priceDisplay.textContent = val;

        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(() => {
            updateProducts();
        }, 250);
    });

    clearFiltersBtn?.addEventListener('click', () => {
        if (!filterForm) return;

        filterForm.reset();
        if (categoryInput) categoryInput.value = '';

        const hiddenSort = filterForm.querySelector('input[name="sort"]');
        const hiddenSize = filterForm.querySelector('input[name="size"]');
        const hiddenColor = filterForm.querySelector('input[name="color"]');
        if (hiddenSort) hiddenSort.value = '';
        if (hiddenSize) hiddenSize.value = '';
        if (hiddenColor) hiddenColor.value = '';

        if (priceRange) priceRange.value = '2500000';
        if (priceDisplay) priceDisplay.textContent = Number(2500000).toLocaleString('vi-VN') + '₫';
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) sortSelect.selectedIndex = 0;

        updateFilterVisuals();
        updateProducts();
    });

    filterForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        updateProducts();
    });

    document.getElementById('sort-select')?.addEventListener('change', function(e) {
        e.preventDefault();
        // Cập nhật lại logic url cho đúng AJAX thay vì location.href
        let sortVal = this.value; 
        if(sortVal.includes('sort=')) {
            let sortMatch = sortVal.match(/sort=([^&]+)/);
            if(sortMatch && filterForm) {
                let hiddenSort = filterForm.querySelector('input[name="sort"]');
                if(hiddenSort) hiddenSort.value = sortMatch[1];
            }
        } else {
            let hiddenSort = filterForm.querySelector('input[name="sort"]');
            if(hiddenSort) hiddenSort.value = '';
        }
        updateProducts();
    });

    async function updateProducts() {
        if (!filterForm) return;
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({ path: newUrl }, '', newUrl);

        try {
            if(productGrid) productGrid.style.opacity = '0.3';
            
            const response = await fetch(newUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newProducts = doc.getElementById('product-grid');
            const newPagination = doc.getElementById('shop-pagination');
            const newResultCount = doc.getElementById('shop-result-count');

            if (newProducts && productGrid) {
                productGrid.innerHTML = newProducts.innerHTML;
                applyView(localStorage.getItem('shop-view') || 'grid'); // Reapply style
            }
            
            if (paginationContainer && newPagination) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            } else if (paginationContainer) {
                paginationContainer.innerHTML = '';
            }

            if (resultCount && newResultCount) {
                resultCount.innerHTML = newResultCount.innerHTML;
            }

            updateFilterVisuals();
            if(productGrid) productGrid.style.opacity = '1';
        } catch (error) {
            console.error(error);
            if(productGrid) productGrid.style.opacity = '1';
        }
    }

    function updateFilterVisuals() {
        categoryButtons.forEach((button) => {
            const label = button.querySelector('.category-option-label');
            if (!label) return;

            const isActive = (button.dataset.categoryId || '') === (categoryInput?.value || '');
            button.classList.toggle('filter-option-active', isActive);
            label.classList.toggle('text-[#C5A572]', isActive);
            label.classList.toggle('font-semibold', isActive);
            label.classList.toggle('text-gray-400', !isActive);
        });

        sizeButtons.forEach((button) => {
            const isActive = (button.dataset.filterSize || '') === (sizeInput?.value || '');
            button.classList.toggle('filter-chip-active', isActive);
            button.classList.toggle('bg-[#C5A572]', isActive);
            button.classList.toggle('text-black', isActive);
            button.classList.toggle('border-[#C5A572]', isActive);
        });

        colorButtons.forEach((button) => {
            const isActive = (button.dataset.filterColor || '') === (colorInput?.value || '');
            button.classList.toggle('filter-chip-active', isActive);
            button.classList.toggle('border-[#2a2a2a]', !isActive);
            button.classList.toggle('text-gray-300', !isActive);

            if (isActive) {
                button.style.setProperty('background-color', '#C5A572', 'important');
                button.style.setProperty('border-color', '#C5A572', 'important');
                button.style.setProperty('color', '#161616', 'important');
                button.style.setProperty('font-weight', '700', 'important');
                button.style.setProperty('box-shadow', '0 0 0 1px rgba(197, 165, 114, 0.22), 0 10px 22px rgba(197, 165, 114, 0.16)', 'important');
                button.style.setProperty('transform', 'translateY(-1px)', 'important');
            } else {
                button.style.removeProperty('background-color');
                button.style.removeProperty('border-color');
                button.style.removeProperty('color');
                button.style.removeProperty('font-weight');
                button.style.removeProperty('box-shadow');
                button.style.removeProperty('transform');
            }
        });
    }

    document.addEventListener('click', function(e) {
        const pageLink = e.target.closest('#shop-pagination a');
        if (!pageLink || !filterForm) return;

        e.preventDefault();
        const url = new URL(pageLink.href);
        const page = url.searchParams.get('page') || '1';
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.set('page', page);
        window.history.pushState({ path: `${window.location.pathname}?${params.toString()}` }, '', `${window.location.pathname}?${params.toString()}`);

        if (productGrid) productGrid.style.opacity = '0.3';

        fetch(`${window.location.pathname}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(res => res.text()).then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newProducts = doc.getElementById('product-grid');
            const newPagination = doc.getElementById('shop-pagination');
            const newResultCount = doc.getElementById('shop-result-count');

            if (newProducts && productGrid) {
                productGrid.innerHTML = newProducts.innerHTML;
                applyView(localStorage.getItem('shop-view') || 'grid');
            }
            if (paginationContainer && newPagination) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }
            if (resultCount && newResultCount) {
                resultCount.innerHTML = newResultCount.innerHTML;
            }
        }).catch(console.error).finally(() => {
            if (productGrid) productGrid.style.opacity = '1';
        });
    });
});
</script>
@endpush
@endsection
