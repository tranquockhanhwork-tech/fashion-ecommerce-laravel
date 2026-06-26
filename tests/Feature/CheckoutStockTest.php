<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_deducts_stock_and_clears_cart(): void
    {
        [$user, $customer, $address, $variant, $cartItem] = $this->makeCheckoutContext(stockQuantity: 5, cartQuantity: 2);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_fee' => 30000,
            'note' => 'Test order',
        ]);

        $order = Order::query()->first();

        $response->assertRedirect(route('checkout.success', $order?->id));
        $this->assertNotNull($order);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_id' => $customer->id,
            'subtotal' => 200000,
            'shipping_fee' => 30000,
            'total_amount' => 230000,
            'payment_method' => 'COD',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'unit_price' => 100000,
            'subtotal' => 200000,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock_quantity' => 3,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_checkout_is_rejected_when_stock_is_insufficient(): void
    {
        [$user, $customer, $address, $variant] = $this->makeCheckoutContext(stockQuantity: 1, cartQuantity: 2);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_fee' => 30000,
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock_quantity' => 1,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    /**
     * @return array{0: User, 1: Customer, 2: CustomerAddress, 3: ProductVariant, 4: CartItem}
     */
    private function makeCheckoutContext(int $stockQuantity, int $cartQuantity): array
    {
        $user = User::query()->create([
            'email' => 'checkout-test-' . Str::random(8) . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Test Customer',
            'phone' => '0900000000',
            'address' => '123 Test Street',
            'gender' => 'other',
        ]);

        $address = CustomerAddress::query()->create([
            'customer_id' => $customer->id,
            'recipient_name' => 'Test Customer',
            'recipient_phone' => '0900000000',
            'province_id' => 1,
            'province_name' => 'Can Tho',
            'district_id' => 2,
            'district_name' => 'Ninh Kieu',
            'ward_id' => 3,
            'ward_name' => 'Xuan Khanh',
            'detailed_address' => '123 Test Street',
            'is_default' => true,
        ]);

        $category = Category::query()->create([
            'name' => 'Test Category',
            'slug' => 'test-category-' . Str::random(6),
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product-' . Str::random(6),
            'price' => 100000,
            'promotional_price' => null,
            'is_active' => true,
        ]);

        $size = Size::query()->firstOrCreate([
            'name' => 'M',
        ], [
            'name' => 'M',
            'sort_order' => 30,
        ]);

        $color = Color::query()->create([
            'name' => 'Black',
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'color_id' => $color->id,
            'sku' => 'TEST-' . strtoupper(Str::random(8)),
            'stock_quantity' => $stockQuantity,
            'price_override' => null,
        ]);

        $cart = Cart::query()->create([
            'customer_id' => $customer->id,
        ]);

        $cartItem = CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => $cartQuantity,
        ]);

        return [$user, $customer, $address, $variant, $cartItem];
    }
}
