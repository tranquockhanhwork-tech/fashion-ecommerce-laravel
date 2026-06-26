@extends('admin.layouts.app')

@section('title', 'Thêm Danh Mục')
@section('page_title', 'Thêm Danh Mục')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Thông tin danh mục mới</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
            &larr; Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories._form')
    </form>
</div>
@endsection
