<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\View\View;

class MapController extends Controller
{
    public function __invoke(): View
    {
        $places = Place::with('category')
            ->where('is_published', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('map', compact('places'));
    }
}
