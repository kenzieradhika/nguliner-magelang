@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Manajemen Pengguna</h1>
            <p class="mt-1 text-sm text-ink-500">Kelola akun admin NGuliner</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-ink-100 bg-white p-6">
            <h2 class="text-sm font-bold">Tambah Pengguna</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="ng-label" for="name">Nama *</label>
                    <input id="name" name="name" required maxlength="100" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="email">Email *</label>
                    <input id="email" name="email" type="email" required maxlength="150" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="password">Password * (min. 8)</label>
                    <input id="password" name="password" type="password" required minlength="8" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="role">Role *</label>
                    <select id="role" name="role" class="ng-input">
                        <option value="editor">Editor</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <button type="submit" class="ng-btn w-full">Simpan</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white lg:col-span-2">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                    <tr>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Role</th>
                        <th class="px-5 py-3.5">Dibuat</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <p class="font-semibold">{{ $user->name }} @if($user->id === auth()->id())<span class="text-xs text-ink-400">(kamu)</span>@endif</p>
                                        <p class="text-xs text-ink-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.users.role', $user) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="ng-input !w-32 !py-1.5">
                                        <option value="editor" @selected($user->role === 'editor')>Editor</option>
                                        <option value="superadmin" @selected($user->role === 'superadmin')>Superadmin</option>
                                    </select>
                                    <button type="submit" class="rounded-lg border border-ink-100 px-2.5 py-1.5 text-xs transition hover:bg-cream-100">Simpan</button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-xs text-ink-400">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end">
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50" {{ $user->id === auth()->id() ? 'disabled' : '' }}>Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="border-t border-ink-100 p-4">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
