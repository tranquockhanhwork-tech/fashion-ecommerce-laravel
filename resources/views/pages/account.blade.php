@extends('layouts.app')

@section('title', 'Tài Khoản Của Tôi')

@section('content')
@php $customer = Auth::user()->customer; @endphp
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col md:flex-row gap-8">

        {{-- Sidebar --}}
        <div class="w-full md:w-1/4">
            <div class="bg-[#111] border border-[#1a1a1a] p-6 rounded">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-[#2a2a2a]">
                    <div class="w-12 h-12 bg-[#2a2a2a] rounded-full flex items-center justify-center text-[#C5A572] text-xl font-bold">
                        {{ substr($customer?->full_name ?? Auth::user()->email, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Xin chào,</p>
                        <p class="font-bold text-white">{{ $customer?->full_name ?? Auth::user()->email }}</p>
                    </div>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('account') }}#info" class="block px-4 py-2 text-white bg-[#2a2a2a] rounded transition-colors">Thông tin tài khoản</a>
                    <a href="{{ route('account') }}#addresses" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-[#2a2a2a] rounded transition-colors">Địa chỉ nhận hàng</a>
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-[#2a2a2a] rounded transition-colors">Đơn hàng của tôi</a>
                    <a href="{{ route('wishlist') }}" class="block px-4 py-2 text-gray-400 hover:text-white hover:bg-[#2a2a2a] rounded transition-colors">Sản phẩm yêu thích</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t border-[#2a2a2a]">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded transition-colors">Đăng xuất</button>
                    </form>
                </nav>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="w-full md:w-3/4 space-y-8">

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 p-4 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded text-sm">{{ session('error') }}</div>
            @endif

            {{-- Thông tin tài khoản --}}
            <div id="info" class="bg-[#111] border border-[#1a1a1a] p-8 rounded">
                <h2 class="text-2xl font-bold mb-6 font-[Outfit]">Thông Tin Tài Khoản</h2>
                <form action="{{ route('account.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Họ tên</label>
                            <input type="text" name="full_name" value="{{ $customer?->full_name }}" class="form-input" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="form-input opacity-60" readonly>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ $customer?->phone }}" class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Ngày sinh</label>
                            <input type="date" name="birthday" value="{{ $customer?->birthday }}" class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Giới tính</label>
                            <select name="gender" class="form-input">
                                <option value="" {{ !$customer?->gender ? 'selected' : '' }}>Chưa cập nhật</option>
                                <option value="male" {{ $customer?->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ $customer?->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ $customer?->gender == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary py-3 px-8">Cập Nhật Thông Tin</button>
                </form>
            </div>

            {{-- ===== SỔ ĐỊA CHỈ ===== --}}
            <div id="addresses" class="bg-[#111] border border-[#1a1a1a] p-8 rounded">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold font-[Outfit]">Địa Chỉ Nhận Hàng</h2>
                    <button onclick="document.getElementById('modal-add-address').classList.remove('hidden')"
                            class="btn-primary text-sm py-2 px-5">
                        + Thêm Địa Chỉ
                    </button>
                </div>

                @if($customer && $customer->addresses->isNotEmpty())
                <div class="space-y-4">
                    @foreach($customer->addresses as $addr)
                    <div class="border border-[#2a2a2a] p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 {{ $addr->is_default ? 'border-[#C5A572]/50 bg-[#C5A572]/5' : '' }}">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-white font-semibold">{{ $addr->recipient_name }}</span>
                                <span class="text-gray-500 text-sm">{{ $addr->recipient_phone }}</span>
                                @if($addr->is_default)
                                <span class="text-xs bg-[#C5A572]/20 text-[#C5A572] px-2 py-0.5 rounded-full">Mặc định</span>
                                @endif
                            </div>
                            <p class="text-gray-400 text-sm">{{ $addr->full_address }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if(!$addr->is_default)
                            <form action="{{ route('addresses.default', $addr->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-gray-400 hover:text-[#C5A572] underline transition-colors whitespace-nowrap">Đặt mặc định</button>
                            </form>
                            @endif
                            <form action="{{ route('addresses.destroy', $addr->id) }}" method="POST"
                                  onsubmit="return confirm('Xóa địa chỉ này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 underline transition-colors">Xóa</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p>Bạn chưa có địa chỉ nào. Hãy thêm địa chỉ để thanh toán nhanh hơn!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL THÊM ĐỊA CHỈ ===== --}}
<div id="modal-add-address" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
    <div class="bg-[#111] border border-[#1a1a1a] w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
        <div class="flex items-center justify-between p-6 border-b border-[#1a1a1a] sticky top-0 bg-[#111] z-10">
            <h3 class="font-[Outfit] font-bold text-lg text-white">Thêm Địa Chỉ Mới</h3>
            <button onclick="document.getElementById('modal-add-address').classList.add('hidden')"
                    class="text-gray-500 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form action="{{ route('addresses.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="form-label">Tên người nhận *</label>
                    <input type="text" name="recipient_name" value="{{ $customer?->full_name }}" class="form-input" required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="form-label">Số điện thoại *</label>
                    <input type="tel" name="recipient_phone" value="{{ $customer?->phone }}" class="form-input" required>
                </div>
            </div>

            {{-- Tỉnh --}}
            <div>
                <label class="form-label">Tỉnh / Thành Phố *</label>
                <select id="addr-province" name="province_name" class="form-input" required onchange="onProvinceChange(this)">
                    <option value="">-- Đang tải... --</option>
                </select>
                <input type="hidden" name="province_id" id="addr-province-id">
            </div>
            {{-- Huyện --}}
            <div>
                <label class="form-label">Quận / Huyện *</label>
                <select id="addr-district" name="district_name" class="form-input" required disabled onchange="onDistrictChange(this)">
                    <option value="">-- Chọn tỉnh trước --</option>
                </select>
                <input type="hidden" name="district_id" id="addr-district-id">
            </div>
            {{-- Xã --}}
            <div>
                <label class="form-label">Phường / Xã *</label>
                <select id="addr-ward" name="ward_name" class="form-input" required disabled onchange="onWardChange(this)">
                    <option value="">-- Chọn huyện trước --</option>
                </select>
                <input type="hidden" name="ward_id" id="addr-ward-id">
            </div>

            <div>
                <label class="form-label">Địa chỉ chi tiết *</label>
                <input type="text" name="detailed_address" placeholder="Số nhà, tên đường..." class="form-input" required>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_default" value="1" class="w-4 h-4 accent-[#C5A572]">
                <span class="text-sm text-gray-300">Đặt làm địa chỉ mặc định</span>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-add-address').classList.add('hidden')"
                        class="btn-outline flex-1 py-3 text-sm">Hủy</button>
                <button type="submit" class="btn-primary flex-1 py-3 text-sm">Lưu Địa Chỉ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const APP_URLS = window.appConfig?.urls || {};

async function loadAddrProvinces() {
    const sel = document.getElementById('addr-province');
    const res  = await fetch(APP_URLS.shippingProvinces || `{{ route('shipping.provinces') }}`).catch(() => null);
    if (!res) return;
    const json = await res.json();
    sel.innerHTML = '<option value="">-- Chọn tỉnh/thành --</option>';
    (json.data || []).forEach(p => {
        sel.innerHTML += `<option value="${p.PROVINCE_NAME}" data-id="${p.PROVINCE_ID}">${p.PROVINCE_NAME}</option>`;
    });
}

async function onProvinceChange(sel) {
    const pid   = sel.options[sel.selectedIndex]?.dataset.id;
    document.getElementById('addr-province-id').value = pid || '';
    const dSel  = document.getElementById('addr-district');
    const wSel  = document.getElementById('addr-ward');
    dSel.innerHTML = '<option value="">Đang tải...</option>';
    dSel.disabled  = true;
    wSel.innerHTML = '<option value="">-- Chọn huyện trước --</option>';
    wSel.disabled  = true;
    if (!pid) return;
    const districtsBase = APP_URLS.shippingDistrictsBase || `{{ url('/shipping/districts') }}`;
    const res  = await fetch(`${districtsBase}/${pid}`).catch(() => null);
    if (!res) return;
    const json = await res.json();
    dSel.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
    (json.data || []).forEach(d => {
        dSel.innerHTML += `<option value="${d.DISTRICT_NAME}" data-id="${d.DISTRICT_ID}">${d.DISTRICT_NAME}</option>`;
    });
    dSel.disabled = false;
}

async function onDistrictChange(sel) {
    const did  = sel.options[sel.selectedIndex]?.dataset.id;
    document.getElementById('addr-district-id').value = did || '';
    const wSel = document.getElementById('addr-ward');
    wSel.innerHTML = '<option value="">Đang tải...</option>';
    wSel.disabled  = true;
    if (!did) return;
    const wardsBase = APP_URLS.shippingWardsBase || `{{ url('/shipping/wards') }}`;
    const res  = await fetch(`${wardsBase}/${did}`).catch(() => null);
    if (!res) return;
    const json = await res.json();
    wSel.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
    (json.data || []).forEach(w => {
        wSel.innerHTML += `<option value="${w.WARDS_NAME}" data-id="${w.WARDS_ID}">${w.WARDS_NAME}</option>`;
    });
    wSel.disabled = false;
}

function onWardChange(sel) {
    const wid = sel.options[sel.selectedIndex]?.dataset.id;
    document.getElementById('addr-ward-id').value = wid || '';
}

// Load khi modal mở
document.querySelector('[onclick*="modal-add-address"]')?.addEventListener('click', () => {
    loadAddrProvinces();
});
// Load luôn khi có lỗi validation => modal cần mở sẵn
@if($errors->any())
document.getElementById('modal-add-address').classList.remove('hidden');
loadAddrProvinces();
@endif
</script>
@endpush
