@extends('layouts.app')

@section('meta_title', $microsite->hero_title ?? $place->name)
@section('meta_description', Str::limit($microsite->about ?? $place->tagline, 160))
@section('og_image', url($microsite->hero_image ?? $place->image))

@section('head')
    <script type="application/ld+json">
    {
        {{ '@context' }}: "https://schema.org",
        "@type": "Restaurant",
        "name": {{ json_encode($microsite->hero_title ?? $place->name) }},
        "description": {{ json_encode($microsite->about ?: $place->tagline) }},
        "image": {{ json_encode(url($microsite->hero_image ?? $place->image)) }},
        "url": {{ json_encode(url()->current()) }},
        "servesCuisine": {{ json_encode($place->category?->name) }},
        "address": { "@type": "PostalAddress", "streetAddress": {{ json_encode($place->address) }} },
        "priceRange": {{ json_encode($place->price_range) }}
        @if($place->latitude && $place->longitude)
        , "geo": { "@type": "GeoCoordinates", "latitude": {{ $place->latitude }}, "longitude": {{ $place->longitude }} }
        @endif
    }
    </script>
    <style>
        .ms-accent { color: {{ $microsite->accent_color }}; }
        .ms-bg { background-color: {{ $microsite->accent_color }}; }
        .ms-btn { background-color: {{ $microsite->accent_color }}; }
        .ms-btn:hover { filter: brightness(0.9); }
    </style>
@endsection

