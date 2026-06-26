<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ViettelPostService;

class ShippingController extends Controller
{
    public function __construct(protected ViettelPostService $vtp) {}

    /**
     * Lấy danh sách tỉnh/thành.
     */
    public function provinces()
    {
        $data = $this->vtp->getProvinces();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Lấy danh sách quận/huyện theo tỉnh.
     */
    public function districts(int $provinceId)
    {
        $data = $this->vtp->getDistricts($provinceId);
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Lấy danh sách phường/xã theo huyện.
     */
    public function wards(int $districtId)
    {
        $data = $this->vtp->getWards($districtId);
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Tính phí vận chuyển theo mã Tỉnh / Huyện (getPriceAll).
     */
    public function calculateFee(Request $request)
    {
        $request->validate([
            'receiver_province_id' => 'required|integer',
            'receiver_district_id' => 'required|integer',
            'weight'               => 'nullable|integer|min:1',
            'price'                => 'nullable|integer|min:0',
        ]);

        $senderProvince = (int) config('services.viettelpost.sender_province', 5);
        $senderDistrict = (int) config('services.viettelpost.sender_district', 3591);

        $fees = $this->vtp->calculateFee(
            $senderProvince,
            $senderDistrict,
            $request->receiver_province_id,
            $request->receiver_district_id,
            $request->input('weight', 500),
            $request->input('price', 0)
        );

        if ($fees === null || empty($fees)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop hiện không hỗ trợ giao hàng đến khu vực này, hoặc khoảng cách quá xa (Lỗi từ Viettel Post).',
            ]);
        }

        return response()->json(['success' => true, 'data' => $fees]);
    }

    /**
     * Tra vận đơn theo mã tracking.
     */
    public function track(string $trackingNumber)
    {
        $data = $this->vtp->trackOrder($trackingNumber);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin vận đơn.',
            ]);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
