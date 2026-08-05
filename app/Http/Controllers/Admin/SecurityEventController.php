<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityEventController extends Controller
{
    public function index(Request $request): View
    {
        $events = SecurityEvent::query()
            ->when($request->input('severity'), fn ($q, $s) => $q->where('severity', $s))
            ->when($request->input('type'), fn ($q, $t) => $q->where('type', $t))
            ->when($request->input('unread') === '1', fn ($q) => $q->unread())
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.security.index', [
            'events' => $events,
            'totalUnread' => SecurityEvent::unread()->count(),
        ]);
    }

    public function markRead(SecurityEvent $event): RedirectResponse
    {
        $event->markRead();

        return back()->with('success', 'Insiden ditandai sudah dibaca.');
    }

    public function markAllRead(): RedirectResponse
    {
        SecurityEvent::unread()->update(['read_at' => now()]);

        return back()->with('success', 'Semua insiden ditandai sudah dibaca.');
    }

    public function destroy(SecurityEvent $event): RedirectResponse
    {
        $event->delete();

        return back()->with('success', 'Insiden dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        SecurityEvent::query()->delete();

        return back()->with('success', 'Semua insiden keamanan dihapus.');
    }
}