@section('content')
    <section class="relative overflow-hidden bg-ink-900 text-white">
        @if($microsite->hero_image)
            <img src="{{ $microsite->hero_image }}" alt="{{ $place->name }}" class="absolute inset-0 h-full w-full object-cover opacity-30">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-ink-900/50 via-transparent to-ink-900/80"></div>
        <div class="ng-container relative flex min-h-[60vh] flex-col items-center justify-center py-24 text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold text-white ms-bg">
                <x-icon name="globe" class="h-3 w-3" /> Microsite UMKM
            </span>
            <h1 class="mt-6 max-w-3xl font-display text-4xl font-bold tracking-tight md:text-6xl">{{ $microsite->hero_title ?? $place->name }}</h1>
            <p class="mt-5 max-w-xl text-sm leading-relaxed text-ink-100/80 md:text-base">
                {{ $place->tagline }}
            </p>
            @if($microsite->cta_text)
                @if(!empty($microsite->socials['whatsapp']))
                    <a href="https://wa.me/{{ $microsite->socials['whatsapp'] }}" target="_blank" rel="noopener" class="ms-btn mt-10 inline-flex items-center gap-2 rounded-full px-8 py-3.5 text-sm font-semibold text-white transition hover:brightness-90">
                        <x-icon name="whatsapp" class="h-4 w-4" /> {{ $microsite->cta_text }}
                    </a>
                @else
                    <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="ms-btn mt-10 inline-flex items-center gap-2 rounded-full px-8 py-3.5 text-sm font-semibold text-white transition hover:brightness-90">
                        <x-icon name="instagram" class="h-4 w-4" /> {{ $microsite->cta_text }}
                    </a>
                @endif
            @endif
        </div>
    </section>

    @if($microsite->about)
        <section class="py-16 md:py-20">
            <div class="ng-container max-w-3xl text-center">
                <p class="ng-eyebrow !justify-center">Tentang Kami</p>
                <h2 class="ng-section-title">Cerita Kami</h2>
                <p class="mt-4 text-[15px] leading-relaxed text-ink-600">{{ $microsite->about }}</p>
            </div>
        </section>
    @endif

    @if(!empty($microsite->menu))
        <section class="border-y border-ink-100 bg-cream-100 py-16 md:py-20">
            <div class="ng-container">
                <h2 class="ng-section-title text-center">Menu &amp; Harga</h2>
                <p class="ng-section-subtitle !mx-auto text-center">Pilihan menu terbaik kami</p>
                <div class="mx-auto mt-10 grid max-w-3xl gap-4">
                    @foreach($microsite->menu as $item)
                        <div class="flex items-start justify-between gap-6 rounded-2xl border border-ink-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div>
                                <p class="font-bold">{{ $item['name'] }}</p>
                                @if(!empty($item['desc']))
                                    <p class="mt-1 text-sm text-ink-500">{{ $item['desc'] }}</p>
                                @endif
                            </div>
                            <p class="ms-accent whitespace-nowrap text-sm font-bold">{{ $item['price'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(!empty($microsite->gallery))
        <section class="py-16 md:py-20">
            <div class="ng-container">
                <p class="ng-eyebrow">Galeri</p>
                <h2 class="ng-section-title">Suasana &amp; Menu</h2>
                <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3">
                    @foreach($microsite->gallery as $image)
                        <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="Galeri {{ $place->name }}" class="aspect-square w-full rounded-2xl object-cover shadow-sm transition duration-300 hover:scale-[1.02]" loading="lazy">
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="border-t border-ink-100 bg-cream-100 py-16 md:py-20">
        <div class="ng-container grid gap-10 md:grid-cols-2">
            <div>
                <p class="ng-eyebrow">Kunjungi Kami</p>
                <h2 class="ng-section-title">Lokasi &amp; Jam Buka</h2>
                <ul class="mt-6 space-y-4 text-sm">
                    <li class="flex gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-sambal-600 shadow-sm">
                            <x-icon name="map-pin" class="h-4 w-4" />
                        </span>
                        <span class="pt-2 text-ink-600">{{ $place->address }}</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-sambal-600 shadow-sm">
                            <x-icon name="clock" class="h-4 w-4" />
                        </span>
                        <span class="pt-2 text-ink-600">
                            @if($place->open_days && $place->open_time)
                                {{ $place->open_time }} – {{ $place->close_time ?? 'habis' }}
                            @else
                                Tidak tersedia
                            @endif
                        </span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-sambal-600 shadow-sm">
                            <x-icon name="banknote" class="h-4 w-4" />
                        </span>
                        <span class="pt-2 text-ink-600">{{ $place->price_range ?? 'Tidak tersedia' }}</span>
                    </li>
                </ul>
                <a href="{{ route('place.show', $place->slug) }}" class="ng-btn-outline mt-8">
                    <x-icon name="arrow-up-right" class="h-4 w-4" /> Lihat di NGuliner
                </a>
            </div>
            <div>
                @if($microsite->map_embed)
                    <div class="overflow-hidden rounded-2xl border border-ink-100 shadow-sm">
                        {!! $microsite->map_embed !!}
                    </div>
                @elseif($place->latitude && $place->longitude)
                    <div class="overflow-hidden rounded-2xl border border-ink-100 shadow-sm">
                        <iframe
                            src="https://maps.google.com/maps?q={{ $place->latitude }},{{ $place->longitude }}&z=16&output=embed"
                            class="h-72 w-full"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta lokasi {{ $place->name }}"></iframe>
                    </div>
                @endif
                @if(!empty($microsite->socials))
                    <div class="mt-6 flex flex-wrap gap-2.5">
                        @if(!empty($microsite->socials['instagram']))
                            <a href="{{ $microsite->socials['instagram'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-ink-900"><x-icon name="instagram" class="h-3 w-3" /> Instagram</a>
                        @endif
                        @if(!empty($microsite->socials['tiktok']))
                            <a href="{{ $microsite->socials['tiktok'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-ink-900"><x-icon name="external-link" class="h-3 w-3" /> TikTok</a>
                        @endif
                        @if(!empty($microsite->socials['whatsapp']))
                            <a href="https://wa.me/{{ $microsite->socials['whatsapp'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-ink-900"><x-icon name="whatsapp" class="h-3 w-3" /> WhatsApp</a>
                        @endif
                        @if(!empty($microsite->socials['website']))
                            <a href="{{ $microsite->socials['website'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-ink-900"><x-icon name="globe" class="h-3 w-3" /> Website</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
