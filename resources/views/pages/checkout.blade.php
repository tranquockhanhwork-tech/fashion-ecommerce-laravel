@extends('layouts.app')

@section('title', 'Thanh Toán')

@section('content')
<div class="min-h-screen bg-[#0A0A0A]">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="breadcrumb mb-8">
        <a href="{{ route('home') }}">Trang Chủ</a>
        <span class="breadcrumb-sep">/</span>
        <a href="{{ route('cart.index') }}">Giỏ Hàng</a>
        <span class="breadcrumb-sep">/</span>
        <span class="text-[#C5A572]">Thanh Toán</span>
    </div>

    <h1 class="font-[Outfit] font-bold text-3xl text-white mb-10">Thanh Toán</h1>

    {{-- Steps --}}
    <div class="flex items-center gap-4 mb-10">
        @foreach(['Giỏ Hàng','Thanh Toán','Xác Nhận'] as $step => $label)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step < 2 ? 'bg-[#C5A572] text-black' : 'bg-[#1a1a1a] text-gray-600 border border-[#2a2a2a]' }}">{{ $step+1 }}</div>
            <span class="text-sm {{ $step == 1 ? 'text-[#C5A572] font-semibold' : 'text-gray-500' }}">{{ $label }}</span>
        </div>
        @if($step < 2)
        <div class="flex-1 h-px bg-[#2a2a2a]"></div>
        @endif
        @endforeach
    </div>

    <form action="{{ route('checkout.store') }}" method="POST">
    @csrf
    <div class="grid lg:grid-cols-3 gap-10">

        {{-- Form thông tin --}}
        <div class="lg:col-span-2 space-y-8">


            {{-- Thông tin nhận hàng --}}
            <div class="bg-[#111] border border-[#1a1a1a] p-6">
                <h2 class="font-[Outfit] font-semibold text-lg text-white mb-6 flex items-center gap-3">
                    <span class="w-7 h-7 bg-[#C5A572] text-black text-sm font-bold flex items-center justify-center">1</span>
                    Địa Chỉ Nhận Hàng
                </h2>

                @if($customer && $customer->addresses->isNotEmpty())
                {{-- Có địa chỉ đã lưu: chỉ cần chọn --}}
                <div class="space-y-3 mb-5" id="saved-addresses">
                    @foreach($customer->addresses as $addr)
                    <label class="flex items-start gap-4 p-4 border cursor-pointer transition-colors
                           {{ $addr->is_default ? 'border-[#C5A572]/60 bg-[#C5A572]/5' : 'border-[#2a2a2a] hover:border-[#C5A572]/40' }}">
                        <input type="radio" name="address_id" value="{{ $addr->id }}"
                               {{ $addr->is_default ? 'checked' : '' }}
                               class="mt-0.5 accent-[#C5A572] flex-shrink-0"
                               data-address="{{ json_encode([
                                   'recipient_name'  => $addr->recipient_name,
                                   'recipient_phone' => $addr->recipient_phone,
                                   'province_id'     => $addr->province_id,
                                   'province_name'   => $addr->province_name,
                                   'district_id'     => $addr->district_id,
                                   'district_name'   => $addr->district_name,
                                   'ward_id'         => $addr->ward_id,
                                   'ward_name'       => $addr->ward_name,
                                   'detailed_address'=> $addr->detailed_address,
                                   'full_address'    => $addr->full_address,
                               ]) }}"
                               onchange="onSelectAddress(this)">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-white font-semibold text-sm">{{ $addr->recipient_name }}</span>
                                <span class="text-gray-500 text-xs">{{ $addr->recipient_phone }}</span>
                                @if($addr->is_default)
                                <span class="text-xs bg-[#C5A572]/20 text-[#C5A572] px-2 py-0.5 rounded-full">Mặc định</span>
                                @endif
                            </div>
                            <p class="text-gray-400 text-xs mt-1">{{ $addr->full_address }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                <a href="{{ route('account') }}#addresses"
                   class="inline-flex items-center gap-1 text-xs text-[#C5A572] hover:underline mb-5">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Thêm địa chỉ mới
                </a>

                {{-- Hidden inputs để gửi lên server khi submit form --}}
                <input type="hidden" name="recipient_name"   id="h_name">
                <input type="hidden" name="phone"            id="h_phone">
                <input type="hidden" name="email"            value="{{ Auth::user()->email }}">
                <input type="hidden" name="city"             id="h_province">
                <input type="hidden" name="district"         id="h_district">
                <input type="hidden" name="ward"             id="h_ward">
                <input type="hidden" name="address"          id="h_detail">
                <input type="hidden" name="province_id"      id="h_province_id">
                <input type="hidden" name="district_id"      id="h_district_id">
                <input type="hidden" name="ward_id"          id="h_ward_id">

                {{-- Tính phí ship tự động sau khi chọn địa chỉ --}}
                <div class="bg-[#0d0d0d] border border-[#2a2a2a] p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-white text-sm font-semibold">Phí Vận Chuyển (Viettel Post)</span>
                        <button type="button" onclick="calcShippingFee()" class="btn-outline text-xs py-1.5 px-4">Tính Lại</button>
                    </div>
                    <div id="fee-loading" class="hidden text-gray-500 text-xs">⏳ Đang tính phí vận chuyển...</div>
                    <div id="fee-result" class="hidden space-y-2"></div>
                    <input type="hidden" name="shipping_fee" id="hidden_shipping_fee" value="0">
                    <input type="hidden" name="service_code" id="hidden_service_code" value="">
                </div>

                <div class="mt-4">
                    <label class="form-label">Ghi Chú (Tùy chọn)</label>
                    <textarea name="note" rows="2" placeholder="Ghi chú cho người giao hàng..." class="form-input resize-none">{{ old('note') }}</textarea>
                </div>

                @else
                {{-- Chưa có địa chỉ: nhập form bình thường --}}
                <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 p-3 rounded text-sm mb-4">
                    Bạn chưa có địa chỉ nào. Hãy
                    <a href="{{ route('account') }}#addresses" class="underline font-semibold">thêm địa chỉ nhận hàng</a>
                    trong trang Tài Khoản để thanh toán nhanh hơn!
                    <br>Hoặc tiếp tục nhập thủ công bên dưới:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Họ Tên *</label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name', $customer?->full_name) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Số Điện Thoại *</label>
                        <input type="tel" name="phone" value="{{ old('phone', $customer?->phone) }}" class="form-input" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-input" required>
                    </div>
                    {{-- Tỉnh / Huyện / Xã động --}}
                    <div>
                        <label class="form-label">Tỉnh / Thành Phố *</label>
                        <select id="vtp-province" name="city" class="form-input" required>
                            <option value="">-- Đang tải... --</option>
                        </select>
                        <input type="hidden" name="province_id" id="province_id">
                    </div>
                    <div>
                        <label class="form-label">Quận / Huyện *</label>
                        <select id="vtp-district" name="district" class="form-input" required disabled>
                            <option value="">-- Chọn tỉnh trước --</option>
                        </select>
                        <input type="hidden" name="district_id" id="district_id">
                    </div>
                    <div>
                        <label class="form-label">Phường / Xã *</label>
                        <select id="vtp-ward" name="ward" class="form-input" required disabled>
                            <option value="">-- Chọn huyện trước --</option>
                        </select>
                        <input type="hidden" name="ward_id" id="ward_id">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Địa Chỉ Chi Tiết *</label>
                        <input type="text" name="address" id="detail_address" value="{{ old('address') }}" class="form-input" required>
                    </div>
                    <div class="sm:col-span-2 bg-[#0d0d0d] border border-[#2a2a2a] p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-white text-sm font-semibold">Phí Vận Chuyển (Viettel Post)</span>
                            <button type="button" onclick="calcShippingFee()" class="btn-outline text-xs py-1.5 px-4">Tính Phí</button>
                        </div>
                        <div id="fee-loading" class="hidden text-gray-500 text-xs">⏳ Đang tính...</div>
                        <div id="fee-result" class="hidden space-y-2"></div>
                        <input type="hidden" name="shipping_fee" id="hidden_shipping_fee" value="0">
                        <input type="hidden" name="service_code" id="hidden_service_code" value="">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Ghi Chú (Tùy chọn)</label>
                        <textarea name="note" rows="2" placeholder="Ghi chú cho người giao hàng..." class="form-input resize-none">{{ old('note') }}</textarea>
                    </div>
                </div>
                @endif
            </div>




            {{-- Phương thức thanh toán --}}
            <div class="bg-[#111] border border-[#1a1a1a] p-6">
                <h2 class="font-[Outfit] font-semibold text-lg text-white mb-6 flex items-center gap-3">
                    <span class="w-7 h-7 bg-[#C5A572] text-black text-sm font-bold flex items-center justify-center">2</span>
                    Phương Thức Thanh Toán
                </h2>
                <div class="space-y-3">
                    @foreach([
                        ['value'=>'cod',   'label'=>'Thanh Toán Khi Nhận Hàng (COD)', 'icon'=>'💵'],
                        ['value'=>'bank',  'label'=>'Chuyển Khoản Ngân Hàng',         'icon'=>'🏦'],
                        ['value'=>'momo',  'label'=>'Ví MoMo',                         'icon'=>'🟣'],
                        ['value'=>'vnpay', 'label'=>'VNPay',                           'icon'=>'💳'],
                    ] as $method)
                    <label class="flex items-center gap-4 p-4 border border-[#2a2a2a] cursor-pointer hover:border-[#C5A572] transition-colors group has-[:checked]:border-[#C5A572] has-[:checked]:bg-[#C5A572]/5">
                        <input type="radio" name="payment_method" value="{{ $method['value'] }}"
                               {{ (old('payment_method', 'cod') == $method['value']) ? 'checked' : '' }}
                               class="w-4 h-4 accent-[#C5A572]">
                        <span class="text-xl">{{ $method['icon'] }}</span>
                        <span class="text-sm text-gray-300 group-hover:text-white transition-colors">{{ $method['label'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tóm tắt đơn hàng từ DB --}}
        <div class="lg:col-span-1">
            <div class="bg-[#111] border border-[#1a1a1a] p-6 sticky top-24">
                <h2 class="font-[Outfit] font-semibold text-lg text-white mb-6 pb-4 border-b border-[#1a1a1a]">
                    Đơn Hàng ({{ $cartItems->count() }} sản phẩm)
                </h2>

                <div class="space-y-4 mb-5 max-h-72 overflow-y-auto pr-1">
                    @foreach($cartItems as $ci)
                    @php
                        $basePrice = $ci->variant->product->promotional_price ?: $ci->variant->product->price;
                        $itemPrice = $ci->variant->price_override ?: $basePrice;
                    @endphp
                    <div class="flex gap-3 items-center">
                        <img src="{{ $ci->variant->product->thumbnail }}"
                             alt="{{ $ci->variant->product->name }}"
                             class="w-12 h-14 object-cover bg-[#1a1a1a] flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-white text-xs font-medium line-clamp-1">{{ $ci->variant->product->name }}</div>
                            <div class="text-gray-500 text-xs mt-0.5">
                                @if($ci->variant->size) {{ $ci->variant->size }} @endif
                                @if($ci->variant->color) / {{ $ci->variant->color }} @endif
                                × {{ $ci->quantity }}
                            </div>
                        </div>
                        <span class="text-[#C5A572] text-sm font-bold flex-shrink-0">
                            {{ number_format($itemPrice * $ci->quantity, 0, ',', '.') }}₫
                        </span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-[#2a2a2a] pt-4 space-y-2 mb-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Tạm tính</span>
                        <span class="text-white" id="order-subtotal" data-raw="{{ $cartTotal }}">{{ number_format($cartTotal, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Vận chuyển</span>
                        <span class="text-green-400 text-xs" id="order-shipping">Miễn Phí</span>
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-[#2a2a2a] pt-4 mb-6">
                    <span class="font-[Outfit] font-semibold text-white">Tổng Cộng</span>
                    <span class="font-[Outfit] font-bold text-[#C5A572] text-xl" id="order-total">{{ number_format($cartTotal, 0, ',', '.') }}₫</span>
                </div>

                <button type="submit" class="btn-primary w-full py-4 text-sm flex items-center justify-center gap-2">
                    Đặt Hàng Ngay
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
                <p class="text-gray-600 text-xs text-center mt-3">
                    Bằng cách đặt hàng, bạn đồng ý với <a href="#" class="text-[#C5A572] hover:underline">Điều Khoản</a> của chúng tôi.
                </p>
            </div>
        </div>
    </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
const APP_URLS = window.appConfig?.urls || {};

/* ===== CHỌN ĐỊA CHỈ ĐÃ LƯU ===== */
let _currentAddress = null;

function onSelectAddress(radio) {
    _currentAddress = JSON.parse(radio.dataset.address);
    document.getElementById('h_name').value        = _currentAddress.recipient_name;
    document.getElementById('h_phone').value       = _currentAddress.recipient_phone;
    document.getElementById('h_province').value    = _currentAddress.province_name;
    document.getElementById('h_district').value    = _currentAddress.district_name;
    document.getElementById('h_ward').value        = _currentAddress.ward_name;
    document.getElementById('h_detail').value      = _currentAddress.detailed_address;
    document.getElementById('h_province_id').value = _currentAddress.province_id;
    document.getElementById('h_district_id').value = _currentAddress.district_id;
    document.getElementById('h_ward_id').value     = _currentAddress.ward_id;
    calcShippingFee();
}

/* ===== TÍNH PHÍ VẬN CHUYỂN ===== */
async function calcShippingFee() {
    let provId, distId;

    if (_currentAddress) {
        provId = _currentAddress.province_id;
        distId = _currentAddress.district_id;
    } else {
        provId = document.getElementById('province_id')?.value;
        distId = document.getElementById('district_id')?.value;
        if (!provId || !distId) { alert('Vui lòng chọn Tỉnh và Quận/Huyện trước!'); return; }
    }

    const loading = document.getElementById('fee-loading');
    const result  = document.getElementById('fee-result');
    loading.classList.remove('hidden');
    result.classList.add('hidden');
    result.innerHTML = '';

    try {
        const res  = await fetch(APP_URLS.shippingFee || `{{ route('shipping.fee') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ 
                receiver_province_id: parseInt(provId), 
                receiver_district_id: parseInt(distId), 
                weight: 500, 
                price: 0 
            }),
        });
        const json = await res.json();
        loading.classList.add('hidden');

        if (!json.success || !json.data?.length) {
            result.innerHTML = '<p class="text-red-400 text-xs">Không thể tính phí. Kiểm tra lại địa chỉ.</p>';
            result.classList.remove('hidden');
            return;
        }

        let html = '<p class="text-gray-400 text-xs mb-2 uppercase tracking-wider">Chọn dịch vụ vận chuyển:</p>';
        json.data.forEach((svc, i) => {
            const fee = Number(svc.MONEY_TOTAL || svc.GIA_CUOC || 0).toLocaleString('vi-VN');
            html += `<label class="flex items-center justify-between p-3 border border-[#2a2a2a] cursor-pointer hover:border-[#C5A572] transition-colors has-[:checked]:border-[#C5A572] has-[:checked]:bg-[#C5A572]/5">
                <span class="flex items-center gap-3">
                    <input type="radio" name="__svc_select" value="${svc.MA_DV_CHINH || svc.SERVICE_CODE}" data-fee="${svc.MONEY_TOTAL || svc.GIA_CUOC || 0}" ${i === 0 ? 'checked' : ''} class="accent-[#C5A572]" onchange="selectService(this)">
                    <span class="text-white text-xs font-medium">${svc.TEN_DICHVU || svc.SERVICE_NAME || 'Dịch vụ ' + (i+1)}</span>
                </span>
                <span class="text-[#C5A572] text-sm font-bold">${fee}₫</span>
            </label>`;
        });
        result.innerHTML = html;
        result.classList.remove('hidden');

        const first = json.data[0];
        document.getElementById('hidden_shipping_fee').value = first.MONEY_TOTAL || first.GIA_CUOC || 0;
        document.getElementById('hidden_service_code').value = first.MA_DV_CHINH || first.SERVICE_CODE || '';
        updateOrderTotal(Number(first.MONEY_TOTAL || first.GIA_CUOC || 0));
    } catch (e) {
        loading.classList.add('hidden');
        result.innerHTML = '<p class="text-red-400 text-xs">Lỗi kết nối Viettel Post.</p>';
        result.classList.remove('hidden');
    }
}

function selectService(radio) {
    const fee = Number(radio.dataset.fee || 0);
    document.getElementById('hidden_shipping_fee').value = fee;
    document.getElementById('hidden_service_code').value = radio.value;
    updateOrderTotal(fee);
}

function updateOrderTotal(shippingFee) {
    const subtotalEl = document.getElementById('order-subtotal');
    const shippingEl = document.getElementById('order-shipping');
    const totalEl    = document.getElementById('order-total');
    if (!subtotalEl || !shippingEl || !totalEl) return;
    const subtotal = parseInt(subtotalEl.dataset.raw || 0);
    shippingEl.textContent = shippingFee > 0 ? Number(shippingFee).toLocaleString('vi-VN') + '₫' : 'Miễn Phí';
    totalEl.textContent    = Number(subtotal + shippingFee).toLocaleString('vi-VN') + '₫';
}

/* ===== Form nhập tay: dropdown tỉnh/huyện/xã ===== */
async function loadProvinces() {
    const sel = document.getElementById('vtp-province');
    if (!sel) return;
    const res  = await fetch(APP_URLS.shippingProvinces || `{{ route('shipping.provinces') }}`).catch(() => null);
    if (!res) return;
    const json = await res.json();
    sel.innerHTML = '<option value="">-- Chọn tỉnh/thành --</option>';
    (json.data || []).forEach(p => { sel.innerHTML += `<option value="${p.PROVINCE_NAME}" data-id="${p.PROVINCE_ID}">${p.PROVINCE_NAME}</option>`; });
}
document.getElementById('vtp-province')?.addEventListener('change', async function () {
    const pid = this.options[this.selectedIndex]?.dataset.id;
    document.getElementById('province_id').value = pid || '';
    const dSel = document.getElementById('vtp-district');
    const wSel = document.getElementById('vtp-ward');
    dSel.innerHTML = '<option value="">Đang tải...</option>'; dSel.disabled = true;
    wSel.innerHTML = '<option value="">-- Chọn huyện trước --</option>'; wSel.disabled = true;
    if (!pid) return;
    const districtsBase = APP_URLS.shippingDistrictsBase || `{{ url('/shipping/districts') }}`;
    const res = await fetch(`${districtsBase}/${pid}`).catch(() => null);
    if (!res) return;
    const json = await res.json();
    dSel.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
    (json.data || []).forEach(d => { dSel.innerHTML += `<option value="${d.DISTRICT_NAME}" data-id="${d.DISTRICT_ID}">${d.DISTRICT_NAME}</option>`; });
    dSel.disabled = false;
});
document.getElementById('vtp-district')?.addEventListener('change', async function () {
    const did = this.options[this.selectedIndex]?.dataset.id;
    document.getElementById('district_id').value = did || '';
    const wSel = document.getElementById('vtp-ward');
    wSel.innerHTML = '<option value="">Đang tải...</option>'; wSel.disabled = true;
    if (!did) return;
    const wardsBase = APP_URLS.shippingWardsBase || `{{ url('/shipping/wards') }}`;
    const res = await fetch(`${wardsBase}/${did}`).catch(() => null);
    if (!res) return;
    const json = await res.json();
    wSel.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
    (json.data || []).forEach(w => { wSel.innerHTML += `<option value="${w.WARDS_NAME}" data-id="${w.WARDS_ID}">${w.WARDS_NAME}</option>`; });
    wSel.disabled = false;
});
document.getElementById('vtp-ward')?.addEventListener('change', function () {
    document.getElementById('ward_id').value = this.options[this.selectedIndex]?.dataset.id || '';
});

/* ===== KHỞI TẠO ===== */
window.addEventListener('DOMContentLoaded', () => {
    const defaultRadio = document.querySelector('input[name="address_id"]:checked');
    if (defaultRadio) {
        onSelectAddress(defaultRadio);
    } else {
        loadProvinces();
    }
});
</script>
@endpush
