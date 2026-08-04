<?php

namespace App\Http\Controllers;

use App\Models\PlaceSuggestion;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuggestionController extends Controller
{
    public function create(): View
    {
        return view('suggestion');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact' => ['nullable', 'string', 'max:150'],
        ]);

        $suggestion = PlaceSuggestion::create($validated);

        NotificationService::suggestionSubmitted($suggestion);

        return back()->with('success', 'Makasih sudah berbagi! Saran tempatmu akan kami review.');
    }
}
