@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')
@section('section', 'Sistem')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Akses & peran</p>
            <h2 class="adm-page-title">Manajemen Pengguna</h2>
            <p class="adm-page-subtitle">Kelola akun admin NGuliner</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="adm-card h-fit p-6">
            <h2 class="adm-card-title"><x-icon name="users" class="h-3.5 w-3.5 text-sambal-600" /> Tambah Pengguna</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="adm-label" for="name">Nama *</label>
                    <input id="name" name="name" required maxlength="100" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="email">Email *</label>
                    <input id="email" name="email" type="email" required maxlength="150" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="password">Password * (min. 8)</label>
                    <input id="password" name="password" type="password" required minlength="8" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="role">Role *</label>
                    <select id="role" name="role" class="adm-input">
                        <option value="editor">Editor</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <button type="submit" class="adm-btn w-full">Simpan</button>
            </form>
        </div>

        <div class="adm-card overflow-hidden lg:col-span-2">
            <div class="overflow-x-auto">
                <table class="adm-table min-w-[560px]">
                    <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                        <tr>
                            <th class="adm-th">Pengguna</th>
                            <th class="adm-th">Role</th>
                            <th class="adm-th">Dibuat</th>
                            <th class="adm-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-900/[0.05]">
                        @foreach($users as $user)
                            <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                                <td class="adm-td">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-600 text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink-900">{{ $user->name }} @if($user->id === auth()->id())<span class="text-xs font-normal text-ink-400">(kamu)</span>@endif</p>
                                            <p class="truncate text-xs text-ink-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="adm-td">
                                    <form action="{{ route('admin.users.role', $user) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="adm-input !w-32 !py-1.5">
                                            <option value="editor" @selected($user->role === 'editor')>Editor</option>
                                            <option value="superadmin" @selected($user->role === 'superadmin')>Superadmin</option>
                                        </select>
                                        <button type="submit" class="adm-btn-ghost"><x-icon name="check" class="h-3.5 w-3.5" /></button>
                                    </form>
                                </td>
                                <td class="adm-td text-xs text-ink-400">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="adm-td">
                                    <div class="flex justify-end">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="adm-btn-danger" {{ $user->id === auth()->id() ? 'disabled' : '' }}><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-900/[0.06] px-5 py-4">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
