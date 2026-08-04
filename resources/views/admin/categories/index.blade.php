@extends('admin.layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="mb-8">
        <h1 class="ng-page-title">Kategori Kuliner</h1>
        <p class="mt-1 text-sm text-ink-500">{{ $categories->total() }} kategori</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-ink-100 bg-white p-6">
            <h2 class="text-sm font-bold">Tambah Kategori</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="ng-label" for="name">Nama *</label>
                    <input id="name" name="name" required maxlength="100" class="ng-input" placeholder="mis. Soto">
                </div>
                <div>
                    <label class="ng-label" for="description">Deskripsi</label>
                    <input id="description" name="description" maxlength="255" class="ng-input">
                </div>
                <button type="submit" class="ng-btn w-full">Simpan</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white lg:col-span-2">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                    <tr>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Slug</th>
                        <th class="px-5 py-3.5">Jumlah Kuliner</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach($categories as $category)
                        <tr>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input name="name" value="{{ $category->name }}" required class="ng-input !py-1.5">
                            </td>
                            <td class="px-5 py-4 text-ink-400">{{ $category->slug }}</td>
                            <td class="px-5 py-4">{{ $category->places_count }} tempat</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="submit" class="rounded-lg border border-ink-100 px-3 py-1.5 text-xs transition hover:bg-cream-100">Simpan</button>
                                </form>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->name }}? Kuliner terkait ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 transition hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="border-t border-ink-100 p-4">{{ $categories->links() }}</div>
        </div>
    </div>
@endsection
