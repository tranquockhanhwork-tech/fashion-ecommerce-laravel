<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id', 'recipient_name', 'recipient_phone',
        'shipping_address', 'subtotal', 'shipping_fee',
        'discount_amount', 'total_amount', 'status',
        'payment_status', 'payment_method', 'transaction_id', 'note',
        'shipping_provider', 'tracking_number',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
