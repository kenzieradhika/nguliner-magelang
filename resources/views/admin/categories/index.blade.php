@extends('admin.layouts.app')

@section('title', 'Kategori')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Taksonomi kuliner</p>
            <h2 class="adm-page-title">Kategori Kuliner</h2>
            <p class="adm-page-subtitle">{{ $categories->total() }} kategori terdaftar</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="adm-card h-fit p-6">
            <h2 class="adm-card-title"><x-icon name="plus" class="h-3.5 w-3.5 text-sambal-600" /> Tambah Kategori</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="adm-label" for="name">Nama *</label>
                    <input id="name" name="name" required maxlength="100" class="adm-input" placeholder="mis. Soto">
                </div>
                <div>
                    <label class="adm-label" for="description">Deskripsi</label>
                    <input id="description" name="description" maxlength="255" class="adm-input">
                </div>
                <button type="submit" class="adm-btn w-full">Simpan</button>
            </form>
        </div>

        <div class="adm-card overflow-hidden lg:col-span-2">
            <div class="overflow-x-auto">
                <table class="adm-table min-w-[560px]">
                    <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                        <tr>
                            <th class="adm-th">Kategori</th>
                            <th class="adm-th">Slug</th>
                            <th class="adm-th">Jumlah Kuliner</th>
                            <th class="adm-th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-900/[0.05]">
                        @foreach($categories as $category)
                            <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                                <td class="adm-td">
                                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" value="{{ $category->name }}" required class="adm-input !py-1.5">
                                </td>
                                <td class="adm-td text-xs text-ink-400">{{ $category->slug }}</td>
                                <td class="adm-td font-semibold text-ink-500">{{ $category->places_count }} tempat</td>
                                <td class="adm-td">
                                    <div class="flex justify-end gap-1.5">
                                        <button type="submit" class="adm-btn-ghost"><x-icon name="check" class="h-3.5 w-3.5" /> Simpan</button>
                                    </form>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->name }}? Kuliner terkait ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-900/[0.06] px-5 py-4">{{ $categories->links() }}</div>
        </div>
    </div>
@endsection
