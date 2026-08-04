<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collaboration;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollaborationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Collaboration::query();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $collaborations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.collaborations.index', compact('collaborations'));
    }

    public function updateStatus(Request $request, Collaboration $collaboration, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Collaboration::STATUSES)],
        ]);

        $collaboration->update($validated);
        $audit->log('collaboration.status', $collaboration, ['status' => $validated['status']]);

        return back()->with('success', 'Status kolaborasi diperbarui.');
    }

    public function destroy(Collaboration $collaboration, AuditService $audit): RedirectResponse
    {
        $name = $collaboration->name;
        $collaboration->delete();
        $audit->log('collaboration.deleted', null, ['name' => $name]);

        return back()->with('success', 'Kolaborasi dihapus.');
    }
}
