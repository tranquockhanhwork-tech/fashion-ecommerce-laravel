@if($errors->any())
<div class="mb-4 p-4 bg-red-50 text-red-800 rounded-lg border border-red-100">
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email đăng nhập *</label>
        <input type="email" id="email" name="email" value="{{ old('email', $employee->user->email ?? '') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ isset($employee) ? 'Mật khẩu mới' : 'Mật khẩu *' }}</label>
        <input type="password" id="password" name="password" {{ isset($employee) ? '' : 'required' }} class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
        @if(isset($employee))
            <p class="text-xs text-gray-400 mt-1">Để trống nếu không đổi mật khẩu.</p>
        @endif
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Vị trí công việc</label>
        <input type="text" id="position" name="position" value="{{ old('position', $employee->position ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Lương</label>
        <input type="number" id="salary" name="salary" value="{{ old('salary', isset($employee) && $employee->salary !== null ? (float) $employee->salary : '') }}" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="hired_at" class="block text-sm font-medium text-gray-700 mb-1">Ngày vào làm</label>
        <input type="date" id="hired_at" name="hired_at" value="{{ old('hired_at', isset($employee) && $employee->hired_at ? $employee->hired_at->format('Y-m-d') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>
</div>

<div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
    <a href="{{ route('admin.employees.index') }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Hủy bỏ</a>
    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#C5A572] rounded-lg hover:bg-[#b09265] transition-colors shadow-sm">
        {{ isset($employee) ? 'Cập nhật nhân viên' : 'Tạo nhân viên' }}
    </button>
</div>
