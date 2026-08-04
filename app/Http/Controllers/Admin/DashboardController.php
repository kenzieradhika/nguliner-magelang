<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collaboration;
use App\Models\IgPost;
use App\Models\Place;
use App\Models\PlaceSuggestion;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'places' => Place::count(),
            'categories' => Category::count(),
            'views' => Place::sum('views'),
            'reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'new_collaborations' => Collaboration::where('status', 'new')->count(),
            'new_suggestions' => PlaceSuggestion::where('status', 'new')->count(),
            'ig_posts' => IgPost::count(),
        ];

        $categoryStats = Category::withCount('places')->get();
        $topPlaces = Place::with('category')->orderByDesc('views')->take(5)->get();
        $latestCollaborations = Collaboration::latest()->take(5)->get();
        $latestReviews = Review::with('place')->latest()->take(5)->get();
        $latestSuggestions = PlaceSuggestion::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'categoryStats', 'topPlaces', 'latestCollaborations', 'latestReviews', 'latestSuggestions'));
    }
}
