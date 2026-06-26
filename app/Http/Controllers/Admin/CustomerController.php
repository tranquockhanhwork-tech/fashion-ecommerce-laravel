<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $totalCustomers = Customer::count();
        $customersWithOrders = Customer::has('orders')->count();
        $customersWithAddresses = Customer::has('addresses')->count();
        $newCustomersThisMonth = Customer::query()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $recentCustomers = Customer::query()
            ->with(['user', 'latestOrder'])
            ->withCount('orders')
            ->latest()
            ->take(5)
            ->get();

        $topCustomers = Customer::query()
            ->with(['user', 'latestOrder'])
            ->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(5)
            ->get();

        $customers = Customer::query()
            ->with(['user', 'defaultAddress', 'latestOrder'])
            ->withCount(['orders', 'addresses', 'wishlists', 'reviews'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('full_name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.customers.index', compact(
            'customers',
            'search',
            'totalCustomers',
            'customersWithOrders',
            'customersWithAddresses',
            'newCustomersThisMonth',
            'recentCustomers',
            'topCustomers'
        ));
    }

    public function show(string $id)
    {
        $customer = Customer::query()
            ->with([
                'user',
                'addresses',
                'defaultAddress',
                'orders' => fn ($query) => $query->latest()->withCount('items'),
                'reviews.product',
                'wishlists.product.primaryImage',
            ])
            ->withCount(['orders', 'addresses', 'wishlists', 'reviews'])
            ->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }
}
