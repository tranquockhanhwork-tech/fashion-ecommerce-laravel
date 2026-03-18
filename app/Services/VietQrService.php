<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class VietQrService
{
    protected string $baseUrl;
    protected string $accountNo;
    protected string $accountName;
    protected string $bankName;
    protected string $acqId;
    protected string $template;
    protected string $format;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.vietqr.base_url', 'https://api.vietqr.io'), '/');
        $this->accountNo = preg_replace('/\D+/', '', (string) config('services.vietqr.account_no', '')) ?? '';
        $this->accountName = $this->normalizeText((string) config('services.vietqr.account_name', ''), 50);
        $this->bankName = (string) config('services.vietqr.bank_name', '');
        $this->acqId = preg_replace('/\D+/', '', (string) config('services.vietqr.acq_id', '')) ?? '';
        $this->template = (string) config('services.vietqr.template', 'compact2');
        $this->format = strtolower((string) config('services.vietqr.format', 'jpg'));
    }

    public function generateOrderQr(Order $order): array
    {
        $transferInfo = $this->buildTransferInfo($order);

        if (! $this->isConfigured()) {
            return [
                'available' => false,
                'message' => 'VietQR chưa được cấu hình đầy đủ: ' . implode(', ', $this->missingConfigFields()) . '.',
                'transfer_info' => $transferInfo,
            ];
        }

        $imageFormat = in_array($this->format, ['jpg', 'jpeg', 'png'], true) ? $this->format : 'jpg';
        $query = http_build_query([
            'accountName' => $transferInfo['account_name'],
            'amount' => $transferInfo['amount'],
            'addInfo' => $transferInfo['add_info'],
        ]);

        $qrDataUrl = "{$this->baseUrl}/image/{$this->acqId}-{$this->accountNo}-{$this->template}.{$imageFormat}?{$query}";

        return [
            'available' => true,
            'message' => null,
            'transfer_info' => $transferInfo,
            'qr_data_url' => $qrDataUrl,
            'qr_code' => null,
        ];
    }

    public function isConfigured(): bool
    {
        return $this->accountNo !== ''
            && $this->accountName !== ''
            && $this->acqId !== '';
    }

    protected function missingConfigFields(): array
    {
        $missing = [];

        if ($this->acqId === '') {
            $missing[] = 'VIETQR_ACQ_ID';
        }

        if ($this->accountNo === '') {
            $missing[] = 'VIETQR_ACCOUNT_NO';
        }

        if ($this->accountName === '') {
            $missing[] = 'VIETQR_ACCOUNT_NAME';
        }

        return $missing;
    }

    protected function buildTransferInfo(Order $order): array
    {
        $reference = 'DH' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

        return [
            'bank_name' => $this->bankName,
            'acq_id' => $this->acqId,
            'account_no' => $this->accountNo,
            'account_name' => $this->accountName,
            'amount' => (int) round((float) $order->total_amount),
            'add_info' => $reference,
            'reference' => $reference,
        ];
    }

    protected function normalizeText(string $value, int $limit): string
    {
        $ascii = Str::upper(Str::ascii(trim($value)));
        $sanitized = preg_replace('/[^A-Z0-9 ]+/', '', $ascii) ?? '';
        $normalized = preg_replace('/\s+/', ' ', trim($sanitized)) ?? '';

        return mb_substr($normalized, 0, $limit);
    }
}
