<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollaborationController extends Controller
{
    public function create(): View
    {
        return view('collaboration');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'business_name' => ['nullable', 'string', 'max:150'],
            'type' => ['required', 'in:' . implode(',', Collaboration::TYPES)],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $collaboration = Collaboration::create($validated);

        NotificationService::collaborationSubmitted($collaboration);

        return back()->with('success', 'Terima kasih! Pengajuan kolaborasi kamu sudah kami terima.');
    }
}
