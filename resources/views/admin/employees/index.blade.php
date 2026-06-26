@extends('admin.layouts.app')

@section('title', 'Quản Lí Nhân Viên')
@section('page_title', 'Danh sách Nhân Viên')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Đội ngũ nhân viên</h2>
                <p class="text-sm text-gray-500 mt-1">Quản lý tài khoản đăng nhập nội bộ và hồ sơ nhân sự cơ bản.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <form method="GET" action="{{ route('admin.employees.index') }}" class="flex flex-col gap-3 sm:flex-row">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo tên, email, vị trí..." class="w-full sm:w-80 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#C5A572] focus:ring-0 text-sm">
                    <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                        Tìm kiếm
                    </button>
                </form>

                <a href="{{ route('admin.employees.create') }}" class="px-4 py-2.5 bg-[#C5A572] hover:bg-[#b09265] text-white rounded-lg transition-colors text-sm font-medium text-center">
                    + Thêm nhân viên
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Tổng nhân viên</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalEmployees, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Vào làm năm nay</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($hiredThisYear, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Số vị trí đang có</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($positionsCount, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3">Nhân viên</th>
                        <th class="px-4 py-3">Liên hệ</th>
                        <th class="px-4 py-3">Vị trí</th>
                        <th class="px-4 py-3">Ngày vào làm</th>
                        <th class="px-4 py-3 text-right">Lương</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#C5A572]/15 text-[#9b7f55] font-bold flex items-center justify-center">
                                    {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $employee->full_name }}</p>
                                    <p class="text-xs text-gray-400">#NV{{ str_pad((string) $employee->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-gray-800">{{ $employee->user?->email ?? 'Không có email' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $employee->phone ?: 'Chưa có số điện thoại' }}</p>
                        </td>
                        <td class="px-4 py-4">{{ $employee->position ?: 'Chưa cập nhật' }}</td>
                        <td class="px-4 py-4">{{ $employee->hired_at?->format('d/m/Y') ?? 'Chưa cập nhật' }}</td>
                        <td class="px-4 py-4 text-right font-semibold text-[#C5A572]">
                            {{ $employee->salary !== null ? number_format($employee->salary, 0, ',', '.') . '₫' : 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md font-medium transition-colors">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');">
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
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">Chưa có nhân viên nào phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
