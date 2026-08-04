<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with('place');

        if ($request->input('status') === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->input('status') === 'approved') {
            $query->where('is_approved', true);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review, AuditService $audit): RedirectResponse
    {
        $review->update(['is_approved' => true]);
        $audit->log('review.approved', $review, ['place_id' => $review->place_id]);

        return back()->with('success', 'Review disetujui dan ditayangkan.');
    }

    public function destroy(Review $review, AuditService $audit): RedirectResponse
    {
        $placeId = $review->place_id;
        $review->delete();
        $audit->log('review.deleted', null, ['place_id' => $placeId]);

        return back()->with('success', 'Review dihapus.');
    }
}
