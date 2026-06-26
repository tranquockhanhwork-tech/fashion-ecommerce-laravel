<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'primaryImage'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $sizeOptions = Size::query()->orderBy('sort_order')->orderBy('name')->pluck('name');
        $colorOptions = Color::query()->orderBy('name')->pluck('name');

        return view('admin.products.create', compact('categories', 'sizeOptions', 'colorOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'description' => 'nullable|string',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',
            'color_images' => 'nullable|array',
            'color_images.*.color' => 'nullable|string|max:50',
            'color_images.*.alt_text' => 'nullable|string|max:255',
            'color_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($this->hasActualUploadedFile('image_url')) {
            $rules['image_url'] = 'image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        $request->validate($rules);

        $data = $request->except('image_url', 'variants', 'color_images');
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['is_active'] = $request->has('is_active');

        $product = null;

        DB::transaction(function () use ($request, $data, &$product) {
            $product = Product::create($data);

            $this->storeSubmittedVariants($product, $request->input('variants', []));
            $this->storeSubmittedColorImages($product, $request, $request->input('color_images', []));

            $uploadedImage = $this->resolveUploadedImage($request);
            if ($uploadedImage instanceof UploadedFile) {
                $path = $this->storeProductImage($uploadedImage);
                if ($path !== null) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'color_id' => null,
                        'image_url' => $path,
                        'is_primary' => true
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Chưa cần thiết hiện tại
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with([
            'images' => fn ($query) => $query->with('colorOption'),
            'variants' => fn ($query) => $query->withOptionRelations(),
        ])->findOrFail($id);

        $useOptionTables = ProductVariant::optionsAreAvailable();

        $product->setRelation(
            'variants',
            $product->variants
                ->sortBy(function ($variant) use ($useOptionTables) {
                    $sizeOrder = $useOptionTables
                        ? ($variant->sizeOption?->sort_order ?? 9999)
                        : 9999;

                    return sprintf(
                        '%s|%05d|%s',
                        Str::lower((string) $variant->color),
                        $sizeOrder,
                        Str::lower((string) $variant->size)
                    );
                })
                ->values()
        );

        $categories = Category::all();
        $sizeOptions = Size::query()->orderBy('sort_order')->orderBy('name')->pluck('name');
        $colorOptions = Color::query()->orderBy('name')->pluck('name');

        return view('admin.products.edit', compact('product', 'categories', 'sizeOptions', 'colorOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'description' => 'nullable|string',
            'restock_quantities' => 'nullable|array',
            'restock_quantities.*' => 'nullable|integer|min:0',
            'restock_entries' => 'nullable|array',
            'restock_entries.*.variant_id' => 'nullable|integer',
            'restock_entries.*.quantity' => 'nullable|integer|min:0',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100',
            'color_images' => 'nullable|array',
            'color_images.*.color' => 'nullable|string|max:50',
            'color_images.*.alt_text' => 'nullable|string|max:255',
            'color_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_color_image_ids' => 'nullable|array',
            'remove_color_image_ids.*' => 'nullable|integer',
        ];

        if ($this->hasActualUploadedFile('image_url')) {
            $rules['image_url'] = 'image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        $request->validate($rules);

        $data = $request->except('image_url', 'restock_quantities', 'restock_entries', 'variants', 'color_images', 'remove_color_image_ids');
        if ($product->name !== $request->name) {
            $data['slug'] = Str::slug($request->name) . '-' . time();
        }
        $data['is_active'] = $request->has('is_active');

        $restockQuantities = $this->prepareRestockQuantities(
            $product,
            $request->input('restock_quantities', []),
            $request->input('restock_entries', [])
        );

        $removedColorImagePaths = [];

        DB::transaction(function () use ($request, $product, $data, $restockQuantities, &$removedColorImagePaths) {
            $product->update($data);

            if ($restockQuantities->isNotEmpty()) {
                $product->loadMissing('variants');

                foreach ($product->variants as $variant) {
                    $restockQuantity = (int) $restockQuantities->get($variant->id, 0);

                    if ($restockQuantity <= 0) {
                        continue;
                    }

                    $variant->increment('stock_quantity', $restockQuantity);
                }
            }

            $this->storeSubmittedVariants($product, $request->input('variants', []));
            $removedColorImagePaths = $this->removeSelectedColorImages(
                $product,
                $request->input('remove_color_image_ids', [])
            );
            $this->storeSubmittedColorImages($product, $request, $request->input('color_images', []));

            $uploadedImage = $this->resolveUploadedImage($request);
            if ($uploadedImage instanceof UploadedFile) {
                $path = $this->storeProductImage($uploadedImage);

                if ($path !== null) {
                    if ($product->primaryImage) {
                        $this->deleteStoredProductImage($product->primaryImage->image_url);
                        $product->primaryImage->update(['image_url' => $path]);
                    } else {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'color_id' => null,
                            'image_url' => $path,
                            'is_primary' => true
                        ]);
                    }
                }
            }
        });

        foreach ($removedColorImagePaths as $imagePath) {
            $this->deleteStoredProductImage($imagePath);
        }

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::with(['images', 'variants.orderItems'])->findOrFail($id);

        $hasOrderHistory = $product->variants->contains(function ($variant) {
            return $variant->orderItems->isNotEmpty();
        });

        if ($hasOrderHistory) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Không thể xóa sản phẩm đã phát sinh đơn hàng. Bạn nên ẩn sản phẩm thay vì xóa.');
        }

        $imagePaths = $product->images
            ->pluck('image_url')
            ->filter(fn ($path) => trim((string) $path) !== '')
            ->values()
            ->all();

        DB::transaction(function () use ($product) {
            $product->delete();
        });

        foreach ($imagePaths as $imagePath) {
            $this->deleteStoredProductImage($imagePath);
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm thành công!');
    }

    protected function deleteStoredProductImage(?string $path): void
    {
        $path = trim((string) $path);

        if ($path === '' || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    protected function storeProductImage(UploadedFile $uploadedFile): ?string
    {
        $pathName = trim((string) $uploadedFile->getPathname());

        if (
            ! $uploadedFile->isValid()
            || $pathName === ''
        ) {
            Log::warning('Skipping invalid product image upload.', [
                'original_name' => $uploadedFile->getClientOriginalName(),
                'error' => $uploadedFile->getError(),
                'pathname' => $uploadedFile->getPathname(),
                'realpath' => $uploadedFile->getRealPath(),
            ]);

            return null;
        }

        $extension = strtolower(trim((string) $uploadedFile->getClientOriginalExtension()));
        $extension = $extension !== '' ? $extension : 'jpg';
        $filename = Str::random(40) . '.' . $extension;
        $relativePath = 'products/' . $filename;

        $stream = @fopen($pathName, 'rb');

        if ($stream === false) {
            Log::warning('Could not open temporary product image upload.', [
                'original_name' => $uploadedFile->getClientOriginalName(),
                'pathname' => $pathName,
            ]);

            return null;
        }

        $stored = Storage::disk('public')->put($relativePath, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        if (! $stored) {
            Log::warning('Could not persist product image to public disk.', [
                'original_name' => $uploadedFile->getClientOriginalName(),
                'relative_path' => $relativePath,
            ]);

            return null;
        }

        return $relativePath;
    }

    protected function resolveUploadedImage(Request $request): ?UploadedFile
    {
        if (! $this->hasActualUploadedFile('image_url')) {
            return null;
        }

        $uploadedImage = $request->file('image_url');

        if (! $uploadedImage instanceof UploadedFile) {
            return null;
        }

        $originalName = trim($uploadedImage->getClientOriginalName());
        $pathName = trim((string) $uploadedImage->getPathname());

        if ($originalName === '') {
            return null;
        }

        if ($uploadedImage->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($pathName === '') {
            return null;
        }

        return $uploadedImage;
    }

    protected function hasActualUploadedFile(string $field): bool
    {
        $file = $_FILES[$field] ?? null;

        if (! is_array($file)) {
            return false;
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        $name = trim((string) ($file['name'] ?? ''));
        $tmpName = trim((string) ($file['tmp_name'] ?? ''));

        return $error !== UPLOAD_ERR_NO_FILE && $name !== '' && $tmpName !== '';
    }

    protected function storeSubmittedVariants(Product $product, array $rows): void
    {
        $variantPayloads = $this->prepareVariantPayloads($product, $rows);

        foreach ($variantPayloads as $variantPayload) {
            ProductVariant::create([
                'product_id' => $product->id,
                'size_id' => $variantPayload['size_id'],
                'color_id' => $variantPayload['color_id'],
                'sku' => $variantPayload['sku'] !== ''
                    ? $variantPayload['sku']
                    : $this->generateVariantSku($product, $variantPayload['size_name'], $variantPayload['color_name']),
                'stock_quantity' => $variantPayload['stock_quantity'],
                'price_override' => null,
            ]);
        }
    }

    protected function prepareRestockQuantities(Product $product, array $legacyQuantities, array $rows): \Illuminate\Support\Collection
    {
        $restockQuantities = collect($legacyQuantities)
            ->mapWithKeys(fn ($quantity, $variantId) => [(int) $variantId => max(0, (int) $quantity)])
            ->filter(fn ($quantity, $variantId) => $variantId > 0 && $quantity > 0);

        $errors = [];
        $validVariantIds = $product->variants()->pluck('id')->flip();
        $entryTotals = [];

        foreach ($rows as $index => $row) {
            $variantId = (int) ($row['variant_id'] ?? 0);
            $quantityValue = $row['quantity'] ?? null;
            $hasAnyInput = $variantId > 0 || ($quantityValue !== null && $quantityValue !== '');

            if (! $hasAnyInput) {
                continue;
            }

            if ($variantId < 1) {
                $errors["restock_entries.$index.variant_id"] = 'Vui lòng chọn biến thể cần nhập thêm hàng.';
                continue;
            }

            if (! isset($validVariantIds[$variantId])) {
                $errors["restock_entries.$index.variant_id"] = 'Biến thể được chọn không thuộc sản phẩm này.';
                continue;
            }

            if ($quantityValue === null || $quantityValue === '') {
                $errors["restock_entries.$index.quantity"] = 'Vui lòng nhập số lượng cần thêm.';
                continue;
            }

            $quantity = max(0, (int) $quantityValue);

            if ($quantity < 1) {
                continue;
            }

            $entryTotals[$variantId] = ($entryTotals[$variantId] ?? 0) + $quantity;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        foreach ($entryTotals as $variantId => $quantity) {
            $restockQuantities[$variantId] = ((int) $restockQuantities->get($variantId, 0)) + (int) $quantity;
        }

        return $restockQuantities->filter(fn ($quantity) => $quantity > 0);
    }

    protected function storeSubmittedColorImages(Product $product, Request $request, array $rows): void
    {
        $errors = [];
        $drafts = [];

        foreach ($rows as $index => $row) {
            $colorName = trim((string) ($row['color'] ?? ''));
            $altText = trim((string) ($row['alt_text'] ?? ''));
            $uploadedImage = $request->file("color_images.$index.image");
            $hasAnyInput = $colorName !== ''
                || $altText !== ''
                || $uploadedImage instanceof UploadedFile;

            if (! $hasAnyInput) {
                continue;
            }

            if ($colorName === '') {
                $errors["color_images.$index.color"] = 'Vui lòng nhập màu cho ảnh này.';
            }

            if (! $uploadedImage instanceof UploadedFile) {
                $errors["color_images.$index.image"] = 'Vui lòng chọn file ảnh cho dòng này.';
            }

            $drafts[] = [
                'index' => $index,
                'color_name' => $colorName,
                'alt_text' => $altText,
                'image' => $uploadedImage,
            ];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        if ($drafts === []) {
            return;
        }

        $nextSortOrder = ((int) $product->images()->max('sort_order')) + 1;
        $storedPaths = [];

        try {
            foreach ($drafts as $draft) {
                $color = Color::query()->firstOrCreate(
                    ['name' => $draft['color_name']],
                    ['hex_code' => null]
                );

                $path = $this->storeProductImage($draft['image']);

                if ($path === null) {
                    throw ValidationException::withMessages([
                        "color_images.{$draft['index']}.image" => 'Không thể lưu ảnh này. Vui lòng thử lại.',
                    ]);
                }

                $storedPaths[] = $path;

                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'color_id' => $color->id,
                    'image_url' => $path,
                    'alt_text' => $draft['alt_text'] !== ''
                        ? $draft['alt_text']
                        : $product->name . ' - ' . $color->name,
                    'is_primary' => false,
                    'sort_order' => $nextSortOrder++,
                ]);
            }
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                $this->deleteStoredProductImage($path);
            }

            throw $exception;
        }
    }

    protected function removeSelectedColorImages(Product $product, array $imageIds): array
    {
        $normalizedIds = collect($imageIds)
            ->map(fn ($imageId) => (int) $imageId)
            ->filter(fn ($imageId) => $imageId > 0)
            ->values();

        if ($normalizedIds->isEmpty()) {
            return [];
        }

        $images = $product->images()
            ->whereNotNull('color_id')
            ->whereIn('id', $normalizedIds)
            ->get();

        if ($images->isEmpty()) {
            return [];
        }

        $paths = $images
            ->pluck('image_url')
            ->filter(fn ($path) => trim((string) $path) !== '')
            ->values()
            ->all();

        ProductImage::query()->whereKey($images->pluck('id'))->delete();

        return $paths;
    }

    protected function prepareVariantPayloads(Product $product, array $rows): array
    {
        $errors = [];
        $drafts = [];
        $submittedSkus = [];
        $seenInputSkus = [];
        $existingCombinationKeys = $product->variants()
            ->get(['size_id', 'color_id'])
            ->mapWithKeys(fn ($variant) => [$variant->size_id . ':' . $variant->color_id => true]);

        foreach ($rows as $index => $row) {
            $size = trim((string) ($row['size'] ?? ''));
            $color = trim((string) ($row['color'] ?? ''));
            $sku = Str::upper(trim((string) ($row['sku'] ?? '')));
            $stockQuantity = $row['stock_quantity'] ?? null;
            $hasAnyInput = $size !== ''
                || $color !== ''
                || $sku !== ''
                || ($stockQuantity !== null && $stockQuantity !== '');

            if (! $hasAnyInput) {
                continue;
            }

            if ($size === '') {
                $errors["variants.$index.size"] = 'Vui lòng nhập size cho biến thể này.';
            }

            if ($color === '') {
                $errors["variants.$index.color"] = 'Vui lòng nhập màu sắc cho biến thể này.';
            }

            if ($stockQuantity === null || $stockQuantity === '') {
                $errors["variants.$index.stock_quantity"] = 'Vui lòng nhập số lượng tồn kho cho biến thể này.';
            }

            if ($sku !== '') {
                if (isset($seenInputSkus[$sku])) {
                    $errors["variants.$index.sku"] = 'SKU bị trùng trong danh sách biến thể mới.';
                }

                $seenInputSkus[$sku] = true;
                $submittedSkus[] = $sku;
            }

            $drafts[] = [
                'index' => $index,
                'size_name' => $size,
                'color_name' => $color,
                'sku' => $sku,
                'stock_quantity' => (int) $stockQuantity,
            ];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        if ($drafts === []) {
            return [];
        }

        $duplicateDatabaseSkus = ProductVariant::query()
            ->whereIn('sku', $submittedSkus)
            ->pluck('sku')
            ->flip();

        $seenCombinationKeys = [];

        foreach ($drafts as $draftIndex => $draft) {
            $size = Size::query()->firstOrCreate(
                ['name' => $draft['size_name']],
                ['sort_order' => $this->resolveSizeSortOrder($draft['size_name'])]
            );

            $color = Color::query()->firstOrCreate(
                ['name' => $draft['color_name']],
                ['hex_code' => null]
            );

            $combinationKey = $size->id . ':' . $color->id;

            if (isset($seenCombinationKeys[$combinationKey])) {
                $errors["variants.{$draft['index']}.size"] = 'Biến thể size/màu này đang bị trùng trong danh sách mới.';
            }

            if (isset($existingCombinationKeys[$combinationKey])) {
                $errors["variants.{$draft['index']}.size"] = 'Biến thể size/màu này đã tồn tại cho sản phẩm.';
            }

            if ($draft['sku'] !== '' && isset($duplicateDatabaseSkus[$draft['sku']])) {
                $errors["variants.{$draft['index']}.sku"] = 'SKU này đã tồn tại trong hệ thống.';
            }

            $seenCombinationKeys[$combinationKey] = true;
            $drafts[$draftIndex]['size_id'] = $size->id;
            $drafts[$draftIndex]['color_id'] = $color->id;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $drafts;
    }

    protected function generateVariantSku(Product $product, string $size, string $color): string
    {
        $base = strtoupper(trim(Str::slug('P' . $product->id . '-' . $color . '-' . $size, '-')));
        $base = $base !== '' ? $base : 'P' . $product->id . '-VARIANT';
        $sku = Str::limit($base, 100, '');
        $counter = 1;

        while (ProductVariant::query()->where('sku', $sku)->exists()) {
            $suffix = '-' . $counter;
            $sku = Str::limit($base, 100 - strlen($suffix), '') . $suffix;
            $counter++;
        }

        return $sku;
    }

    protected function resolveSizeSortOrder(string $size): int
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

        $normalized = strtoupper(trim($size));

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (is_numeric($normalized)) {
            return 100 + (int) $normalized;
        }

        return 999;
    }
}
