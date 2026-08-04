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
    <section class="relative overflow-hidden bg-ink-900 text-white">
        <img src="{{ url('/img/hero.svg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-35" loading="eager">
        <div class="absolute inset-0 bg-gradient-to-b from-ink-900/60 via-transparent to-ink-900/80"></div>
        <div class="ng-container relative flex min-h-[72vh] flex-col items-center justify-center py-24 text-center">
            <p class="mb-6 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.4em] text-sambal-400">
                <x-icon name="compass" class="h-3.5 w-3.5" /> Kuliner Magelang &amp; Sekitarnya
            </p>
            <h1 class="max-w-3xl font-display text-4xl font-bold leading-tight tracking-tight md:text-6xl">
                Referensi Kuliner<br class="hidden md:block">
                <span class="italic text-sambal-400">No.1 di Magelang</span>
            </h1>
            <p class="mt-6 max-w-xl text-sm leading-relaxed text-ink-100/80 md:text-base">
                Rekomendasi makan Magelang: bakso legendaris, es dawet, martabak, nasi goreng magelangan, hingga street food yang layak dicoba.
            </p>
            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="#rekomendasi" class="ng-btn-primary !px-8 !py-3.5">Lihat Rekomendasi</a>
                <a href="{{ route('map') }}" class="ng-btn-outline !border-white/30 !text-white hover:!bg-white hover:!text-ink-900">Lihat Peta Kuliner</a>
            </div>
            <div class="mt-16 grid w-full max-w-2xl grid-cols-3 gap-4 border-t border-white/15 pt-8 text-center">
                <div>
                    <p class="font-display text-2xl font-bold text-sambal-400 md:text-3xl">{{ $categories->sum('place_count') }}+</p>
                    <p class="mt-1 text-[11px] uppercase tracking-widest text-ink-100/60">Tempat Kuliner</p>
                </div>
                <div>
                    <p class="font-display text-2xl font-bold text-sambal-400 md:text-3xl">{{ $categories->count() }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-widest text-ink-100/60">Kategori</p>
                </div>
                <div>
                    <p class="font-display text-2xl font-bold text-sambal-400 md:text-3xl">{{ $igPosts->count() }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-widest text-ink-100/60">Update IG</p>
                </div>
            </div>
        </div>
    </section>

    <section id="rekomendasi" class="py-20 md:py-24">
        <div class="ng-container">
            <p class="ng-eyebrow">Rekomendasi Hari Ini</p>
            <h2 class="ng-section-title">Pilihan Terbaik Hari Ini</h2>
            <p class="ng-section-subtitle">Berganti otomatis setiap hari — dirotasi dari kuliner unggulan Magelang.</p>
            @if($dailyPicks->isEmpty())
                <p class="mt-8 text-sm text-ink-500">Belum ada rekomendasi untuk hari ini.</p>
            @else
                <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($dailyPicks as $place)
                        <x-place-card :place="$place" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="border-y border-ink-100 bg-cream-100 py-20 md:py-24">
        <div class="ng-container">
            <p class="ng-eyebrow">Terbaru &amp; Legendaris</p>
            <div class="mb-10 flex items-end justify-between gap-4">
                <div>
                    <h2 class="ng-section-title">Rekomendasi Terbaru</h2>
                    <p class="ng-section-subtitle">Update tempat makan terbaru dan legendaris di Magelang.</p>
                </div>
                <a href="{{ route('search') }}" class="group hidden items-center gap-1.5 whitespace-nowrap text-sm font-bold text-sambal-600 underline-offset-4 transition hover:text-sambal-700 hover:underline sm:flex">
                    Lihat Semua
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" />
                </a>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($latest as $place)
                    <x-place-card :place="$place" />
                @endforeach
            </div>
        </div>
    </section>

    <section id="kategori" class="py-20 md:py-24">
        <div class="ng-container">
            <p class="ng-eyebrow">Kategori</p>
            <h2 class="ng-section-title">Jelajahi Kategori Kuliner</h2>
            <p class="ng-section-subtitle">Dari bakso legendaris sampai street food kekinian.</p>
            <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-3">
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="group flex items-center justify-between rounded-2xl border border-ink-100 bg-white px-6 py-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-ink-900 hover:bg-ink-900 hover:text-white hover:shadow-lg">
                        <div>
                            <p class="font-bold">{{ $category->name }}</p>
                            <p class="mt-1 text-xs text-ink-400 group-hover:text-ink-100/60">{{ $category->place_count }} tempat</p>
                        </div>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cream-100 text-sambal-600 transition group-hover:bg-white/10 group-hover:text-white">
                            <x-icon name="chevron-right" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($igPosts->isNotEmpty())
        <section class="border-y border-ink-100 bg-cream-100 py-20 md:py-24">
            <div class="ng-container">
                <div class="mb-10 flex items-end justify-between gap-4">
                    <div>
                        <p class="ng-eyebrow">Instagram</p>
                        <h2 class="ng-section-title">@ngulinermagelang</h2>
                        <p class="ng-section-subtitle">Update kuliner terbaru dari feed Instagram kami.</p>
                    </div>
                    <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="group hidden items-center gap-1.5 whitespace-nowrap text-sm font-bold text-sambal-600 underline-offset-4 transition hover:text-sambal-700 hover:underline sm:flex">
                        <x-icon name="instagram" class="h-3.5 w-3.5" /> Follow Kami
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    @foreach($igPosts as $post)
                        <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="group relative block overflow-hidden rounded-xl bg-ink-100 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                            <img src="{{ $post->image_url }}" alt="{{ Str::limit(strip_tags($post->caption), 40) }}" loading="lazy" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105">
                            <span class="absolute inset-0 flex items-center justify-center bg-ink-900/0 text-white opacity-0 transition duration-200 group-hover:bg-ink-900/40 group-hover:opacity-100">
                                <x-icon name="instagram" class="h-5 w-5" />
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
                            <a href="{{ route('collaboration.create') }}" class="ng-btn !bg-white !text-ink-900 hover:!bg-cream-100">Ajukan Kolaborasi</a>
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
