@extends('admin.layouts.app')

@section('title', 'Microsite')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Microsite UMKM</h1>
            <p class="mt-1 text-sm text-ink-500">Landing page khusus per resto/UMKM, dapat diakses di /{slug}</p>
        </div>
        @if($placesWithoutMicrosite->isNotEmpty())
            <form action="{{ route('admin.microsites.create') }}" method="GET" class="flex gap-2">
                <select name="place_id" required class="ng-input sm:!w-64">
                    <option value="">Pilih kuliner...</option>
                    @foreach($placesWithoutMicrosite as $place)
                        <option value="{{ $place->id }}">{{ $place->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="ng-btn-primary"><x-icon name="plus" class="h-4 w-4" /> Buat Microsite</button>
            </form>
        @endif
    </div>

    <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="px-5 py-3.5">Microsite</th>
                    <th class="px-5 py-3.5">Warna Tema</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach($microsites as $microsite)
                    <tr class="transition hover:bg-cream-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $microsite->hero_image ?: $microsite->place->image }}" alt="" class="h-11 w-11 rounded-lg object-cover">
                                <div>
                                    <p class="font-semibold">{{ $microsite->hero_title ?: $microsite->place->name }}</p>
                                    <p class="text-xs text-ink-400">/{{ $microsite->place->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-block h-5 w-5 rounded-full border border-ink-100" style="background-color: {{ $microsite->accent_color }}"></span>
                        </td>
                        <td class="px-5 py-4">
                            @if($microsite->is_active)
                                <span class="ng-badge ng-badge-green">Aktif</span>
                            @else
                                <span class="ng-badge ng-badge-neutral">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('microsite.show', $microsite->place->slug) }}" target="_blank" class="ng-btn-outline !px-3 !py-1.5 !text-xs"><x-icon name="eye" class="h-3 w-3" /> Lihat</a>
                                <a href="{{ route('admin.microsites.edit', $microsite) }}" class="ng-btn-outline !px-3 !py-1.5 !text-xs"><x-icon name="edit" class="h-3 w-3" /> Edit</a>
                                <form action="{{ route('admin.microsites.destroy', $microsite) }}" method="POST" onsubmit="return confirm('Hapus microsite ini?')">
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
    <div class="mt-6">{{ $microsites->links() }}</div>
@endsection
