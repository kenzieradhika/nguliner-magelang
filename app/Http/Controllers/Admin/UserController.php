<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:superadmin,editor'],
        ]);

        $user = User::create($validated);
        $audit->log('user.created', $user, ['name' => $user->name, 'role' => $user->role]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function updateRole(Request $request, User $user, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:superadmin,editor'],
        ]);

        $user->update($validated);
        $audit->log('user.role_updated', $user, ['role' => $validated['role']]);

        return back()->with('success', 'Role user diperbarui.');
    }

    public function destroy(User $user, AuditService $audit): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();
        $audit->log('user.deleted', null, ['name' => $name]);

        return back()->with('success', 'User dihapus.');
    }
}
