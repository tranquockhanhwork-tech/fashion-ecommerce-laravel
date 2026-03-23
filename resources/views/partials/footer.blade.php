{{-- resources/views/partials/footer.blade.php --}}
<footer class="bg-[#080808] border-t border-[#1a1a1a] pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-[#C5A572] flex items-center justify-center">
                        <span class="text-black font-bold text-sm font-[Outfit]">CW</span>
                    </div>
                    <span class="text-white font-[Outfit] font-bold text-xl tracking-widest">COOL<span class="text-[#C5A572]">WEAR</span></span>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed mb-5">
                    Thương hiệu thời trang cao cấp dành cho những người trẻ tự tin và phong cách. Chất lượng không thoả hiệp.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 border border-[#2a2a2a] flex items-center justify-center text-gray-500 hover:border-[#C5A572] hover:text-[#C5A572] transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 border border-[#2a2a2a] flex items-center justify-center text-gray-500 hover:border-[#C5A572] hover:text-[#C5A572] transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 border border-[#2a2a2a] flex items-center justify-center text-gray-500 hover:border-[#C5A572] hover:text-[#C5A572] transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                </div>
            </div>

            {{-- Links --}}
            <div>
                <h4 class="text-white font-[Outfit] font-semibold text-sm tracking-widest uppercase mb-5">Mua Sắm</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('shop.index') }}" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Tất Cả Sản Phẩm</a></li>
                    <li><a href="{{ route('shop.index') }}?category=men" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Thời Trang Nam</a></li>
                    <li><a href="{{ route('shop.index', ['search' => 'Polo']) }}" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Polo & Casual</a></li>
                    <li><a href="{{ route('shop.index') }}?sale=1" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Giảm Giá</a></li>
                    <li><a href="{{ route('shop.index') }}?new=1" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Hàng Mới</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-[Outfit] font-semibold text-sm tracking-widest uppercase mb-5">Hỗ Trợ</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Về CoolWear</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Liên Hệ</a></li>
                    <li><a href="#" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Chính Sách Đổi Trả</a></li>
                    <li><a href="#" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Hướng Dẫn Size</a></li>
                    <li><a href="#" class="text-gray-500 text-sm hover:text-[#C5A572] transition-colors">Câu Hỏi Thường Gặp</a></li>
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h4 class="text-white font-[Outfit] font-semibold text-sm tracking-widest uppercase mb-5">Nhận Tin Mới</h4>
                <p class="text-gray-500 text-sm mb-4">Đăng ký để nhận ưu đãi độc quyền và bộ sưu tập mới nhất.</p>
                <form id="newsletter-form" class="flex">
                    <input type="email" placeholder="Email của bạn" class="form-input flex-1 text-sm" required>
                    <button type="submit" class="bg-[#C5A572] text-black px-4 flex-shrink-0 hover:bg-[#a8894f] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Bottom --}}
        <div class="border-t border-[#1a1a1a] pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-gray-600 text-sm">© {{ date('Y') }} CoolWear. Bản quyền thuộc về CoolWear.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="text-gray-600 text-xs hover:text-gray-400 transition-colors">Điều Khoản Sử Dụng</a>
                <a href="#" class="text-gray-600 text-xs hover:text-gray-400 transition-colors">Chính Sách Bảo Mật</a>
                <div class="flex items-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-4 opacity-40">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-5 opacity-40">
                </div>
            </div>
        </div>
    </div>
</footer>
