<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with([
            'items.variant' => fn ($query) => $query->withOptionRelations()->with('product'),
        ])
            ->where('customer_id', Auth::user()->customer?->id)
            ->latest()
            ->get();

        return view('pages.orders', compact('orders'));
    }
}
