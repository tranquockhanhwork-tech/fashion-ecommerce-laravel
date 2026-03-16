<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $paymentStatus = trim((string) $request->query('payment_status', ''));

        $orders = Order::query()
            ->with(['customer', 'coupon'])
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    if (is_numeric($search)) {
                        $subQuery->where('id', (int) $search)
                            ->orWhere('recipient_name', 'like', '%' . $search . '%')
                            ->orWhere('recipient_phone', 'like', '%' . $search . '%')
                            ->orWhere('tracking_number', 'like', '%' . $search . '%')
                            ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('full_name', 'like', '%' . $search . '%');
                            });

                        return;
                    }

                    $subQuery->where('recipient_name', 'like', '%' . $search . '%')
                        ->orWhere('recipient_phone', 'like', '%' . $search . '%')
                        ->orWhere('tracking_number', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('full_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($paymentStatus !== '', fn ($query) => $query->where('payment_status', $paymentStatus))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $shippingOrders = Order::whereIn('status', ['processing', 'shipped', 'delivered'])->count();
        $paidRevenue = Order::where('payment_status', 'paid')->sum('total_amount');

        return view('admin.orders.index', compact(
            'orders',
            'search',
            'status',
            'paymentStatus',
            'totalOrders',
            'pendingOrders',
            'shippingOrders',
            'paidRevenue'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Admin thường không tạo đơn hàng
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Admin thường không tạo đơn hàng
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['customer', 'items.variant.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return $this->show($id); // Có thể gộp edit vào show cho tiện quản lý
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded'
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status
        ]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Đã xóa đơn hàng thành công!');
    }
}
