<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductVariantManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_variants(): void
    {
        $admin = $this->makeAdminUser();
        $category = $this->makeCategory();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Áo Thun Test',
            'category_id' => $category->id,
            'price' => 150000,
            'promotional_price' => 120000,
            'description' => 'Sản phẩm test',
            'is_active' => '1',
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Đen',
                    'stock_quantity' => 12,
                    'sku' => 'TEST-BLACK-M',
                ],
                [
                    'size' => 'L',
                    'color' => 'Trắng',
                    'stock_quantity' => 7,
                    'sku' => '',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('name', 'Áo Thun Test')->firstOrFail();
        $black = Color::query()->where('name', 'Đen')->firstOrFail();
        $white = Color::query()->where('name', 'Trắng')->firstOrFail();
        $sizeM = Size::query()->where('name', 'M')->firstOrFail();
        $sizeL = Size::query()->where('name', 'L')->firstOrFail();

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'size_id' => $sizeM->id,
            'color_id' => $black->id,
            'stock_quantity' => 12,
            'sku' => 'TEST-BLACK-M',
        ]);

        $generatedVariant = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('size_id', $sizeL->id)
            ->where('color_id', $white->id)
            ->first();

        $this->assertNotNull($generatedVariant);
        $this->assertSame(7, $generatedVariant->stock_quantity);
        $this->assertNotSame('', trim((string) $generatedVariant->sku));
    }

    public function test_admin_can_add_new_variant_while_editing_product(): void
    {
        $admin = $this->makeAdminUser();
        $category = $this->makeCategory();
        $sizeM = Size::query()->firstOrCreate(
            ['name' => 'M'],
            ['sort_order' => 30]
        );
        $black = Color::query()->create(['name' => 'Đen']);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Áo Polo Test',
            'slug' => 'ao-polo-test-' . Str::random(6),
            'price' => 250000,
            'promotional_price' => null,
            'is_active' => true,
        ]);

        $existingVariant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $sizeM->id,
            'color_id' => $black->id,
            'sku' => 'POLO-BLACK-M',
            'stock_quantity' => 4,
            'price_override' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Áo Polo Test',
            'category_id' => $category->id,
            'price' => 250000,
            'promotional_price' => '',
            'description' => 'Đã cập nhật',
            'is_active' => '1',
            'restock_entries' => [
                [
                    'variant_id' => $existingVariant->id,
                    'quantity' => 3,
                ],
            ],
            'variants' => [
                [
                    'size' => 'XL',
                    'color' => 'Xanh Navy',
                    'stock_quantity' => 5,
                    'sku' => '',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.edit', $product->id));

        $existingVariant->refresh();
        $this->assertSame(7, $existingVariant->stock_quantity);

        $sizeXl = Size::query()->where('name', 'XL')->firstOrFail();
        $navy = Color::query()->where('name', 'Xanh Navy')->firstOrFail();

        $newVariant = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('size_id', $sizeXl->id)
            ->where('color_id', $navy->id)
            ->first();

        $this->assertNotNull($newVariant);
        $this->assertSame(5, $newVariant->stock_quantity);
        $this->assertNotSame('', trim((string) $newVariant->sku));
    }

    public function test_admin_can_upload_color_specific_images_when_creating_product(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdminUser();
        $category = $this->makeCategory();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Áo Sơ Mi Màu',
            'category_id' => $category->id,
            'price' => 320000,
            'promotional_price' => '',
            'description' => 'Có ảnh theo màu',
            'is_active' => '1',
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Đen',
                    'stock_quantity' => 8,
                    'sku' => 'SHIRT-BLACK-M',
                ],
            ],
            'color_images' => [
                [
                    'color' => 'Đen',
                    'alt_text' => 'Áo sơ mi đen mặt trước',
                    'image' => UploadedFile::fake()->image('shirt-black-front.jpg'),
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('name', 'Áo Sơ Mi Màu')->firstOrFail();
        $black = Color::query()->where('name', 'Đen')->firstOrFail();
        $image = ProductImage::query()
            ->where('product_id', $product->id)
            ->where('color_id', $black->id)
            ->first();

        $this->assertNotNull($image);
        $this->assertFalse($image->is_primary);
        $this->assertSame('Áo sơ mi đen mặt trước', $image->alt_text);
        Storage::disk('public')->assertExists($image->image_url);
    }

    public function test_admin_can_remove_color_specific_images_when_updating_product(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdminUser();
        $category = $this->makeCategory();
        $color = Color::query()->create(['name' => 'Đỏ']);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Áo Khoác Đỏ',
            'slug' => 'ao-khoac-do-' . Str::random(6),
            'price' => 450000,
            'promotional_price' => null,
            'is_active' => true,
        ]);

        Storage::disk('public')->put('products/existing-red.jpg', 'fake-image');

        $image = ProductImage::query()->create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'color_id' => $color->id,
            'image_url' => 'products/existing-red.jpg',
            'alt_text' => 'Áo đỏ',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Áo Khoác Đỏ',
            'category_id' => $category->id,
            'price' => 450000,
            'promotional_price' => '',
            'description' => 'Đã cập nhật',
            'is_active' => '1',
            'remove_color_image_ids' => [$image->id],
        ]);

        $response->assertRedirect(route('admin.products.edit', $product->id));
        $this->assertDatabaseMissing('product_images', [
            'id' => $image->id,
        ]);
        Storage::disk('public')->assertMissing('products/existing-red.jpg');
    }

    private function makeAdminUser(): User
    {
        return User::query()->create([
            'email' => 'admin-' . Str::random(8) . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    private function makeCategory(): Category
    {
        return Category::query()->create([
            'name' => 'Danh mục test ' . Str::random(5),
            'slug' => 'danh-muc-test-' . Str::random(8),
            'description' => 'Danh mục test',
        ]);
    }
}
