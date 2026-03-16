@extends('admin.layouts.app')

@section('title', 'Quản Lí Đánh Giá')
@section('page_title', 'Danh sách Đánh Giá')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Kiểm duyệt đánh giá sản phẩm</h2>
                <p class="text-sm text-gray-500 mt-1">Theo dõi phản hồi của khách và duyệt những đánh giá phù hợp trước khi hiển thị.</p>
            </div>

            <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-col gap-3 lg:flex-row">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo sản phẩm, khách hoặc nội dung" class="w-full lg:w-80 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm">
                <select name="status" class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                    Lọc
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Tổng đánh giá</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalReviews, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Đã duyệt</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($approvedReviews, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Chờ duyệt</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($pendingReviews, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Điểm trung bình</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($averageRating, 1, ',', '.') }}/5</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Sản phẩm</th>
                        <th class="px-4 py-3">Khách hàng</th>
                        <th class="px-4 py-3">Điểm</th>
                        <th class="px-4 py-3">Nội dung</th>
                        <th class="px-4 py-3">Ngày gửi</th>
                        <th class="px-4 py-3 text-center">Duyệt</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900 line-clamp-1">{{ $review->product?->name ?? 'Sản phẩm đã xóa' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-gray-800">{{ $review->customer?->full_name ?? 'Khách không xác định' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $review->customer?->user?->email ?? 'Không có email' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">
                                {{ $review->rating }}/5
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-gray-900 line-clamp-1">{{ $review->title ?: 'Không có tiêu đề' }}</p>
                            <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $review->comment ?: 'Không có nội dung chi tiết' }}</p>
                        </td>
                        <td class="px-4 py-4">{{ $review->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-4 text-center">
                            <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                                <button type="submit" class="inline-flex px-3 py-1.5 rounded-md text-xs font-semibold {{ $review->is_approved ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }} transition-colors">
                                    {{ $review->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.reviews.show', $review->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md font-medium transition-colors">
                                    Chi tiết
                                </a>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
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
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">Chưa có đánh giá nào phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection
