@extends('admin.layouts.app')

@section('title', 'Kelola Kuliner')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Kelola Kuliner</h1>
            <p class="mt-1 text-sm text-neutral-500">{{ $places->total() }} tempat terdaftar</p>
        </div>
        <a href="{{ route('admin.places.create') }}" class="ng-btn">+ Tambah Kuliner</a>
    </div>

    <div class="mb-6 rounded-2xl border border-neutral-200 bg-white p-4">
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

    <div class="overflow-x-auto rounded-2xl border border-neutral-200 bg-white">
        <table class="w-full min-w-[800px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase tracking-wider text-neutral-400">
                <tr>
                    <th class="px-5 py-3.5">Kuliner</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5">Views</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach($places as $place)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $place->image }}" alt="" class="h-11 w-11 rounded-lg object-cover">
                                <div>
                                    <p class="font-semibold">{{ $place->name }}</p>
                                    <p class="flex items-center gap-2 text-xs text-neutral-400">
                                        <span>{{ $place->reviews_count }} review</span>
                                        @if($place->is_legendary)<span class="ng-tag !px-2 !py-0.5 !text-[10px]">Legendaris</span>@endif
                                        @if($place->is_featured)<span class="ng-tag !px-2 !py-0.5 !text-[10px]">Featured</span>@endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-neutral-500">{{ $place->category?->name }}</td>
                        <td class="px-5 py-4">
                            @if($place->is_published)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-green-700">Tayang</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-neutral-500">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-neutral-500">{{ number_format($place->views) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('place.show', $place->slug) }}" target="_blank" class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs transition hover:bg-neutral-100">Lihat</a>
                                <a href="{{ route('admin.places.edit', $place) }}" class="rounded-lg border border-neutral-200 px-3 py-1.5 text-xs transition hover:bg-neutral-100">Edit</a>
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
