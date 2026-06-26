<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $reviews = ProductReview::query()
            ->with(['product', 'customer.user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('comment', 'like', '%' . $search . '%')
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('full_name', 'like', '%' . $search . '%')
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery->where('email', 'like', '%' . $search . '%');
                                });
                        });
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_approved', $status === 'approved');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalReviews = ProductReview::count();
        $approvedReviews = ProductReview::where('is_approved', true)->count();
        $pendingReviews = ProductReview::where('is_approved', false)->count();
        $averageRating = round((float) ProductReview::avg('rating'), 1);

        return view('admin.reviews.index', compact(
            'reviews',
            'search',
            'status',
            'totalReviews',
            'approvedReviews',
            'pendingReviews',
            'averageRating'
        ));
    }

    public function show(string $id)
    {
        $review = ProductReview::with(['product.primaryImage', 'customer.user'])->findOrFail($id);

        return view('admin.reviews.show', compact('review'));
    }

    public function update(Request $request, string $id)
    {
        $review = ProductReview::findOrFail($id);
        $data = $request->validate([
            'is_approved' => ['required', 'boolean'],
        ]);

        $review->update([
            'is_approved' => (bool) $data['is_approved'],
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái duyệt đánh giá.');
    }

    public function destroy(string $id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã xóa đánh giá.');
    }
}
