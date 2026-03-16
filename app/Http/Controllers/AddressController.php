<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerAddress;

class AddressController extends Controller
{
    private function getCustomer()
    {
        return Auth::user()->customer;
    }

    /**
     * Lưu địa chỉ mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name'   => 'required|string|max:100',
            'recipient_phone'  => 'required|string|max:20',
            'province_id'      => 'required|integer',
            'province_name'    => 'required|string|max:100',
            'district_id'      => 'required|integer',
            'district_name'    => 'required|string|max:100',
            'ward_id'          => 'required|integer',
            'ward_name'        => 'required|string|max:100',
            'detailed_address' => 'required|string|max:255',
            'is_default'       => 'nullable|boolean',
        ]);

        $customer = $this->getCustomer();
        if (!$customer) {
            return back()->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        $isDefault = (bool) $request->input('is_default', false);

        // Nếu đặt làm mặc định, bỏ mặc định của các địa chỉ khác
        if ($isDefault || $customer->addresses()->count() === 0) {
            $isDefault = true;
            $customer->addresses()->update(['is_default' => false]);
        }

        $customer->addresses()->create([
            'recipient_name'   => $request->recipient_name,
            'recipient_phone'  => $request->recipient_phone,
            'province_id'      => $request->province_id,
            'province_name'    => $request->province_name,
            'district_id'      => $request->district_id,
            'district_name'    => $request->district_name,
            'ward_id'          => $request->ward_id,
            'ward_name'        => $request->ward_name,
            'detailed_address' => $request->detailed_address,
            'is_default'       => $isDefault,
        ]);

        return back()->with('success', 'Đã thêm địa chỉ mới thành công!');
    }

    /**
     * Xóa địa chỉ.
     */
    public function destroy(int $id)
    {
        $customer = $this->getCustomer();
        $address  = $customer->addresses()->findOrFail($id);

        $wasDefault = $address->is_default;
        $address->delete();

        // Nếu xóa địa chỉ mặc định, tự động set địa chỉ đầu tiên còn lại làm mặc định
        if ($wasDefault) {
            $first = $customer->addresses()->first();
            $first?->update(['is_default' => true]);
        }

        return back()->with('success', 'Đã xóa địa chỉ.');
    }

    /**
     * Đặt một địa chỉ làm mặc định.
     */
    public function setDefault(int $id)
    {
        $customer = $this->getCustomer();
        $customer->addresses()->update(['is_default' => false]);
        $customer->addresses()->findOrFail($id)->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt làm địa chỉ mặc định.');
    }
}
