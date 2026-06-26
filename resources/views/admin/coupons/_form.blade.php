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
        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã coupon *</label>
        <input type="text" id="code" name="code" value="{{ old('code', $coupon->code ?? '') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border uppercase">
    </div>

    <div>
        <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá *</label>
        <select id="discount_type" name="discount_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border bg-white">
            <option value="fixed" {{ old('discount_type', $coupon->discount_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Giảm số tiền cố định</option>
            <option value="percentage" {{ old('discount_type', $coupon->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Giảm theo phần trăm</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
        <input type="text" id="description" name="description" value="{{ old('description', $coupon->description ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Giá trị giảm *</label>
        <input type="number" id="discount_value" name="discount_value" value="{{ old('discount_value', isset($coupon) ? (float) $coupon->discount_value : '') }}" min="0" step="0.01" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">Đơn tối thiểu</label>
        <input type="number" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', isset($coupon) ? (float) $coupon->min_order_amount : 0) }}" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="max_discount_amount" class="block text-sm font-medium text-gray-700 mb-1">Giảm tối đa</label>
        <input type="number" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', isset($coupon) ? $coupon->max_discount_amount : '') }}" min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">Giới hạn lượt dùng</label>
        <input type="number" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="used_count" class="block text-sm font-medium text-gray-700 mb-1">Đã dùng</label>
        <input type="number" id="used_count" name="used_count" value="{{ old('used_count', $coupon->used_count ?? 0) }}" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Bắt đầu</label>
        <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\\TH:i') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div>
        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Hết hạn</label>
        <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\\TH:i') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#C5A572] focus:ring-[#C5A572] sm:text-sm p-2 border">
    </div>

    <div class="md:col-span-2 flex items-center mt-2 pl-1">
        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 text-[#C5A572] bg-gray-100 border-gray-300 rounded focus:ring-[#C5A572] cursor-pointer">
        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 cursor-pointer">Cho phép sử dụng coupon</label>
    </div>
</div>

<div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
    <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Hủy bỏ</a>
    <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#C5A572] rounded-lg hover:bg-[#b09265] transition-colors shadow-sm">
        {{ isset($coupon) ? 'Cập nhật coupon' : 'Lưu coupon' }}
    </button>
</div>
