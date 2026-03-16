@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', Str::limit(strip_tags($product->description), 160))

@section('content')
@php $totalStock = $product->variants->sum('stock_quantity'); @endphp
<div class="min-h-screen bg-[#0A0A0A]">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <div class="breadcrumb mb-8">
        <a href="{{ route('home') }}">Trang Chủ</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('shop.index') }}">Shop</a>
        @if($product->category)
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('shop.index', ['category_id' => $product->category_id]) }}">{{ $product->category->name }}</a>
        @endif
        <span class="breadcrumb-sep">/</span>
        <span class="text-[#C5A572]">{{ $product->name }}</span>
    </div>

    <div class="grid lg:grid-cols-2 gap-12 mb-20">

        {{-- Image Gallery --}}
        <div class="flex gap-4">
            {{-- Thumbnails --}}
            <div class="flex flex-col gap-3">
                @forelse($product->images as $img)
                <img data-gallery-thumb src="{{ $img->image_url }}" alt="{{ $img->alt_text ?? $product->name }}"
                     class="w-16 h-20 object-cover border-2 border-[#2a2a2a] cursor-pointer hover:border-[#C5A572] transition-colors">
                @empty
                <img data-gallery-thumb src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                     class="w-16 h-20 object-cover border-2 border-[#C5A572] cursor-pointer">
                @endforelse
            </div>
            {{-- Ảnh chính --}}
            <div class="flex-1 aspect-[4/5] bg-[#111] overflow-hidden">
                <img data-gallery-main src="{{ $product->thumbnail }}" alt="{{ $product->name }}"
                     class="w-full h-full object-cover transition-opacity duration-300">
            </div>
        </div>

        {{-- Product Info --}}
        <div
            data-product-detail
            data-variants='@json($variantsData)'
            data-total-stock="{{ $totalStock }}"
        >
            <div class="flex items-center gap-3 mb-3">
                @if($product->discount_percent)
                <span class="product-badge badge-sale static">-{{ $product->discount_percent }}%</span>
                @endif
                <span class="text-gray-500 text-xs uppercase tracking-widest">{{ $product->category?->name }}</span>
            </div>

            <h1 class="font-[Outfit] font-bold text-3xl xl:text-4xl text-white mb-4">{{ $product->name }}</h1>

            {{-- Rating --}}
            <div class="flex items-center gap-4 mb-5">
                <div class="flex items-center gap-0.5">
                    @php $avgRating = (int) round($product->average_rating ?: 5); @endphp
                    @foreach(range(1,5) as $s)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $s <= $avgRating ? 'text-amber-400' : 'text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endforeach
                </div>
                <span class="text-gray-400 text-sm">{{ $product->average_rating ?: '5.0' }} ({{ $product->reviews->count() }} đánh giá)</span>
                <span class="text-gray-600 text-sm">|</span>
                @if($totalStock > 0)
                <span id="product-stock-status" class="text-green-400 text-sm font-medium">● Còn hàng</span>
                @else
                <span id="product-stock-status" class="text-red-400 text-sm font-medium">● Hết hàng</span>
                @endif
            </div>

            {{-- Price --}}
            <div class="flex items-baseline gap-4 mb-6">
                <span class="font-[Outfit] font-bold text-4xl text-[#C5A572]">{{ $product->formatted_price }}</span>
                @if($product->promotional_price)
                <span class="text-gray-500 text-lg line-through">{{ $product->formatted_original_price }}</span>
                <span class="text-red-400 text-sm font-bold">-{{ $product->discount_percent }}%</span>
                @endif
            </div>

            <div class="gold-divider mb-6"></div>

            <p class="text-gray-400 text-sm leading-relaxed mb-8">
                {{ $product->description ?? 'Sản phẩm chất lượng cao từ CoolWear.' }}
            </p>

            {{-- Color --}}
            @if($colors->isNotEmpty())
            <div class="mb-6">
                <div class="text-white text-sm font-semibold uppercase tracking-wider mb-3">Màu Sắc: <span class="text-[#C5A572] font-normal" id="selected-color">Chưa chọn</span></div>
                <div class="flex flex-wrap gap-2">
                    @foreach($colors as $color)
                    <button
                        type="button"
                        data-variant-color="{{ $color }}"
                        class="size-btn variant-option min-w-[76px] justify-center"
                    >
                        {{ $color }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Size --}}
            @if($sizes->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-white text-sm font-semibold uppercase tracking-wider">Kích Cỡ: <span class="text-[#C5A572] font-normal" id="selected-size">Chưa chọn</span></div>
                    <button class="text-xs text-[#C5A572] underline">Hướng Dẫn Chọn Size</button>
                </div>
                <div data-size-group class="flex flex-wrap gap-2">
                    @foreach($sizes as $size)
                    <button type="button" data-variant-size="{{ $size }}" class="size-btn variant-option">{{ $size }}</button>
                    @endforeach
                </div>
                <div id="variant-stock-message" class="mt-4 text-sm text-gray-400">
                    @if($totalStock > 0)
                    Chọn màu sắc và kích cỡ để xem số lượng còn.
                    @else
                    Sản phẩm hiện đã hết hàng.
                    @endif
                </div>
            </div>
            @endif

            {{-- Quantity + Add to cart --}}
            <div class="flex gap-3 mb-5" data-product-purchase>
                <input type="hidden" data-selected-variant-id value="">
                <div data-qty-control class="flex items-center">
                    <button class="qty-btn" data-qty="dec">−</button>
                    <input type="number" class="qty-input" value="1" min="1">
                    <button class="qty-btn" data-qty="inc">+</button>
                </div>
                
                @auth
                <button class="btn-primary flex-1 py-3.5 opacity-50 cursor-not-allowed" data-add-to-cart data-requires-variant="true" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                    Thêm Vào Giỏ Hàng
                </button>
                @else
                <a href="{{ route('login') }}" class="btn-primary flex-1 py-3.5 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/>
                    </svg>
                    Thêm Vào Giỏ Hàng
                </a>
                @endauth

                <button class="action-wishlist btn-outline py-3.5 px-4" data-product-id="{{ $product->id }}">
                    @if(in_array($product->id, $wishlistIds ?? []))
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: #C5A572; fill: #C5A572;" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                    </svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white hover:text-[#C5A572] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    @endif
                </button>
            </div>

            @auth
            <button class="btn-primary w-full py-4 text-sm bg-[#1a1a1a] text-white hover:bg-[#C5A572] hover:text-black opacity-50 cursor-not-allowed" data-buy-now disabled>
                Mua Ngay
            </button>
            @else
            <a href="{{ route('login') }}" class="btn-primary flex items-center justify-center w-full py-4 text-sm bg-[#1a1a1a] text-white hover:bg-[#C5A572] hover:text-black">
                Mua Ngay
            </a>
            @endauth

            {{-- Trust Badges --}}
            <div class="grid grid-cols-3 gap-4 mt-8 pt-8 border-t border-[#1a1a1a]">
                <div class="text-center"><div class="text-2xl mb-1">🚚</div><div class="text-gray-500 text-xs">Free Ship<br>từ 1 triệu</div></div>
                <div class="text-center"><div class="text-2xl mb-1">🔄</div><div class="text-gray-500 text-xs">Đổi trả<br>30 ngày</div></div>
                <div class="text-center"><div class="text-2xl mb-1">🛡️</div><div class="text-gray-500 text-xs">100%<br>Chính Hãng</div></div>
            </div>
        </div>
    </div>

    {{-- Reviews từ DB --}}
    @if($product->reviews->isNotEmpty())
    <div class="mb-20">
        <div class="section-label mb-3">Nhận Xét</div>
        <h3 class="section-title text-2xl mb-8">Đánh Giá Từ Khách Hàng ({{ $product->reviews->count() }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($product->reviews as $review)
            <div class="bg-[#111] border border-[#1a1a1a] p-6 hover:border-[#C5A572]/50 transition-colors">
                <div class="flex items-center gap-0.5 mb-3">
                    @foreach(range(1,5) as $s)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endforeach
                </div>
                @if($review->title)
                <div class="font-semibold text-white mb-2 text-sm">{{ $review->title }}</div>
                @endif
                <p class="text-gray-400 text-sm leading-relaxed mb-4 italic">"{{ $review->comment }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-[#C5A572] flex items-center justify-center font-[Outfit] font-bold text-black text-sm">
                        {{ substr($review->customer?->full_name ?? 'K', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-[Outfit] font-semibold text-sm text-white">{{ $review->customer?->full_name ?? 'Khách hàng' }}</div>
                        <div class="text-gray-600 text-xs">{{ $review->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Related Products --}}
    @if($relatedProducts->isNotEmpty())
    <div>
        <div class="section-label mb-3">Có Thể Bạn Thích</div>
        <h3 class="section-title text-2xl mb-8">Sản Phẩm Liên Quan</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($relatedProducts as $p)
            <div class="product-card">
                <div class="product-card-image">
                    <a href="{{ route('shop.show', $p->id) }}" class="block h-full">
                        <img src="{{ $p->thumbnail }}" alt="{{ $p->name }}" loading="lazy">
                    </a>
                    <div class="product-card-actions">
                        @auth
                        <button class="btn-primary flex-1 py-2 text-xs" data-add-to-cart data-product-id="{{ $p->id }}" data-product-name="{{ $p->name }}">Thêm Giỏ</button>
                        @else
                        <a href="{{ route('login') }}" class="btn-primary flex-1 py-2 text-xs flex items-center justify-center">Thêm Giỏ</a>
                        @endauth
                    </div>
                </div>
                <div class="p-3">
                    <a href="{{ route('shop.show', $p->id) }}">
                        <h4 class="font-[Outfit] font-medium text-sm text-white hover:text-[#C5A572] transition-colors mb-1 line-clamp-1">{{ $p->name }}</h4>
                    </a>
                    <span class="text-[#C5A572] font-bold text-sm">{{ $p->formatted_price }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>
@endsection
