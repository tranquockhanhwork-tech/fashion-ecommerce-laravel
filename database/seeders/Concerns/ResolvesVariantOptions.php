<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ResolvesVariantOptions
{
    protected function prepareVariantPayloads(array $variants): array
    {
        return collect($this->attachVariantOptionIds($variants))
            ->map(function (array $variant) {
                unset($variant['size'], $variant['color']);

                return $variant;
            })
            ->all();
    }

    protected function attachVariantOptionIds(array $variants): array
    {
        $sizeIds = $this->ensureSizeIds(
            collect($variants)->pluck('size')->all()
        );

        $colorIds = $this->ensureColorIds(
            collect($variants)->pluck('color')->all()
        );

        return collect($variants)
            ->map(function (array $variant) use ($sizeIds, $colorIds) {
                $sizeName = trim((string) ($variant['size'] ?? ''));
                $colorName = trim((string) ($variant['color'] ?? ''));

                return array_merge($variant, [
                    'size_id' => $sizeName !== '' ? ($sizeIds->get($sizeName)) : null,
                    'color_id' => $colorName !== '' ? ($colorIds->get($colorName)) : null,
                ]);
            })
            ->all();
    }

    protected function ensureSizeIds(array $names): Collection
    {
        $sizeNames = collect($names)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        if ($sizeNames->isEmpty()) {
            return collect();
        }

        $now = now();

        DB::table('sizes')->upsert(
            $sizeNames->map(fn (string $size) => [
                'name' => $size,
                'sort_order' => $this->resolveSizeSortOrder($size),
                'created_at' => $now,
                'updated_at' => $now,
            ])->all(),
            ['name'],
            ['sort_order', 'updated_at']
        );

        return DB::table('sizes')
            ->whereIn('name', $sizeNames)
            ->pluck('id', 'name');
    }

    protected function ensureColorIds(array $names): Collection
    {
        $colorNames = collect($names)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        if ($colorNames->isEmpty()) {
            return collect();
        }

        $now = now();

        DB::table('colors')->upsert(
            $colorNames->map(fn (string $color) => [
                'name' => $color,
                'hex_code' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all(),
            ['name'],
            ['hex_code', 'updated_at']
        );

        return DB::table('colors')
            ->whereIn('name', $colorNames)
            ->pluck('id', 'name');
    }

    protected function resolveSizeSortOrder(?string $size): int
    {
        $map = [
            'XS' => 10,
            'S' => 20,
            'M' => 30,
            'L' => 40,
            'XL' => 50,
            '2XL' => 60,
            'XXL' => 60,
            '3XL' => 70,
            'XXXL' => 70,
        ];

        $normalized = strtoupper(trim((string) $size));

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (is_numeric($normalized)) {
            return 100 + (int) $normalized;
        }

        return 999;
    }
}
