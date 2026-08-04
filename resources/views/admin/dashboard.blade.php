@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
            <p class="mt-1 text-sm text-neutral-500">Ringkasan aktivitas NGuliner Magelang</p>
        </div>
        <span class="text-sm text-neutral-400">{{ now()->format('l, d M Y') }}</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Total Kuliner</p>
            <p class="mt-2 text-3xl font-extrabold">{{ $stats['places'] }}</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Total Views</p>
            <p class="mt-2 text-3xl font-extrabold">{{ number_format($stats['views']) }}</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Review Menunggu</p>
            <p class="mt-2 text-3xl font-extrabold {{ $stats['pending_reviews'] ? 'text-red-600' : '' }}">{{ $stats['pending_reviews'] }}</p>
        </div>
        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Kolaborasi Baru</p>
            <p class="mt-2 text-3xl font-extrabold {{ $stats['new_collaborations'] ? 'text-red-600' : '' }}">{{ $stats['new_collaborations'] }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-bold">Kuliner per Kategori</h2>
            <div class="mt-5 space-y-3">
                @foreach($categoryStats as $category)
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ $category->name }}</span>
                            <span class="text-neutral-400">{{ $category->places_count }} tempat</span>
                        </div>
                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-neutral-100">
                            <div class="h-full rounded-full bg-neutral-900" style="width: {{ $stats['places'] ? ($category->places_count / max($stats['places'], 1)) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-bold">Kuliner Terpopuler</h2>
            <div class="mt-4 divide-y divide-neutral-100">
                @foreach($topPlaces as $index => $place)
                    <div class="flex items-center gap-4 py-3">
                        <span class="text-sm font-bold text-neutral-300">#{{ $index + 1 }}</span>
                        <img src="{{ $place->image }}" alt="" class="h-10 w-10 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $place->name }}</p>
                            <p class="text-xs text-neutral-400">{{ $place->category?->name }}</p>
                        </div>
                        <span class="text-xs text-neutral-400">{{ number_format($place->views) }}x dilihat</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-bold">Kolaborasi Terbaru</h2>
            <div class="mt-4 space-y-4">
                @forelse($latestCollaborations as $collab)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold">{{ $collab->name }}</p>
                            <p class="text-xs capitalize text-neutral-400">{{ $collab->type }} · {{ $collab->business_name ?: 'UMKM' }}</p>
                        </div>
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-neutral-500">{{ $collab->status }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400">Belum ada kolaborasi.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-bold">Review Terbaru</h2>
            <div class="mt-4 space-y-4">
                @forelse($latestReviews as $review)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold">{{ $review->name }} <span class="font-normal text-neutral-400">→ {{ $review->place?->name }}</span></p>
                            <p class="mt-0.5 line-clamp-1 text-xs text-neutral-500">{{ $review->comment }}</p>
                        </div>
                        <span class="rounded-full {{ $review->is_approved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} px-2.5 py-1 text-[10px] font-semibold uppercase">{{ $review->is_approved ? 'Disetujui' : 'Pending' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400">Belum ada review.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="text-sm font-bold">Saran Tempat</h2>
            <div class="mt-4 space-y-4">
                @forelse($latestSuggestions as $suggestion)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold">{{ $suggestion->name }}</p>
                            <p class="mt-0.5 line-clamp-1 text-xs text-neutral-500">{{ $suggestion->category ?: 'Kategori tidak diisi' }}</p>
                        </div>
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-neutral-500">{{ $suggestion->status }}</span>
                    </div>
                @empty
                    <p class="text-sm text-neutral-400">Belum ada saran.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
