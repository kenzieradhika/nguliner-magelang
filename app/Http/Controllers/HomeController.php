<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IgPost;
use App\Models\Place;
use App\Services\DailyPickService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(DailyPickService $dailyPick): View
    {
        $dailyPicks = $dailyPick->pick(3);
        $latest = Place::with('category', 'reviews')
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();
        $categories = Category::withCount('places as place_count')
            ->whereHas('places', fn ($q) => $q->where('is_published', true))
            ->orderBy('sort_order')
            ->get();
        $igPosts = IgPost::latest('posted_at')->take(6)->get();

        return view('home', compact('dailyPicks', 'latest', 'categories', 'igPosts'));
    }
}
