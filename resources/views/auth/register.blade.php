@extends('layouts.app')

@section('title', 'Đăng Ký')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-10 h-10 bg-[#C5A572] flex items-center justify-center">
                    <span class="text-black font-bold font-[Outfit]">CW</span>
                </div>
            </div>
            <h1 class="font-[Outfit] font-bold text-3xl text-white mb-2">Tạo Tài Khoản</h1>
            <p class="text-gray-500 text-sm">Tham gia CoolWear – Mua sắm phong cách hơn</p>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] p-8">
            @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Họ</label>
                        <input type="text" name="last_name" placeholder="Nguyễn" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Tên</label>
                        <input type="text" name="first_name" placeholder="Văn A" class="form-input" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" class="form-input @error('email') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="form-label">Số Điện Thoại</label>
                    <input type="tel" name="phone" placeholder="0901 234 567" class="form-input">
                </div>
                <div>
                    <label class="form-label">Mật Khẩu</label>
                    <input type="password" name="password" placeholder="Tối thiểu 8 ký tự" class="form-input @error('password') border-red-500 @enderror" required>
                </div>
                <div>
                    <label class="form-label">Xác Nhận Mật Khẩu</label>
                    <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" class="form-input" required>
                </div>
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="terms" name="terms" class="w-4 h-4 accent-[#C5A572] mt-0.5 flex-shrink-0" required>
                    <label for="terms" class="text-gray-400 text-xs leading-relaxed cursor-pointer">
                        Tôi đồng ý với <a href="#" class="text-[#C5A572] hover:underline">Điều Khoản Dịch Vụ</a> và <a href="#" class="text-[#C5A572] hover:underline">Chính Sách Bảo Mật</a> của CoolWear.
                    </label>
                </div>
                <button type="submit" class="btn-primary w-full py-4">Tạo Tài Khoản</button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#2a2a2a]"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-[#111] px-4 text-gray-600 text-xs uppercase tracking-widest">hoặc</span>
                </div>
            </div>

            <p class="text-center text-gray-500 text-sm">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-[#C5A572] font-semibold hover:underline ml-1">Đăng Nhập</a>
            </p>
        </div>
    </div>
</div>
@endsection
