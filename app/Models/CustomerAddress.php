<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id', 'recipient_name', 'recipient_phone',
        'province_id', 'province_name',
        'district_id', 'district_name',
        'ward_id', 'ward_name',
        'detailed_address', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Địa chỉ đầy đủ dạng text (dùng để gọi API tính phí, hoặc lưu vào orders)
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->detailed_address}, {$this->ward_name}, {$this->district_name}, {$this->province_name}";
    }
}
