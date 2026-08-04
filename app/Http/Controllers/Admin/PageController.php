<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::latest()->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        $page = new Page();

        return view('admin.pages.form', compact('page'));
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $this->validatePage($request);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $page = Page::create($validated);
        $audit->log('page.created', $page, ['title' => $page->title]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page, AuditService $audit): RedirectResponse
    {
        $validated = $this->validatePage($request, $page);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $page->update($validated);
        $audit->log('page.updated', $page, ['title' => $page->title]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page, AuditService $audit): RedirectResponse
    {
        $title = $page->title;
        $page->delete();
        $audit->log('page.deleted', null, ['title' => $title]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dihapus.');
    }

    private function validatePage(Request $request, ?Page $page = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:pages,slug' . ($page ? ",{$page->id}" : '')],
            'sections' => ['nullable', 'array'],
            'sections.*.type' => ['required', 'in:heading,text,image,list,cta,quote,embed'],
            'sections.*.content' => ['nullable', 'string'],
            'sections.*.items' => ['nullable', 'array'],
            'sections.*.items.*' => ['string'],
            'sections.*.url' => ['nullable', 'string', 'max:500'],
            'sections.*.button' => ['nullable', 'string', 'max:100'],
            'meta_title' => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);
        $data['is_published'] = $request->boolean('is_published');

        $sections = [];

        foreach ($data['sections'] ?? [] as $section) {
            if (! in_array($section['type'] ?? '', ['heading', 'text', 'image', 'list', 'cta', 'quote', 'embed'], true)) {
                continue;
            }

            if (($section['type'] ?? null) === 'list' && isset($section['items']) && is_string($section['items'])) {
                $section['items'] = array_values(array_filter(array_map('trim', explode("\n", $section['items']))));
            }

            $sections[] = $section;
        }

        $data['sections'] = $sections;

        return $data;
    }
}
