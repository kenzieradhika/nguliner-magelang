<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function show(string $slug): View
    {
        $place = Place::with('category', 'approvedReviews')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $place->increment('views');

        $related = Place::with('category')
            ->where('category_id', $place->category_id)
            ->where('id', '!=', $place->id)
            ->where('is_published', true)
            ->take(3)
            ->get();

        return view('place.show', compact('place', 'related'));
    }

    public function storeReview(Request $request, string $slug): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $place = Place::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $place->reviews()->create($validated);

        \App\Services\NotificationService::reviewSubmitted($place);

        return back()->with('success', 'Terima kasih! Review kamu akan tampil setelah dimoderasi.');
    }
}
