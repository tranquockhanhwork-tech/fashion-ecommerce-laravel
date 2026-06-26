<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'product_variant_id', 'color_id', 'image_url', 'alt_text', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function colorOption(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function getResolvedUrlAttribute(): string
    {
        $imageUrl = trim((string) $this->image_url);

        if ($imageUrl === '') {
            return 'https://placehold.co/640x800/111111/C5A572?text=No+Image';
        }

        if (Str::startsWith($imageUrl, ['http://', 'https://'])) {
            return $imageUrl;
        }

        return asset('storage/' . ltrim($imageUrl, '/'));
    }
}
