<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;

/* ===================== MAIN ROUTES ===================== */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

/* ===================== SHOP ROUTES ===================== */
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{id}', [ShopController::class, 'show'])->name('show');
});

/* ===================== CART ROUTES ===================== */
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index')->middleware('auth');
    Route::post('/add', [CartController::class, 'add'])->name('add')->middleware('auth');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('update')->middleware('auth');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove')->middleware('auth');
});

/* ===================== CHECKOUT ROUTES ===================== */
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/',  [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

/* ===================== SHIPPING / VIETTEL POST ===================== */
Route::prefix('shipping')->name('shipping.')->group(function () {
    Route::get('/provinces',          [ShippingController::class, 'provinces'])->name('provinces');
    Route::get('/districts/{pid}',    [ShippingController::class, 'districts'])->name('districts');
    Route::get('/wards/{did}',        [ShippingController::class, 'wards'])->name('wards');
    Route::post('/calculate-fee',     [ShippingController::class, 'calculateFee'])->name('fee');
    Route::get('/track/{tracking}',   [ShippingController::class, 'track'])->name('track');
});

/* ===================== AUTH ROUTES ===================== */
use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::patch('/account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Quản lý địa chỉ
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{id}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
});

/* ===================== ADMIN ROUTES ===================== */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý Sản phẩm
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

    // Quản lý Danh mục
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    
    // Quản lý Đơn hàng
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);

    // Quản lý Khách hàng
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show']);

    // Quản lý mã giảm giá
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->except(['show']);

    // Quản lý đánh giá
    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'show', 'update', 'destroy']);

    // Quản lý nhân viên
    Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class)
        ->except(['show'])
        ->middleware('role:admin');
});
