@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('section', 'Ringkasan')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Selamat datang kembali</p>
            <h2 class="adm-page-title">{{ auth()->user()->name }}</h2>
            <p class="adm-page-subtitle">Kondisi NGuliner Magelang hari ini — {{ now()->format('d F Y') }}</p>
        </div>
        <a href="{{ route('admin.places.create') }}" class="adm-btn">
            <x-icon name="plus" class="h-4 w-4" /> Tambah Kuliner
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $statsCard = [
                ['label' => 'Total Kuliner', 'value' => $stats['places'], 'icon' => 'utensils', 'sub' => $stats['categories'] . ' kategori'],
                ['label' => 'Total Views', 'value' => number_format($stats['views']), 'icon' => 'eye', 'sub' => 'akumulasi kunjungan'],
                ['label' => 'Review Menunggu', 'value' => $stats['pending_reviews'], 'icon' => 'star', 'sub' => 'perlu moderasi', 'accent' => $stats['pending_reviews'] > 0],
                ['label' => 'Kolaborasi Baru', 'value' => $stats['new_collaborations'], 'icon' => 'briefcase', 'sub' => 'pengajuan masuk', 'accent' => $stats['new_collaborations'] > 0],
            ];
        @endphp
        @foreach($statsCard as $card)
            <div class="adm-card group relative overflow-hidden p-6 transition-transform duration-300 hover:-translate-y-0.5">
                <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-sambal-600/[0.06] transition-transform duration-500 group-hover:scale-125"></div>
                <div class="flex items-center justify-between">
                    <p class="adm-stat-label">{{ $card['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-ink-900/[0.06] text-sambal-600">
                        <x-icon name="{{ $card['icon'] }}" class="h-4 w-4" />
                    </span>
                </div>
                <p class="adm-stat-value mt-4 {{ ($card['accent'] ?? false) ? 'text-sambal-600' : '' }}">{{ $card['value'] }}</p>
                <p class="mt-2 text-xs text-ink-400">{{ $card['sub'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="adm-card p-6">
            <h2 class="adm-card-title"><x-icon name="tag" class="h-3.5 w-3.5 text-sambal-600" /> Distribusi per Kategori</h2>
            <div class="space-y-5">
                @foreach($categoryStats as $category)
                    @php $pct = $stats['places'] ? ($category->places_count / max($stats['places'], 1)) * 100 : 0; @endphp
                    <div>
                        <div class="mb-2 flex items-baseline justify-between gap-3">
                            <span class="text-sm font-semibold text-ink-800">{{ $category->name }}</span>
                            <span class="text-xs text-ink-400">{{ $category->places_count }} tempat</span>
                        </div>
                        <div class="adm-progress-track">
                            <div class="adm-progress-fill transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="adm-card p-6">
            <div class="flex items-center justify-between">
                <h2 class="adm-card-title mb-0"><x-icon name="trophy" class="h-3.5 w-3.5 text-sambal-600" /> Paling Banyak Dilihat</h2>
            </div>
            <div class="mt-4 divide-y divide-ink-900/[0.05]">
                @foreach($topPlaces as $index => $place)
                    <div class="adm-list-row">
                        <span class="w-8 shrink-0 font-display text-xl font-bold {{ $index === 0 ? 'text-sambal-600' : 'text-ink-300' }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <img src="{{ $place->image }}" alt="" class="h-11 w-11 shrink-0 rounded-xl border border-ink-900/[0.06] object-cover" loading="lazy">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink-800">{{ $place->name }}</p>
                            <p class="text-xs text-ink-400">{{ $place->category?->name }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-semibold text-ink-500">{{ number_format($place->views) }} <span class="font-normal text-ink-300">x</span></span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="adm-card p-6">
            <h2 class="adm-card-title"><x-icon name="briefcase" class="h-3.5 w-3.5 text-sambal-600" /> Kolaborasi Terbaru</h2>
            <div class="space-y-4">
                @forelse($latestCollaborations as $collab)
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink-800">{{ $collab->name }}</p>
                            <p class="mt-0.5 text-xs capitalize text-ink-400">{{ $collab->type }} · {{ $collab->business_name ?: 'UMKM' }}</p>
                        </div>
                        <span class="adm-badge adm-badge-soft-neutral shrink-0">{{ $collab->status }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-ink-400">Belum ada kolaborasi.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.collaborations.index') }}" class="mt-6 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-sambal-600 transition hover:text-sambal-700">
                Lihat semua <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        <div class="adm-card p-6">
            <h2 class="adm-card-title"><x-icon name="star" class="h-3.5 w-3.5 text-sambal-600" /> Review Terbaru</h2>
            <div class="space-y-4">
                @forelse($latestReviews as $review)
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink-800">{{ $review->name }} <span class="font-normal text-ink-400">→ {{ $review->place?->name }}</span></p>
                            <p class="mt-0.5 line-clamp-1 text-xs text-ink-500">{{ $review->comment }}</p>
                        </div>
                        <span class="adm-badge {{ $review->is_approved ? 'adm-badge-soft-green' : 'adm-badge-soft-amber' }} shrink-0">{{ $review->is_approved ? 'Disetujui' : 'Pending' }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-ink-400">Belum ada review.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.reviews.index') }}" class="mt-6 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-sambal-600 transition hover:text-sambal-700">
                Lihat semua <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>

        <div class="adm-card p-6">
            <h2 class="adm-card-title"><x-icon name="lightbulb" class="h-3.5 w-3.5 text-sambal-600" /> Saran Tempat</h2>
            <div class="space-y-4">
                @forelse($latestSuggestions as $suggestion)
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink-800">{{ $suggestion->name }}</p>
                            <p class="mt-0.5 line-clamp-1 text-xs text-ink-500">{{ $suggestion->category ?: 'Kategori tidak diisi' }}</p>
                        </div>
                        <span class="adm-badge {{ $suggestion->status === 'new' ? 'adm-badge-soft-amber' : 'adm-badge-soft-neutral' }} shrink-0">{{ $suggestion->status }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-ink-400">Belum ada saran.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.suggestions.index') }}" class="mt-6 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-sambal-600 transition hover:text-sambal-700">
                Lihat semua <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </div>
    </div>
@endsection
