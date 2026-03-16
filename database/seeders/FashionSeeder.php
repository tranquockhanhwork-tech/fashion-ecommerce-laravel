<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FashionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Thời gian chung
        $now = Carbon::now();

        // 1. Users (Tài khoản)
        $users = [
            ['id' => 1, 'email' => 'admin@fashion.vn', 'password' => Hash::make('password'), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'email' => 'nhanvien@fashion.vn', 'password' => Hash::make('password'), 'role' => 'employee', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'email' => 'khachhang1@gmail.com', 'password' => Hash::make('password'), 'role' => 'customer', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'email' => 'khachhang2@yahoo.com', 'password' => Hash::make('password'), 'role' => 'customer', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('users')->insert($users);

        // 2. Employees (Nhân viên)
        DB::table('employees')->insert([
            ['id' => 1, 'user_id' => 2, 'full_name' => 'Nguyễn Bán Hàng', 'phone' => '0987654321', 'position' => 'Bán hàng', 'salary' => 8000000.00, 'hired_at' => '2025-01-01', 'created_at' => $now, 'updated_at' => $now]
        ]);

        // 3. Customers (Khách hàng)
        DB::table('customers')->insert([
            ['id' => 1, 'user_id' => 3, 'full_name' => 'Trần Văn Khách', 'phone' => '0123456789', 'address' => '123 Đường A, Quận 1, TP.HCM', 'gender' => 'male', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'user_id' => 4, 'full_name' => 'Lê Thị Hà', 'phone' => '0912345678', 'address' => '456 Đường B, Quận 3, TP.HCM', 'gender' => 'female', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Categories (Danh mục - không phân giới tính)
        DB::table('categories')->insert([
            ['id' => 1, 'parent_id' => null, 'name' => 'Áo thun', 'slug' => 'ao-thun', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'parent_id' => null, 'name' => 'Áo sơ mi', 'slug' => 'ao-so-mi', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'parent_id' => null, 'name' => 'Quần Jean', 'slug' => 'quan-jean', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'parent_id' => null, 'name' => 'Quần Tây', 'slug' => 'quan-tay', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'parent_id' => null, 'name' => 'Áo Khoác', 'slug' => 'ao-khoac', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'parent_id' => null, 'name' => 'Váy - Chân váy', 'slug' => 'vay-chan-vay', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'parent_id' => null, 'name' => 'Phụ kiện', 'slug' => 'phu-kien', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 5. Products (Sản phẩm - Mẫu thử 10 dòng)
        $products = [
            ['id' => 1, 'category_id' => 1, 'name' => 'Áo thun Cotton Basic', 'slug' => Str::slug('Áo thun Cotton Basic'), 'price' => 150000, 'promotional_price' => 120000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'category_id' => 1, 'name' => 'Áo Polo tay ngắn họa tiết', 'slug' => Str::slug('Áo Polo tay ngắn họa tiết'), 'price' => 250000, 'promotional_price' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'category_id' => 3, 'name' => 'Quần Jean Dáng Suông Mài Rách', 'slug' => Str::slug('Quần Jean Dáng Suông Mài Rách'), 'price' => 380000, 'promotional_price' => 350000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'category_id' => 6, 'name' => 'Váy đầm lụa trễ vai quyến rũ', 'slug' => Str::slug('Váy đầm lụa trễ vai quyến rũ'), 'price' => 450000, 'promotional_price' => 400000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'category_id' => 6, 'name' => 'Váy babydoll dáng xòe', 'slug' => Str::slug('Váy babydoll dáng xòe'), 'price' => 320000, 'promotional_price' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'category_id' => 5, 'name' => 'Áo Khoác Gió Form Rộng', 'slug' => Str::slug('Áo Khoác Gió Form Rộng'), 'price' => 290000, 'promotional_price' => 250000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'category_id' => 4, 'name' => 'Quần ống rộng lụa nhung', 'slug' => Str::slug('Quần ống rộng lụa nhung'), 'price' => 200000, 'promotional_price' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'category_id' => 4, 'name' => 'Quần Tây Ống Đứng', 'slug' => Str::slug('Quần Tây Ống Đứng'), 'price' => 350000, 'promotional_price' => 300000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'category_id' => 1, 'name' => 'Áo thun dài tay vải tăm', 'slug' => Str::slug('Áo thun dài tay vải tăm'), 'price' => 180000, 'promotional_price' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'category_id' => 6, 'name' => 'Chân Váy Chữ A Công Sở', 'slug' => Str::slug('Chân Váy Chữ A Công Sở'), 'price' => 220000, 'promotional_price' => 199000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('products')->insert($products);

        // 6. Product Variants (Biến thể)
        $variants = [
            ['id' => 1, 'product_id' => 1, 'sku' => 'AOT-BASIC-TRANG-M', 'size' => 'M', 'color' => 'Trắng', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'product_id' => 1, 'sku' => 'AOT-BASIC-TRANG-L', 'size' => 'L', 'color' => 'Trắng', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'product_id' => 1, 'sku' => 'AOT-BASIC-DEN-M', 'size' => 'M', 'color' => 'Đen', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'product_id' => 3, 'sku' => 'QJN-SUONG-M', 'size' => 'M', 'color' => 'Xanh Nhạt', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'product_id' => 3, 'sku' => 'QJN-SUONG-L', 'size' => 'L', 'color' => 'Xanh Nhạt', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'product_id' => 4, 'sku' => 'VAY-LUA-DO-S', 'size' => 'S', 'color' => 'Đỏ Đô', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'product_id' => 10, 'sku' => 'VAY-CHU-A-DEN-M', 'size' => 'M', 'color' => 'Đen', 'stock_quantity' => 999, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('product_variants')->insert($variants);

        // 7. Coupons (Mã giảm giá)
        DB::table('coupons')->insert([
            ['id' => 1, 'code' => 'WELCOME50K', 'description' => 'Giảm 50k cho đơn đầu tiên', 'discount_type' => 'fixed', 'discount_value' => 50000, 'min_order_amount' => 200000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'code' => 'TET2026', 'description' => 'Giảm 10% dịp Lễ', 'discount_type' => 'percentage', 'discount_value' => 10, 'max_discount_amount' => 100000, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 8. Carts
        DB::table('carts')->insert([
            ['id' => 1, 'customer_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'customer_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 9. Cart Items
        DB::table('cart_items')->insert([
            ['id' => 1, 'cart_id' => 1, 'product_variant_id' => 1, 'quantity' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'cart_id' => 1, 'product_variant_id' => 4, 'quantity' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 10. Orders
        DB::table('orders')->insert([
            [
                'id' => 1, 
                'customer_id' => 1, 
                'recipient_name' => 'Trần Văn Khách', 
                'recipient_phone' => '0123456789', 
                'shipping_address' => '123 Đường A, Quận 1', 
                'subtotal' => 590000, // 2*120k + 1*350k
                'shipping_fee' => 30000, 
                'discount_amount' => 50000, 
                'total_amount' => 570000, 
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => 'COD',
                'coupon_id' => 1,
                'created_at' => clone $now->subDays(2), 
                'updated_at' => clone $now->subDays(2)
            ],
            [
                'id' => 2, 
                'customer_id' => 2, 
                'recipient_name' => 'Lê Thị Hà', 
                'recipient_phone' => '0912345678', 
                'shipping_address' => 'Công ty B, Tòa nhà C', 
                'subtotal' => 400000, // Váy đỏ
                'shipping_fee' => 0, 
                'discount_amount' => 0, 
                'total_amount' => 400000, 
                'status' => 'shipped',
                'payment_status' => 'paid',
                'payment_method' => 'VNPay',
                'coupon_id' => null,
                'created_at' => clone $now->subDays(1), 
                'updated_at' => clone $now->subDays(1)
            ],
        ]);

        // 11. Order Items
        DB::table('order_items')->insert([
            ['id' => 1, 'order_id' => 1, 'product_variant_id' => 1, 'quantity' => 2, 'unit_price' => 120000, 'subtotal' => 240000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'order_id' => 1, 'product_variant_id' => 4, 'quantity' => 1, 'unit_price' => 350000, 'subtotal' => 350000, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'order_id' => 2, 'product_variant_id' => 6, 'quantity' => 1, 'unit_price' => 400000, 'subtotal' => 400000, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 12. Product Reviews
        DB::table('product_reviews')->insert([
            ['id' => 1, 'product_id' => 4, 'customer_id' => 2, 'rating' => 5, 'title' => 'Tuyệt vời', 'comment' => 'Váy rất đẹp, vải mát mẻ, lên form chuẩn! Sẽ mua ủng hộ thêm.', 'is_approved' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
