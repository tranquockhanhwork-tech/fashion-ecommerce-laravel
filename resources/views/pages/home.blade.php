@extends('layouts.app')

@section('title', 'Trang Chủ')
@section('meta_description', 'CoolWear – Thời trang cao cấp, phong cách hiện đại. Khám phá bộ sưu tập mới nhất.')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="hero-section">
    <div class="hero-bg"></div>
    {{-- Decorative lines --}}
    <div class="absolute inset-0 opacity-5" style="background-image:repeating-linear-gradient(90deg,#C5A572 0 1px,transparent 1px 80px)"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center py-20">
            <div>
                <div class="section-label mb-4">New Collection 2025</div>
                <h1 class="font-[Outfit] font-bold text-5xl sm:text-6xl xl:text-7xl text-white leading-[1.05] mb-6">
                    Phong Cách<br>
                    <span class="text-[#C5A572]">Không Giới Hạn</span>
                </h1>
                <p class="text-gray-400 text-lg leading-relaxed mb-8 max-w-lg">
                    Khám phá bộ sưu tập thời trang cao cấp được thiết kế cho những người trẻ tự tin và đầy phong cách. Chất lượng vượt trội – Phong cách độc đáo.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('shop.index') }}" class="btn-primary px-8 py-4 text-sm">
                        Mua Sắm Ngay
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('about') }}" class="btn-outline px-8 py-4 text-sm">Về Chúng Tôi</a>
                </div>
                {{-- Stats --}}
                <div class="flex gap-10 mt-12 pt-10 border-t border-[#1a1a1a]">
                    <div>
                        <div class="font-[Outfit] font-bold text-3xl text-white">500+</div>
                        <div class="text-gray-500 text-xs uppercase tracking-widest mt-1">Sản Phẩm</div>
                    </div>
                    <div>
                        <div class="font-[Outfit] font-bold text-3xl text-white">50K+</div>
                        <div class="text-gray-500 text-xs uppercase tracking-widest mt-1">Khách Hàng</div>
                    </div>
                    <div>
                        <div class="font-[Outfit] font-bold text-3xl text-white">4.9★</div>
                        <div class="text-gray-500 text-xs uppercase tracking-widest mt-1">Đánh Giá</div>
                    </div>
                </div>
            </div>

            {{-- Hero Visual --}}
            <div class="relative hidden lg:block">
                <div class="relative">
                    {{-- Main product card mock --}}
                    <div class="w-full aspect-[4/5] bg-[#111] border border-[#1a1a1a] overflow-hidden relative group">
                        <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80" alt="Hero Fashion" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="bg-[#111]/80 backdrop-blur-sm border border-[#2a2a2a] p-4">
                                <div class="text-xs text-[#C5A572] uppercase tracking-widest mb-1">Bestseller</div>
                                <div class="font-[Outfit] font-semibold text-white text-lg">Premium Streetwear Set</div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-[#C5A572] font-bold text-xl">1.290.000₫</span>
                                    <button class="btn-primary py-2 px-4 text-xs" data-add-to-cart data-product-name="Premium Streetwear Set">Thêm Vào Giỏ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Floating badge --}}
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-[#C5A572] rounded-full flex flex-col items-center justify-center text-black">
                        <span class="font-[Outfit] font-bold text-xl leading-none">30%</span>
                        <span class="text-xs font-bold">OFF</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-gray-600">
        <span class="text-xs uppercase tracking-widest">Cuộn xuống</span>
        <div class="w-px h-8 bg-gradient-to-b from-[#C5A572] to-transparent"></div>
    </div>
</section>

{{-- ===== MARQUEE BANNER ===== --}}
<div class="bg-[#C5A572] py-3 overflow-hidden">
    <div class="flex gap-12 animate-[marquee_20s_linear_infinite] whitespace-nowrap">
        @foreach(range(1,6) as $i)
        <span class="text-black text-xs font-bold uppercase tracking-widest flex items-center gap-4">
            Free Ship Đơn Từ 1 Triệu
            <span class="w-1.5 h-1.5 bg-black/30 rounded-full inline-block"></span>
            Đổi Trả Miễn Phí 30 Ngày
            <span class="w-1.5 h-1.5 bg-black/30 rounded-full inline-block"></span>
            Hàng Chính Hãng 100%
            <span class="w-1.5 h-1.5 bg-black/30 rounded-full inline-block"></span>
        </span>
        @endforeach
    </div>
</div>
@push('styles')
<style>
@keyframes marquee { from {transform:translateX(0)} to {transform:translateX(-50%)} }
</style>
@endpush

{{-- ===== CATEGORIES ===== --}}
<section class="py-20 bg-[#0A0A0A]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <div class="section-label">Danh Mục</div>
                <h2 class="section-title">Khám Phá<br>Phong Cách Của Bạn</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="hidden md:flex items-center gap-2 text-sm text-[#C5A572] hover:gap-3 transition-all uppercase tracking-wider font-semibold">
                Xem Tất Cả
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Men --}}
            <a href="{{ route('shop.index') }}?category=men" class="category-card group">
                <img src="https://images.unsplash.com/photo-1617127365659-c47fa864d8bc?w=600&q=80" alt="Thời Trang Nam" loading="lazy">
                <div class="category-card-overlay"></div>
                <div class="category-card-content">
                    <div class="text-[#C5A572] text-xs uppercase tracking-widest mb-2">Bộ Sưu Tập</div>
                    <h3 class="font-[Outfit] font-bold text-2xl text-white mb-3">Thời Trang Nam</h3>
                    <div class="flex items-center gap-2 text-white/70 text-sm group-hover:text-[#C5A572] transition-colors">
                        Khám phá
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Women --}}
            <a href="{{ route('shop.index') }}?category=women" class="category-card group">
                <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80" alt="Thời Trang Nữ" loading="lazy">
                <div class="category-card-overlay"></div>
                <div class="category-card-content">
                    <div class="text-[#C5A572] text-xs uppercase tracking-widest mb-2">Bộ Sưu Tập</div>
                    <h3 class="font-[Outfit] font-bold text-2xl text-white mb-3">Thời Trang Nữ</h3>
                    <div class="flex items-center gap-2 text-white/70 text-sm group-hover:text-[#C5A572] transition-colors">
                        Khám phá
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Accessories --}}
            <a href="{{ route('shop.index') }}?category=accessories" class="category-card group">
                <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&q=80" alt="Phụ Kiện" loading="lazy">
                <div class="category-card-overlay"></div>
                <div class="category-card-content">
                    <div class="text-[#C5A572] text-xs uppercase tracking-widest mb-2">Bộ Sưu Tập</div>
                    <h3 class="font-[Outfit] font-bold text-2xl text-white mb-3">Phụ Kiện</h3>
                    <div class="flex items-center gap-2 text-white/70 text-sm group-hover:text-[#C5A572] transition-colors">
                        Khám phá
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- ===== TRENDING PRODUCTS ===== --}}
<section class="py-20 bg-[#080808]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <div class="section-label">Đang Hot</div>
                <h2 class="section-title">Sản Phẩm<br>Bán Chạy Nhất</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="hidden md:flex items-center gap-2 text-sm text-[#C5A572] hover:gap-3 transition-all uppercase tracking-wider font-semibold">
                Xem Tất Cả
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($featuredProducts as $product)
            <div class="product-card group">
                <div class="product-card-image">
                    <a href="{{ route('shop.show', $product->id) }}" class="block h-full">
                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                    </a>

                    {{-- Badge giảm giá --}}
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
                        <button class="btn-primary flex-1 py-2.5 text-xs" data-add-to-cart data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">Thêm Vào Giỏ</button>
                        @else
                        <a href="{{ route('login') }}" class="btn-primary flex-1 py-2.5 text-xs flex items-center justify-center text-center">Thêm Vào Giỏ</a>
                        @endauth
                        <a href="{{ route('shop.show', $product->id) }}" class="btn-outline py-2.5 px-3 text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <a href="{{ route('shop.show', $product->id) }}" class="block">
                        <h3 class="font-[Outfit] font-medium text-sm text-white hover:text-[#C5A572] transition-colors mb-2 line-clamp-1">{{ $product->name }}</h3>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="text-[#C5A572] font-bold text-base">{{ $product->formatted_price }}</span>
                        @if($product->promotional_price)
                        <span class="text-gray-600 text-sm line-through">{{ $product->formatted_original_price }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-0.5 mt-2">
                        @php $rating = (int) round($product->average_rating ?: 4); @endphp
                        @foreach(range(1,5) as $s)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 {{ $s <= $rating ? 'text-amber-400' : 'text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endforeach
                        <span class="text-gray-600 text-xs ml-1">({{ $product->reviews->count() }})</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-4 text-center text-gray-500 py-12">Chưa có sản phẩm nổi bật.</div>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('shop.index') }}" class="btn-outline px-12 py-4">Xem Thêm Sản Phẩm</a>
        </div>
    </div>
</section>

{{-- ===== BRAND STORY ===== --}}
<section class="py-20 bg-[#0A0A0A]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="aspect-square bg-[#111] overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=700&q=80" alt="CoolWear Story" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -right-6 w-48 h-48 bg-[#C5A572]/10 border border-[#C5A572]/30 hidden lg:block"></div>
                <div class="absolute -top-6 -left-6 w-24 h-24 bg-[#C5A572] flex items-center justify-center hidden lg:flex">
                    <div class="text-center text-black">
                        <div class="font-[Outfit] font-bold text-2xl">10+</div>
                        <div class="text-xs font-semibold">NĂM</div>
                    </div>
                </div>
            </div>
            <div>
                <div class="section-label">Câu Chuyện Của Chúng Tôi</div>
                <h2 class="section-title mb-6">Đam Mê Tạo Nên<br><span class="text-[#C5A572]">Phong Cách</span></h2>
                <div class="gold-divider"></div>
                <p class="text-gray-400 leading-relaxed mb-6">
                    CoolWear được thành lập năm 2015 với một sứ mệnh đơn giản: tạo ra những bộ trang phục chất lượng cao, thiết kế độc đáo mà mọi người có thể tự tin mặc hàng ngày.
                </p>
                <p class="text-gray-400 leading-relaxed mb-8">
                    Chúng tôi tin rằng thời trang không chỉ là quần áo – đó là cách bạn thể hiện cá tính và kể câu chuyện của bản thân. Mỗi sản phẩm đều được chăm chút từng chi tiết với chất liệu cao cấp nhất.
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="border-l-2 border-[#C5A572] pl-4">
                        <div class="font-[Outfit] font-bold text-2xl text-white">100%</div>
                        <div class="text-gray-500 text-sm mt-1">Chất Liệu Tự Nhiên</div>
                    </div>
                    <div class="border-l-2 border-[#C5A572] pl-4">
                        <div class="font-[Outfit] font-bold text-2xl text-white">Zero</div>
                        <div class="text-gray-500 text-sm mt-1">Tác Động Môi Trường</div>
                    </div>
                </div>
                <a href="{{ route('about') }}" class="btn-primary px-8 py-4">Khám Phá Thêm</a>
            </div>
        </div>
    </div>
</section>

{{-- ===== FEATURES ===== --}}
<section class="py-16 bg-[#111] border-y border-[#1a1a1a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            @php
            $features = [
                ['icon'=>'🚚','title'=>'Giao Hàng Miễn Phí','desc'=>'Từ đơn hàng 1.000.000₫'],
                ['icon'=>'🔄','title'=>'Đổi Trả 30 Ngày','desc'=>'Không câu hỏi, hoàn tiền nhanh'],
                ['icon'=>'🛡️','title'=>'Hàng Chính Hãng','desc'=>'100% sản phẩm authentic'],
                ['icon'=>'💬','title'=>'Hỗ Trợ 24/7','desc'=>'Luôn sẵn sàng giúp đỡ bạn'],
            ];
            @endphp
            @foreach($features as $f)
            <div class="flex flex-col items-center text-center group">
                <div class="w-14 h-14 bg-[#1a1a1a] border border-[#2a2a2a] flex items-center justify-center text-2xl mb-4 group-hover:border-[#C5A572] transition-colors">
                    {{ $f['icon'] }}
                </div>
                <h4 class="font-[Outfit] font-semibold text-sm text-white uppercase tracking-wide mb-1">{{ $f['title'] }}</h4>
                <p class="text-gray-500 text-xs">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== NEWSLETTER ===== --}}
<section class="py-20 bg-[#080808] relative overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background-image:radial-gradient(circle at 50% 50%,#C5A572 1px,transparent 1px);background-size:40px 40px;"></div>
    <div class="relative max-w-2xl mx-auto px-4 text-center">
        <div class="section-label text-center mb-4">Newsletter</div>
        <h2 class="section-title mb-4">Đừng Bỏ Lỡ<br><span class="text-[#C5A572]">Ưu Đãi Độc Quyền</span></h2>
        <p class="text-gray-400 mb-8">Đăng ký nhận bản tin để nhận ưu đãi sớm nhất, bộ sưu tập mới và voucher giảm 10% cho đơn đầu tiên.</p>
        <form id="newsletter-form" class="flex flex-col sm:flex-row gap-0 max-w-md mx-auto">
            <input type="email" placeholder="Nhập email của bạn..." class="form-input flex-1 sm:rounded-none" required>
            <button type="submit" class="btn-primary sm:rounded-none px-8 whitespace-nowrap">Đăng Ký</button>
        </form>
        <p class="text-gray-600 text-xs mt-4">Chúng tôi tôn trọng quyền riêng tư của bạn. Hủy đăng ký bất kỳ lúc nào.</p>
    </div>
</section>

{{-- ===== TESTIMONIALS ===== --}}
<section class="py-20 bg-[#0A0A0A]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="section-label text-center">Đánh Giá Khách Hàng</div>
            <h2 class="section-title">Họ Nói Gì Về<br><span class="text-[#C5A572]">CoolWear?</span></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $reviews = [
                ['name'=>'Nguyễn Minh Tuấn','role'=>'Khách hàng từ 2022','rating'=>5,'text'=>'Chất lượng vượt trội, giao hàng nhanh. CoolWear là thương hiệu yêu thích của tôi khi cần mặc đẹp mà không cần tốn quá nhiều tiền.'],
                ['name'=>'Trần Thị Linh','role'=>'Fashion Blogger','rating'=>5,'text'=>'Tôi đã mua nhiều lần và lần nào cũng ưng ý. Thiết kế trendy, chất liệu thật sự thoải mái và bền. Sẽ tiếp tục ủng hộ!'],
                ['name'=>'Lê Đức Anh','role'=>'Khách hàng thân thiết','rating'=>5,'text'=>'Dịch vụ đổi trả tuyệt vời, nhân viên nhiệt tình. Mua lần đầu nhưng chắc chắn sẽ quay lại vì sản phẩm đẹp quá!'],
            ];
            @endphp
            @foreach($reviews as $review)
            <div class="bg-[#111] border border-[#1a1a1a] p-6 hover:border-[#C5A572]/50 transition-colors">
                <div class="flex items-center gap-0.5 mb-4">
                    @foreach(range(1,5) as $s)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endforeach
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-5 italic">"{{ $review['text'] }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#C5A572] flex items-center justify-center font-[Outfit] font-bold text-black">
                        {{ substr($review['name'], 0, 1) }}
                    </div>
                    <div>
                        <div class="font-[Outfit] font-semibold text-sm text-white">{{ $review['name'] }}</div>
                        <div class="text-gray-600 text-xs">{{ $review['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
