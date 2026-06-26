@extends('admin.layouts.app')

@section('title', 'Tạo Mã Giảm Giá')
@section('page_title', 'Tạo Mã Giảm Giá')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Thông tin voucher mới</h2>
        <a href="{{ route('admin.coupons.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
            &larr; Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        @include('admin.coupons._form')
    </form>
</div>
@endsection
