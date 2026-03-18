<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
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
        return view('admin.products.create', compact('categories'));
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
        ];

        if ($this->hasActualUploadedFile('image_url')) {
            $rules['image_url'] = 'image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        $request->validate($rules);

        $data = $request->except('image_url');
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['is_active'] = $request->has('is_active');

        $product = Product::create($data);

        $uploadedImage = $this->resolveUploadedImage($request);
        if ($uploadedImage instanceof UploadedFile) {
            $path = $this->storeProductImage($uploadedImage);
            if ($path !== null) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                    'is_primary' => true
                ]);
            }
        }

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
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
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
        ];

        if ($this->hasActualUploadedFile('image_url')) {
            $rules['image_url'] = 'image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        $request->validate($rules);

        $data = $request->except('image_url');
        if ($product->name !== $request->name) {
            $data['slug'] = Str::slug($request->name) . '-' . time();
        }
        $data['is_active'] = $request->has('is_active');

        $product->update($data);

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
                        'image_url' => $path,
                        'is_primary' => true
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
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
                ->with('error', 'Khong the xoa san pham da phat sinh don hang. Ban nen an san pham thay vi xoa.');
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
}
