<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = Place::with('category', 'reviews')->where('is_published', true);

        if ($q = $request->input('q')) {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('tagline', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        if ($category = $request->input('kategori')) {
            $query->whereHas('category', fn ($builder) => $builder->where('slug', $category));
        }

        if ($request->boolean('buka')) {
            $query->get()->filter(fn (Place $place) => $place->isOpenNow());
            $openIds = Place::all()
                ->filter(fn (Place $place) => $place->isOpenNow())
                ->pluck('id');
            $query->whereIn('id', $openIds);
        }

        if ($request->boolean('legendaris')) {
            $query->where('is_legendary', true);
        }

        $sort = $request->input('sort', 'terbaru');
        $query->when($sort === 'rating', function ($builder) {
            $builder->withAvg(['approvedReviews as rating_avg' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->orderByDesc('rating_avg');
        })->when($sort === 'view', fn ($builder) => $builder->orderByDesc('views'))
            ->when($sort === 'harga', fn ($builder) => $builder->orderBy('price_range'))
            ->when($sort === 'terbaru', fn ($builder) => $builder->latest());

        $places = $query->paginate(12)->withQueryString();
        $categories = Category::whereHas('places', fn ($builder) => $builder->where('is_published', true))->get();

        return view('search', compact('places', 'categories'));
    }
}
