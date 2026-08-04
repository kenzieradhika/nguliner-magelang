<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('places')->latest()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);
        $audit->log('category.created', $category, ['name' => $category->name]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);
        $audit->log('category.updated', $category, ['name' => $category->name]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category, AuditService $audit): RedirectResponse
    {
        $name = $category->name;
        $category->delete();
        $audit->log('category.deleted', null, ['name' => $name]);

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
