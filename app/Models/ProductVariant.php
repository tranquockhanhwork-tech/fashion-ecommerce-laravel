<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ProductVariant extends Model
{
    protected static ?bool $optionTablesAvailable = null;

    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'sku',
        'stock_quantity',
        'price_override',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sizeOption(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function colorOption(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    public function scopeWithOptionRelations(Builder $query): Builder
    {
        if (! static::optionsAreAvailable()) {
            return $query;
        }

        return $query->with(['sizeOption', 'colorOption']);
    }

    public function scopeWhereSizeName(Builder $query, string $size): Builder
    {
        $size = trim($size);

        if (! static::optionsAreAvailable()) {
            return $query->where('size', $size);
        }

        return $query->where(function (Builder $variantQuery) use ($size) {
            $variantQuery->whereHas('sizeOption', fn (Builder $sizeQuery) => $sizeQuery->where('name', $size));

            if (Schema::hasColumn($this->getTable(), 'size')) {
                $variantQuery->orWhere('size', $size);
            }
        });
    }

    public function scopeWhereColorName(Builder $query, string $color): Builder
    {
        $color = trim($color);

        if (! static::optionsAreAvailable()) {
            return $query->where('color', $color);
        }

        return $query->where(function (Builder $variantQuery) use ($color) {
            $variantQuery->whereHas('colorOption', fn (Builder $colorQuery) => $colorQuery->where('name', $color));

            if (Schema::hasColumn($this->getTable(), 'color')) {
                $variantQuery->orWhere('color', $color);
            }
        });
    }

    public static function optionsAreAvailable(): bool
    {
        if (static::$optionTablesAvailable !== null) {
            return static::$optionTablesAvailable;
        }

        $instance = new static();

        static::$optionTablesAvailable = Schema::hasTable('sizes')
            && Schema::hasTable('colors')
            && Schema::hasColumn($instance->getTable(), 'size_id')
            && Schema::hasColumn($instance->getTable(), 'color_id');

        return static::$optionTablesAvailable;
    }

    public function getSizeAttribute(?string $value): ?string
    {
        if (trim((string) $value) !== '') {
            return $value;
        }

        return $this->sizeOption?->name;
    }

    public function getColorAttribute(?string $value): ?string
    {
        if (trim((string) $value) !== '') {
            return $value;
        }

        return $this->colorOption?->name;
    }

    public function getVariantLabelAttribute(): string
    {
        return collect([$this->color, $this->size])
            ->filter()
            ->join(' / ');
    }
}
