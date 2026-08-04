<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $places = Place::with('category', 'reviews')
            ->where('category_id', $category->id)
            ->where('is_published', true)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('category', compact('category', 'places'));
    }
}
