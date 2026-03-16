<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $revenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $newOrdersCount = Order::where('status', 'pending')->count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::where('is_active', true)->count();

        // Đơn hàng gần đây
        $recentOrders = Order::with('customer')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Biến thể sắp hết hàng và hết hàng chung một danh sách
        $lowStockVariants = ProductVariant::with('product')
            ->whereHas('product', fn ($query) => $query->where('is_active', true))
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity')
            ->orderBy('id')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'revenue', 
            'newOrdersCount', 
            'totalCustomers', 
            'totalProducts', 
            'recentOrders', 
            'lowStockVariants'
        ));
    }
}
