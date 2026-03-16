@extends('admin.layouts.app')

@section('title', 'Quản Lí Sản Phẩm')
@section('page_title', 'Danh sách Sản Phẩm')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 overflow-hidden flex flex-col">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-gray-800">Tất Cả Sản Phẩm</h2>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-[#C5A572] hover:bg-[#b09265] text-white rounded transition-colors text-sm font-medium">
            + Thêm Mới
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 text-green-800 border-l-4 border-green-500 rounded text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Hình Ảnh</th>
                    <th class="px-4 py-3">Tên Sản Phẩm</th>
                    <th class="px-4 py-3">Danh Mục</th>
                    <th class="px-4 py-3">Giá Bán</th>
                    <th class="px-4 py-3">Trạng Thái</th>
                    <th class="px-4 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $product->id }}</td>
                    <td class="px-4 py-3">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_url) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">No Img</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                    <td class="px-4 py-3">{{ $product->category->name ?? 'Không phân loại' }}</td>
                    <td class="px-4 py-3 text-[#C5A572] font-semibold">{{ number_format($product->price, 0, ',', '.') }}₫</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Đang bán' : 'Ẩn' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md font-medium transition-colors cursor-pointer">
                                Sửa
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="m-0 inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-medium transition-colors cursor-pointer">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">Chưa có dữ liệu sản phẩm.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
