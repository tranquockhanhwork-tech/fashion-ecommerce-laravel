<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'promotional_price', 'is_active', 'views',
    ];

    protected $casts = [
        'price'             => 'float',
        'promotional_price' => 'float',
        'is_active'         => 'boolean',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    /* ===================== ACCESSORS ===================== */

    /**
     * Giá hiển thị: dùng giá khuyến mãi nếu có, ngược lại dùng giá gốc.
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->promotional_price ?? $this->price;
    }

    /**
     * Phần trăm giảm giá, trả về null nếu không có khuyến mãi.
     */
    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->promotional_price || $this->promotional_price >= $this->price) {
            return null;
        }
        return (int) round((1 - $this->promotional_price / $this->price) * 100);
    }

    /**
     * Ảnh đại diện: ảnh primary trong DB, hoặc fallback ảnh online.
     */
    public function getThumbnailAttribute(): string
    {
        $imageUrl = $this->primaryImage?->image_url;

        if (! $imageUrl) {
            return 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&q=80';
        }

        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }

        return asset('storage/' . ltrim($imageUrl, '/'));
    }

    /**
     * Giá hiển thị định dạng VNĐ.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->current_price, 0, ',', '.') . '₫';
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . '₫';
    }

    /**
     * Số sao trung bình (dùng trong product detail).
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews->avg('rating') ?? 0, 1);
    }
}
