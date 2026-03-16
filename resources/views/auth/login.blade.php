@extends('layouts.app')

@section('title', 'Đăng Nhập')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-10 h-10 bg-[#C5A572] flex items-center justify-center">
                    <span class="text-black font-bold font-[Outfit]">CW</span>
                </div>
            </div>
            <h1 class="font-[Outfit] font-bold text-3xl text-white mb-2">Chào Mừng Trở Lại</h1>
            <p class="text-gray-500 text-sm">Đăng nhập để tiếp tục mua sắm</p>
        </div>

        <div class="bg-[#111] border border-[#1a1a1a] p-8">
            @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" class="form-input @error('email') border-red-500 @enderror" required autofocus>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">Mật Khẩu</label>
                        <a href="#" class="text-xs text-[#C5A572] hover:underline">Quên mật khẩu?</a>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" class="form-input" required>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 accent-[#C5A572]">
                    <label for="remember" class="text-gray-400 text-sm cursor-pointer">Ghi nhớ tôi</label>
                </div>
                <button type="submit" class="btn-primary w-full py-4">Đăng Nhập</button>
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
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="text-[#C5A572] font-semibold hover:underline ml-1">Đăng Ký Ngay</a>
            </p>
        </div>
    </div>
</div>
@endsection
