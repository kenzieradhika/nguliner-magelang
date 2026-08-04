@extends('layouts.app')

@section('meta_title', 'NGuliner Magelang — Referensi Kuliner No.1 di Magelang')

@section('head')
    <script type="application/ld+json">
    {
        {{ '@context' }}: "https://schema.org",
        "@type": "ItemList",
        "name": "Rekomendasi Kuliner Magelang",
        "itemListElement": [
            @foreach($latest as $i => $place)
            {
                "@type": "ListItem",
                "position": {{ $i + 1 }},
                "name": {{ json_encode($place->name) }},
                "url": {{ json_encode(route('place.show', $place->slug)) }}
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
@endsection

@section('content')
    <section class="relative overflow-hidden bg-neutral-950 text-white">
        <img src="{{ url('/img/hero.svg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40" loading="eager">
        <div class="ng-container relative flex min-h-[70vh] flex-col items-center justify-center py-24 text-center">
            <p class="mb-6 text-[11px] font-semibold uppercase tracking-[0.4em] text-neutral-400">Kuliner Magelang &amp; Sekitarnya</p>
            <h1 class="max-w-3xl text-4xl font-extrabold leading-tight tracking-tighter md:text-6xl">
                Referensi Kuliner<br class="hidden md:block">
                <span class="ng-serif font-semibold italic">No.1 di Magelang</span>
            </h1>
            <p class="mt-6 max-w-xl text-sm leading-relaxed text-neutral-400 md:text-base">
                Rekomendasi makan Magelang: bakso legendaris, es dawet, martabak, nasi goreng magelangan, hingga street food yang layak dicoba.
            </p>
            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="#rekomendasi" class="ng-btn !bg-white !text-neutral-900 hover:!bg-neutral-200">Lihat Rekomendasi</a>
                <a href="{{ route('map') }}" class="ng-btn-outline !border-white/30 !text-white hover:!bg-white hover:!text-neutral-900">Lihat Peta Kuliner</a>
            </div>
        </div>
    </section>

    <section id="rekomendasi" class="py-20">
        <div class="ng-container">
            <p class="ng-section-subtitle !mb-2">Rekomendasi Hari Ini</p>
            <h2 class="ng-section-title">Pilihan Terbaik Hari Ini</h2>
            <p class="ng-section-subtitle">Berganti otomatis setiap hari — dirotasi dari kuliner unggulan Magelang.</p>
            @if($dailyPicks->isEmpty())
                <p class="text-sm text-neutral-500">Belum ada rekomendasi untuk hari ini.</p>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($dailyPicks as $place)
                        <x-place-card :place="$place" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="border-y border-neutral-200 bg-neutral-50 py-20">
        <div class="ng-container">
            <p class="ng-section-subtitle !mb-2">Terbaru &amp; Legendaris</p>
            <div class="mb-10 flex items-end justify-between gap-4">
                <div>
                    <h2 class="ng-section-title">Rekomendasi Terbaru</h2>
                    <p class="ng-section-subtitle !mb-0">Update tempat makan terbaru dan legendaris di Magelang.</p>
                </div>
                <a href="{{ route('search') }}" class="hidden whitespace-nowrap text-sm font-medium underline-offset-4 hover:underline sm:block">Lihat Semua</a>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($latest as $place)
                    <x-place-card :place="$place" />
                @endforeach
            </div>
        </div>
    </section>

    <section id="kategori" class="py-20">
        <div class="ng-container">
            <p class="ng-section-subtitle !mb-2">Kategori</p>
            <h2 class="ng-section-title">Jelajahi Kategori Kuliner</h2>
            <p class="ng-section-subtitle">Dari bakso legendaris sampai street food kekinian.</p>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="group flex items-center justify-between rounded-2xl border border-neutral-200 px-6 py-6 transition hover:border-neutral-900 hover:bg-neutral-900 hover:text-white">
                        <div>
                            <p class="font-semibold">{{ $category->name }}</p>
                            <p class="mt-1 text-xs text-neutral-400">{{ $category->place_count }} tempat</p>
                        </div>
                        <svg class="h-4 w-4 text-neutral-300 transition group-hover:translate-x-1 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($igPosts->isNotEmpty())
        <section class="border-y border-neutral-200 bg-neutral-50 py-20">
            <div class="ng-container">
                <div class="mb-10 flex items-end justify-between gap-4">
                    <div>
                        <p class="ng-section-subtitle !mb-2">Instagram</p>
                        <h2 class="ng-section-title">@ngulinermagelang</h2>
                        <p class="ng-section-subtitle !mb-0">Update kuliner terbaru dari feed Instagram kami.</p>
                    </div>
                    <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="hidden whitespace-nowrap text-sm font-medium underline-offset-4 hover:underline sm:block">Follow Kami</a>
                </div>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    @foreach($igPosts as $post)
                        <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="group relative block overflow-hidden rounded-xl bg-neutral-200">
                            <img src="{{ $post->image_url }}" alt="{{ Str::limit(strip_tags($post->caption), 40) }}" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
                            <span class="absolute inset-0 flex items-center justify-center bg-neutral-900/0 text-white opacity-0 transition group-hover:bg-neutral-900/40 group-hover:opacity-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 3H8a5 5 0 0 0-5 5v8a5 5 0 0 0 5 5h8a5 5 0 0 0 5-5V8a5 5 0 0 0-5-5Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="kolaborasi" class="py-20">
        <div class="ng-container">
            <div class="overflow-hidden rounded-3xl bg-neutral-950 text-white">
                <div class="grid gap-10 p-10 md:grid-cols-2 md:p-16">
                    <div>
                        <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.3em] text-neutral-500">Kolaborasi</p>
                        <h2 class="text-3xl font-bold tracking-tight md:text-4xl">Bisnis, Iklan, &amp; UMKM</h2>
                        <p class="mt-5 max-w-md text-sm leading-relaxed text-neutral-400">
                            Kami terbuka untuk iklan, endorse, review resto/UMKM, dan partnership konten. Jangkau pecinta kuliner Magelang bersama kami.
                        </p>
                        <ul class="mt-8 space-y-3 text-sm">
                            <li class="flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-white"></span>Iklan &amp; Endorse</li>
                            <li class="flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-white"></span>Review resto / UMKM</li>
                            <li class="flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-white"></span>Partnership konten</li>
                        </ul>
                        <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('collaboration.create') }}" class="ng-btn !bg-white !text-neutral-900 hover:!bg-neutral-200">Ajukan Kolaborasi</a>
                            <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="ng-btn-outline !border-white/30 !text-white hover:!bg-white hover:!text-neutral-900">Instagram</a>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white p-8 text-neutral-900">
                        <h3 class="mb-5 text-base font-bold">Saran Tempat Baru</h3>
                        <p class="mb-6 text-sm text-neutral-500">Kenal tempat makan enak yang belum ada di NGuliner? Ceritakan ke kami.</p>
                        <form action="{{ route('suggestion.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="ng-label" for="s-name">Nama Tempat *</label>
                                <input id="s-name" name="name" required maxlength="150" class="ng-input" placeholder="mis. Sate Kambing Bu Sri">
                                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="ng-label" for="s-cat">Kategori</label>
                                    <input id="s-cat" name="category" maxlength="100" class="ng-input" placeholder="Bakso, Es Dawet...">
                                </div>
                                <div>
                                    <label class="ng-label" for="s-contact">Kontak</label>
                                    <input id="s-contact" name="contact" maxlength="150" class="ng-input" placeholder="IG/WA/email">
                                </div>
                            </div>
                            <div>
                                <label class="ng-label" for="s-addr">Alamat</label>
                                <input id="s-addr" name="address" maxlength="255" class="ng-input" placeholder="Jl. ...">
                            </div>
                            <div>
                                <label class="ng-label" for="s-desc">Cerita Singkat</label>
                                <textarea id="s-desc" name="description" rows="3" maxlength="2000" class="ng-input" placeholder="Kenapa tempat ini wajib direkomendasikan?"></textarea>
                            </div>
                            <button type="submit" class="ng-btn w-full">Kirim Saran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
