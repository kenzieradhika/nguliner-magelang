@extends('admin.layouts.app')

@section('title', 'Kelola Kuliner')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Direktori kuliner</p>
            <h2 class="adm-page-title">Kelola Kuliner</h2>
            <p class="adm-page-subtitle">{{ $places->total() }} tempat terdaftar di NGuliner Magelang</p>
        </div>
        <a href="{{ route('admin.places.create') }}" class="adm-btn"><x-icon name="plus" class="h-4 w-4" /> Tambah Kuliner</a>
    </div>

    <div class="adm-card mb-6 p-4">
        <form action="{{ route('admin.places.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-300" />
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kuliner..." class="adm-input pl-10">
            </div>
            <select name="kategori" class="adm-input sm:!w-52">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('kategori') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="adm-btn px-6">Filter</button>
            @if(request('q') || request('kategori'))
                <a href="{{ route('admin.places.index') }}" class="adm-btn-secondary px-6">Reset</a>
            @endif
        </form>
    </div>

    <div class="adm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="adm-table min-w-[800px]">
                <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                    <tr>
                        <th class="adm-th">Kuliner</th>
                        <th class="adm-th">Kategori</th>
                        <th class="adm-th">Status</th>
                        <th class="adm-th">Views</th>
                        <th class="adm-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/[0.05]">
                    @foreach($places as $place)
                        <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                            <td class="adm-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $place->image }}" alt="" class="h-11 w-11 shrink-0 rounded-xl border border-ink-900/[0.06] object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-ink-900">{{ $place->name }}</p>
                                        <p class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-ink-400">
                                            <span>{{ $place->reviews_count }} review</span>
                                            @if($place->is_legendary)<span class="rounded-full bg-ink-900 px-2 py-0.5 text-[10px] font-bold text-white">Legendaris</span>@endif
                                            @if($place->is_featured)<span class="rounded-full bg-sambal-600 px-2 py-0.5 text-[10px] font-bold text-white">Featured</span>@endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="adm-td text-ink-500">{{ $place->category?->name }}</td>
                            <td class="adm-td">
                                @if($place->is_published)
                                    <span class="adm-badge adm-badge-soft-green">Tayang</span>
                                @else
                                    <span class="adm-badge adm-badge-soft-neutral">Draft</span>
                                @endif
                            </td>
                            <td class="adm-td font-semibold tabular-nums text-ink-500">{{ number_format($place->views) }}</td>
                            <td class="adm-td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('place.show', $place->slug) }}" target="_blank" class="adm-btn-ghost"><x-icon name="eye" class="h-3.5 w-3.5" /> Lihat</a>
                                    <a href="{{ route('admin.places.edit', $place) }}" class="adm-btn-ghost"><x-icon name="edit" class="h-3.5 w-3.5" /> Edit</a>
                                    <form action="{{ route('admin.places.destroy', $place) }}" method="POST" onsubmit="return confirm('Hapus kuliner ini?')">
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
    </div>
    <div class="mt-6">{{ $places->links() }}</div>
@endsection
