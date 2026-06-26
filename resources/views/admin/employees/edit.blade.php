@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Nhân Viên')
@section('page_title', 'Chỉnh Sửa Nhân Viên')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Cập nhật nhân viên: {{ $employee->full_name }}</h2>
        <a href="{{ route('admin.employees.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
            &larr; Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.employees._form')
    </form>
</div>
@endsection
