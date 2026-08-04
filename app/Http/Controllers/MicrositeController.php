<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\View\View;

class MicrositeController extends Controller
{
    public function show(string $slug): View
    {
        $place = Place::with('category', 'approvedReviews')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $microsite = $place->microsite;

        if (! $microsite?->is_active) {
            abort(404);
        }

        $microsite->place->increment('views');

        return view('microsite.show', compact('place', 'microsite'));
    }
}
