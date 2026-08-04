@extends('admin.layouts.app')

@section('title', 'Kelola Kuliner')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Kelola Kuliner</h1>
            <p class="mt-1 text-sm text-ink-500">{{ $places->total() }} tempat terdaftar</p>
        </div>
        <a href="{{ route('admin.places.create') }}" class="ng-btn-primary"><x-icon name="plus" class="h-4 w-4" /> Tambah Kuliner</a>
    </div>

    <div class="mb-6 rounded-2xl border border-ink-100 bg-white p-4">
        <form action="{{ route('admin.places.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kuliner..." class="ng-input flex-1">
            <select name="kategori" class="ng-input sm:!w-52">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('kategori') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="ng-btn">Filter</button>
            @if(request('q') || request('kategori'))
                <a href="{{ route('admin.places.index') }}" class="ng-btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white">
        <table class="w-full min-w-[800px] text-left text-sm">
            <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="px-5 py-3.5">Kuliner</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Views</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach($places as $place)
                    <tr class="transition hover:bg-cream-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $place->image }}" alt="" class="h-11 w-11 rounded-lg object-cover">
                                <div>
                                    <p class="font-semibold">{{ $place->name }}</p>
                                    <p class="flex items-center gap-2 text-xs text-ink-400">
                                        <span>{{ $place->reviews_count }} review</span>
                                        @if($place->is_legendary)<span class="ng-tag !px-2 !py-0.5 !text-[10px]">Legendaris</span>@endif
                                        @if($place->is_featured)<span class="ng-tag !px-2 !py-0.5 !text-[10px]">Featured</span>@endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-ink-500">{{ $place->category?->name }}</td>
                        <td class="px-5 py-4">
                            @if($place->is_published)
                                <span class="ng-badge ng-badge-green">Tayang</span>
                            @else
                                <span class="ng-badge ng-badge-neutral">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-ink-500">{{ number_format($place->views) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('place.show', $place->slug) }}" target="_blank" class="ng-btn-outline !px-3 !py-1.5 !text-xs"><x-icon name="eye" class="h-3 w-3" /> Lihat</a>
                                <a href="{{ route('admin.places.edit', $place) }}" class="ng-btn-outline !px-3 !py-1.5 !text-xs"><x-icon name="edit" class="h-3 w-3" /> Edit</a>
                                <form action="{{ route('admin.places.destroy', $place) }}" method="POST" onsubmit="return confirm('Hapus kuliner ini?')">
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
    </div>
    <div class="mt-6">{{ $places->links() }}</div>
@endsection
