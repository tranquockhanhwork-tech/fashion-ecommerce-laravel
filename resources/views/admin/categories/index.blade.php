@extends('admin.layouts.app')

@section('title', 'Quản Lí Danh Mục')
@section('page_title', 'Danh sách Danh Mục')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Quản lý danh mục sản phẩm</h2>
                <p class="text-sm text-gray-500 mt-1">Tổ chức cây danh mục để quản lý sản phẩm và điều hướng ngoài shop.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col gap-3 sm:flex-row">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm theo tên hoặc slug"
                        class="w-full sm:w-72 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm"
                    >
                    <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                        Tìm kiếm
                    </button>
                </form>

                <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 bg-[#C5A572] hover:bg-[#b09265] text-white rounded-lg transition-colors text-sm font-medium text-center">
                    + Thêm danh mục
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Tổng danh mục</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalCategories, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Danh mục gốc</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($rootCategories, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Có sản phẩm</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($categoriesWithProducts, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Danh mục</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Cha</th>
                        <th class="px-4 py-3 text-center">Danh mục con</th>
                        <th class="px-4 py-3 text-center">Sản phẩm</th>
                        <th class="px-4 py-3 text-center">Thứ tự</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                            <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $category->description ?: 'Chưa có mô tả' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <code class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">{{ $category->slug }}</code>
                        </td>
                        <td class="px-4 py-4">{{ $category->parent?->name ?? 'Danh mục gốc' }}</td>
                        <td class="px-4 py-4 text-center">{{ $category->children_count }}</td>
                        <td class="px-4 py-4 text-center">{{ $category->products_count }}</td>
                        <td class="px-4 py-4 text-center">{{ $category->sort_order }}</td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md font-medium transition-colors">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-medium transition-colors">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">Chưa có danh mục nào phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
