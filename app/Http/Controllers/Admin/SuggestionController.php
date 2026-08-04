<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlaceSuggestion;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuggestionController extends Controller
{
    public function index(Request $request): View
    {
        $query = PlaceSuggestion::query();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $suggestions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.suggestions.index', compact('suggestions'));
    }

    public function updateStatus(Request $request, PlaceSuggestion $suggestion, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', PlaceSuggestion::STATUSES)],
        ]);

        $suggestion->update($validated);
        $audit->log('suggestion.status', $suggestion, ['status' => $validated['status']]);

        return back()->with('success', 'Status saran diperbarui.');
    }

    public function destroy(PlaceSuggestion $suggestion, AuditService $audit): RedirectResponse
    {
        $name = $suggestion->name;
        $suggestion->delete();
        $audit->log('suggestion.deleted', null, ['name' => $name]);

        return back()->with('success', 'Saran dihapus.');
    }
}
