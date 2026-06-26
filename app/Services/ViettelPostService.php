<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ViettelPostService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl  = config('services.viettelpost.base_url', 'https://partner.viettelpost.vn/v2');
        $this->username = config('services.viettelpost.username', '');
        $this->password = config('services.viettelpost.password', '');
    }

    /* ===================== AUTH ===================== */

    /**
     * Lấy token dài hạn (cache 23h để tránh gọi API liên tục).
     */
    public function getToken(): ?string
    {
        return Cache::remember('viettelpost_token', 3600 * 23, function () {
            return $this->fetchToken();
        });
    }

    private function fetchToken(): ?string
    {
        try {
            // Bước 1: Login lấy token tạm
            $loginRes = Http::post("{$this->baseUrl}/user/Login", [
                'USERNAME' => $this->username,
                'PASSWORD' => $this->password,
            ]);

            if (!$loginRes->successful()) {
                Log::error('[ViettelPost] Login failed', ['status' => $loginRes->status()]);
                return null;
            }

            $tempToken = $loginRes->json('data.token');
            if (!$tempToken) return null;

            // Bước 2: Lấy long-term token
            $tokenRes = Http::withHeaders(['Token' => $tempToken])
                ->get("{$this->baseUrl}/user/ownerconnect");

            if (!$tokenRes->successful()) {
                Log::error('[ViettelPost] Get long-term token failed', ['status' => $tokenRes->status()]);
                return $tempToken; // Fallback dùng temp token
            }

            return $tokenRes->json('data.token') ?? $tempToken;
        } catch (\Throwable $e) {
            Log::error('[ViettelPost] fetchToken exception: ' . $e->getMessage());
            return null;
        }
    }

    /* ===================== TÍNH PHÍ VẬN CHUYỂN ===================== */

    /**
     * Tính phí ship bằng phương thức getPriceAll (chuẩn xác 100% bằng ID, không lỗi do chữ).
     */
    public function calculateFee(
        int $senderProvince,
        int $senderDistrict,
        int $receiverProvince,
        int $receiverDistrict,
        int $weight = 500,
        int $price = 0
    ): ?array {
        $token = $this->getToken();
        if (!$token) return null;

        try {
            $res = Http::withHeaders(['Token' => $token])
                ->post("{$this->baseUrl}/order/getPriceAll", [
                    'SENDER_PROVINCE'   => $senderProvince,
                    'SENDER_DISTRICT'   => $senderDistrict,
                    'RECEIVER_PROVINCE' => $receiverProvince,
                    'RECEIVER_DISTRICT' => $receiverDistrict,
                    'PRODUCT_TYPE'      => 'HH',   // Hàng hóa
                    'PRODUCT_WEIGHT'    => $weight,
                    'PRODUCT_PRICE'     => $price,
                    'MONEY_COLLECTION'  => $price,
                    'ORDER_SERVICE_ADD' => '',
                    'ORDER_SERVICE'     => ''
                ]);

            if (!$res->successful()) {
                Log::error('[ViettelPost] calculateFee getPriceAll failed', ['status' => $res->status()]);
                return null;
            }
            $rawData = $res->json();

            // Nếu API trả về mảng có key error và status là lỗi
            if (isset($rawData['error']) && $rawData['error'] == true) {
                 Log::error('[ViettelPost] calculateFee no price applied', ['status' => $res->status()]);
                 return []; 
            }

            // Có trường hợp API trả về mảng list trực tiếp thay vì bọc trong data
            if (isset($rawData['data'])) {
                return $rawData['data'];
            }
            
            // Hoặc bản thân nó là mảng list
            return is_array($rawData) ? $rawData : [];
        } catch (\Throwable $e) {
            Log::error('[ViettelPost] calculateFee exception: ' . $e->getMessage());
            return null;
        }
    }

    /* ===================== TẠO ĐƠN HÀNG ===================== */

    /**
     * Tạo đơn hàng trên hệ thống Viettel Post.
     *
     * @return array|null  ['ORDER_NUMBER' => 'VTP1234...', ...]
     */
    public function createOrder(array $data): ?array
    {
        $token = $this->getToken();
        if (!$token) return null;

        try {
            $payload = [
                'ORDER_NUMBER'      => $data['order_number'],          // Mã đơn của shop
                'SENDER_FULLNAME'   => config('app.shop_name', 'CoolWear'),
                'SENDER_ADDRESS'    => config('services.viettelpost.sender_address'),
                'SENDER_PHONE'      => config('services.viettelpost.sender_phone'),
                'SENDER_EMAIL'      => config('services.viettelpost.sender_email', ''),
                'SENDER_WARD'       => (int) config('services.viettelpost.sender_ward'),
                'SENDER_DISTRICT'   => (int) config('services.viettelpost.sender_district'),
                'SENDER_PROVINCE'   => (int) config('services.viettelpost.sender_province'),
                'RECEIVER_FULLNAME' => $data['receiver_name'],
                'RECEIVER_ADDRESS'  => $data['receiver_address'],
                'RECEIVER_PHONE'    => $data['receiver_phone'],
                'RECEIVER_WARD'     => (int) ($data['receiver_ward'] ?? 0),
                'RECEIVER_DISTRICT' => (int) ($data['receiver_district'] ?? 0),
                'RECEIVER_PROVINCE' => (int) ($data['receiver_province'] ?? 0),
                'PRODUCT_NAME'      => $data['product_name'] ?? 'Thời trang',
                'PRODUCT_TYPE'      => 'HH',
                'PRODUCT_PRICE'     => (int) $data['product_price'],
                'PRODUCT_WEIGHT'    => (int) ($data['weight'] ?? 500),
                'PRODUCT_QUANTITY'  => (int) ($data['quantity'] ?? 1),
                'MONEY_COLLECTION'  => $data['payment_method'] === 'COD' ? (int) $data['product_price'] : 0,
                'SERVICE_CODE'      => $data['service_code'] ?? 'LCOD', // LCOD = Chuyển phát thường COD
                'ORDER_PAYMENT'     => $data['payment_method'] === 'COD' ? 3 : 1, // 3=COD, 1=đã TT
                'LIST_ITEM'         => $data['list_items'] ?? [],
            ];

            $res = Http::withHeaders(['Token' => $token])
                ->post("{$this->baseUrl}/order/createOrder", $payload);

            if (!$res->successful()) {
                Log::error('[ViettelPost] createOrder failed', ['status' => $res->status()]);
                return null;
            }

            return $res->json('data');
        } catch (\Throwable $e) {
            Log::error('[ViettelPost] createOrder exception: ' . $e->getMessage());
            return null;
        }
    }

    /* ===================== THEO DÕI VẬN ĐƠN ===================== */

    /**
     * Lấy trạng thái vận đơn theo mã vận đơn Viettel Post.
     */
    public function trackOrder(string $trackingNumber): ?array
    {
        $token = $this->getToken();
        if (!$token) return null;

        try {
            $res = Http::withHeaders(['Token' => $token])
                ->get("{$this->baseUrl}/order/getOrderByOrderNumber", [
                    'ORDER_NUMBER' => $trackingNumber,
                ]);

            if (!$res->successful()) {
                Log::error('[ViettelPost] trackOrder failed', ['status' => $res->status()]);
                return null;
            }

            return $res->json('data');
        } catch (\Throwable $e) {
            Log::error('[ViettelPost] trackOrder exception: ' . $e->getMessage());
            return null;
        }
    }

    /* ===================== PROVINCE/DISTRICT/WARD LIST ===================== */

    public function getProvinces(): array
    {
        return Cache::remember('vtp_provinces', 3600 * 24, function () {
            $token = $this->getToken();
            if (!$token) return [];
            $res = Http::withHeaders(['Token' => $token])->get("{$this->baseUrl}/categories/listProvinceById?provinceId=0");
            return $res->json('data') ?? [];
        });
    }

    public function getDistricts(int $provinceId): array
    {
        return Cache::remember("vtp_districts_{$provinceId}", 3600 * 24, function () use ($provinceId) {
            $token = $this->getToken();
            if (!$token) return [];
            $res = Http::withHeaders(['Token' => $token])->get("{$this->baseUrl}/categories/listDistrict?provinceId={$provinceId}");
            return $res->json('data') ?? [];
        });
    }

    public function getWards(int $districtId): array
    {
        return Cache::remember("vtp_wards_{$districtId}", 3600 * 24, function () use ($districtId) {
            $token = $this->getToken();
            if (!$token) return [];
            $res = Http::withHeaders(['Token' => $token])->get("{$this->baseUrl}/categories/listWards?districtId={$districtId}");
            return $res->json('data') ?? [];
        });
    }
}
