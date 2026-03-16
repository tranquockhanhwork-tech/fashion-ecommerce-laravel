@extends('layouts.app')

@section('title', 'Liên Hệ')

@section('content')
<div class="bg-[#080808] border-b border-[#1a1a1a] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="breadcrumb mb-4">
            <a href="{{ route('home') }}">Trang Chủ</a>
            <span class="breadcrumb-sep">/</span>
            <span class="text-[#C5A572]">Liên Hệ</span>
        </div>
        <h1 class="font-[Outfit] font-bold text-4xl text-white">Liên Hệ Với Chúng Tôi</h1>
    </div>
</div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid lg:grid-cols-2 gap-16">
        <div>
            <div class="section-label mb-4">Gửi Tin Nhắn</div>
            <h2 class="section-title text-3xl mb-8">Chúng Tôi Luôn<br><span class="text-[#C5A572]">Lắng Nghe Bạn</span></h2>
            <form class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Họ Tên</label>
                        <input type="text" placeholder="Nguyễn Văn A" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" placeholder="email@example.com" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Chủ Đề</label>
                    <select class="form-input">
                        <option>Hỗ trợ đơn hàng</option>
                        <option>Đổi trả sản phẩm</option>
                        <option>Hợp tác kinh doanh</option>
                        <option>Khác</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tin Nhắn</label>
                    <textarea rows="5" placeholder="Nội dung tin nhắn..." class="form-input resize-none"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full py-4">Gửi Tin Nhắn</button>
            </form>
        </div>
        <div>
            <div class="section-label mb-4">Thông Tin Liên Hệ</div>
            <h2 class="section-title text-3xl mb-8">Tìm Chúng Tôi<br><span class="text-[#C5A572]">Tại Đây</span></h2>
            <div class="space-y-6">
                @foreach([
                    ['📍','Địa Chỉ','123 Đường Thời Trang, Phường An Thới, Quận Bình Thuỷ, Cần Thơ'],
                    ['📞','Điện Thoại','1900 1234 | 0901 234 567'],
                    ['📧','Email','hello@coolwear.vn'],
                    ['⏰','Giờ Làm Việc','T2 – CN: 8:00 – 21:00'],
                ] as [$icon,$label,$val])
                <div class="flex gap-4 bg-[#111] border border-[#1a1a1a] p-5 hover:border-[#C5A572]/50 transition-colors">
                    <div class="text-2xl flex-shrink-0">{{ $icon }}</div>
                    <div>
                        <div class="text-xs text-[#C5A572] uppercase tracking-widest font-bold mb-1">{{ $label }}</div>
                        <div class="text-gray-300 text-sm">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
