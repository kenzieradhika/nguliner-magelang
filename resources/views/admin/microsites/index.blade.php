@extends('admin.layouts.app')

@section('title', 'Microsite')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Landing page UMKM</p>
            <h2 class="adm-page-title">Microsite UMKM</h2>
            <p class="adm-page-subtitle">Landing page khusus per resto/UMKM, dapat diakses di /{slug}</p>
        </div>
        @if($placesWithoutMicrosite->isNotEmpty())
            <form action="{{ route('admin.microsites.create') }}" method="GET" class="flex flex-wrap gap-2">
                <select name="place_id" required class="adm-input sm:!w-64">
                    <option value="">Pilih kuliner...</option>
                    @foreach($placesWithoutMicrosite as $place)
                        <option value="{{ $place->id }}">{{ $place->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="adm-btn"><x-icon name="plus" class="h-4 w-4" /> Buat Microsite</button>
            </form>
        @endif
    </div>

    <div class="adm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="adm-table min-w-[700px]">
                <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                    <tr>
                        <th class="adm-th">Microsite</th>
                        <th class="adm-th">Warna Tema</th>
                        <th class="adm-th">Status</th>
                        <th class="adm-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/[0.05]">
                    @foreach($microsites as $microsite)
                        <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                            <td class="adm-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $microsite->hero_image ?: $microsite->place->image }}" alt="" class="h-11 w-11 shrink-0 rounded-xl border border-ink-900/[0.06] object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-ink-900">{{ $microsite->hero_title ?: $microsite->place->name }}</p>
                                        <p class="text-xs text-ink-400">/{{ $microsite->place->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="adm-td">
                                <span class="inline-block h-5 w-5 rounded-full border border-ink-900/10 shadow-sm" style="background-color: {{ $microsite->accent_color }}"></span>
                            </td>
                            <td class="adm-td">
                                @if($microsite->is_active)
                                    <span class="adm-badge adm-badge-soft-green">Aktif</span>
                                @else
                                    <span class="adm-badge adm-badge-soft-neutral">Nonaktif</span>
                                @endif
                            </td>
                            <td class="adm-td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('microsite.show', $microsite->place->slug) }}" target="_blank" class="adm-btn-ghost"><x-icon name="eye" class="h-3.5 w-3.5" /> Lihat</a>
                                    <a href="{{ route('admin.microsites.edit', $microsite) }}" class="adm-btn-ghost"><x-icon name="edit" class="h-3.5 w-3.5" /> Edit</a>
                                    <form action="{{ route('admin.microsites.destroy', $microsite) }}" method="POST" onsubmit="return confirm('Hapus microsite ini?')">
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
    <div class="mt-6">{{ $microsites->links() }}</div>
@endsection
