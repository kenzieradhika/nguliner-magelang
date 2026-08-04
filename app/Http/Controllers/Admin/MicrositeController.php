<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Microsite;
use App\Models\Place;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MicrositeController extends Controller
{
    public function index(): View
    {
        $microsites = Microsite::with('place')->latest()->paginate(15);
        $placesWithoutMicrosite = Place::whereDoesntHave('microsite')->get();

        return view('admin.microsites.index', compact('microsites', 'placesWithoutMicrosite'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $placeId = $request->integer('place_id');

        if (! $placeId) {
            return redirect()->route('admin.microsites.index')
                ->with('error', 'Pilih tempat kuliner dulu untuk membuat microsite.');
        }

        $place = Place::findOrFail($placeId);
        $microsite = new Microsite(['place_id' => $place->id, 'accent_color' => '#111111']);

        return view('admin.microsites.form', compact('microsite', 'place'));
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $this->validateMicrosite($request);

        $microsite = Microsite::create($validated);
        $audit->log('microsite.created', $microsite, ['place_id' => $microsite->place_id]);

        return redirect()->route('admin.microsites.index')->with('success', 'Microsite berhasil dibuat.');
    }

    public function edit(Microsite $microsite): View
    {
        $place = $microsite->place;

        return view('admin.microsites.form', compact('microsite', 'place'));
    }

    public function update(Request $request, Microsite $microsite, AuditService $audit): RedirectResponse
    {
        $validated = $this->validateMicrosite($request, $microsite);
        $validated['place_id'] = $microsite->place_id;

        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $request->file('hero_image')->store('microsites', 'public');
        }

        $microsite->update($validated);
        $audit->log('microsite.updated', $microsite, ['place_id' => $microsite->place_id]);

        return redirect()->route('admin.microsites.index')->with('success', 'Microsite berhasil diperbarui.');
    }

    public function destroy(Microsite $microsite, AuditService $audit): RedirectResponse
    {
        $placeId = $microsite->place_id;
        $microsite->delete();
        $audit->log('microsite.deleted', null, ['place_id' => $placeId]);

        return redirect()->route('admin.microsites.index')->with('success', 'Microsite berhasil dihapus.');
    }

    private function validateMicrosite(Request $request, ?Microsite $microsite = null): array
    {
        $rules = [
            'place_id' => ['required', 'exists:places,id'],
            'hero_title' => ['nullable', 'string', 'max:200'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'about' => ['nullable', 'string'],
            'menu' => ['nullable', 'array'],
            'menu.*.name' => ['required', 'string'],
            'menu.*.desc' => ['nullable', 'string'],
            'menu.*.price' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'string'],
            'socials' => ['nullable', 'array'],
            'socials.instagram' => ['nullable', 'string', 'max:500'],
            'socials.tiktok' => ['nullable', 'string', 'max:500'],
            'socials.whatsapp' => ['nullable', 'string', 'max:30'],
            'socials.website' => ['nullable', 'string', 'max:500'],
            'map_embed' => ['nullable', 'string', 'max:2000'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);

        if (! $request->hasFile('hero_image') && $microsite?->hero_image) {
            $data['hero_image'] = $microsite->hero_image;
        }

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
