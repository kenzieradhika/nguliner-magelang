<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Place;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Place::with('category')->withCount('reviews');

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        if ($category = $request->input('kategori')) {
            $query->where('category_id', $category);
        }

        $places = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.places.index', compact('places', 'categories'));
    }

    public function create(Request $request): View
    {
        $categories = Category::all();
        $place = new Place();

        if ($request->has('name')) {
            $place->name = $request->input('name');
            $place->address = $request->input('address');
            $place->description = $request->input('description');
            $place->category_id = $request->integer('category_id') ?: null;
        }

        return view('admin.places.form', compact('categories', 'place'));
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $this->validatePlace($request);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('places', 'public');
        }

        $place = Place::create($validated);
        $audit->log('place.created', $place, ['name' => $place->name]);

        return redirect()->route('admin.places.index')->with('success', 'Kuliner berhasil ditambahkan.');
    }

    public function edit(Place $place): View
    {
        $categories = Category::all();

        return view('admin.places.form', compact('categories', 'place'));
    }

    public function update(Request $request, Place $place, AuditService $audit): RedirectResponse
    {
        $validated = $this->validatePlace($request, $place);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('places', 'public');
        }

        $place->update($validated);
        $audit->log('place.updated', $place, ['name' => $place->name]);

        return redirect()->route('admin.places.index')->with('success', 'Kuliner berhasil diperbarui.');
    }

    public function destroy(Place $place, AuditService $audit): RedirectResponse
    {
        $name = $place->name;
        $place->delete();
        $audit->log('place.deleted', null, ['name' => $name]);

        return redirect()->route('admin.places.index')->with('success', 'Kuliner berhasil dihapus.');
    }

    private function validatePlace(Request $request, ?Place $place = null): array
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:places,slug' . ($place ? ",{$place->id}" : '')],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'open_days' => ['nullable', 'string', 'max:255'],
            'open_time' => ['nullable', 'string', 'max:10'],
            'close_time' => ['nullable', 'string', 'max:10'],
            'price_range' => ['nullable', 'string', 'max:100'],
            'tips' => ['nullable', 'string', 'max:500'],
            'since_year' => ['nullable', 'integer', 'min:1900', 'max:' . now()->year],
            'is_legendary' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];

        $data = $request->validate($rules);
        $data['is_legendary'] = $request->boolean('is_legendary');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published') || $request->has('is_published');

        return $data;
    }
}
