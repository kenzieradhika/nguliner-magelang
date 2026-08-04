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
    <section class="relative overflow-hidden bg-neutral-950 text-white">
        @if($microsite->hero_image)
            <img src="{{ $microsite->hero_image }}" alt="{{ $place->name }}" class="absolute inset-0 h-full w-full object-cover opacity-30">
        @endif
        <div class="ng-container relative flex min-h-[60vh] flex-col items-center justify-center py-24 text-center">
            <span class="ng-tag ms-bg !border-0">Microsite UMKM</span>
            <h1 class="mt-6 max-w-3xl text-4xl font-extrabold tracking-tighter md:text-6xl">{{ $microsite->hero_title ?? $place->name }}</h1>
            <p class="mt-5 max-w-xl text-sm leading-relaxed text-neutral-300 md:text-base">
                {{ $place->tagline }}
            </p>
            @if($microsite->cta_text)
                @if(!empty($microsite->socials['whatsapp']))
                    <a href="https://wa.me/{{ $microsite->socials['whatsapp'] }}" target="_blank" rel="noopener" class="ms-btn mt-10 rounded-full px-8 py-3.5 text-sm font-medium text-white transition">{{ $microsite->cta_text }}</a>
                @else
                    <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="ms-btn mt-10 rounded-full px-8 py-3.5 text-sm font-medium text-white transition">{{ $microsite->cta_text }}</a>
                @endif
            @endif
        </div>
    </section>

    @if($microsite->about)
        <section class="py-16">
            <div class="ng-container max-w-3xl text-center">
                <h2 class="ng-section-title">Tentang Kami</h2>
                <p class="text-[15px] leading-relaxed text-neutral-600">{{ $microsite->about }}</p>
            </div>
        </section>
    @endif

    @if(!empty($microsite->menu))
        <section class="border-y border-neutral-200 bg-neutral-50 py-16">
            <div class="ng-container">
                <h2 class="ng-section-title text-center">Menu &amp; Harga</h2>
                <p class="ng-section-subtitle text-center">Pilihan menu terbaik kami</p>
                <div class="mx-auto grid max-w-3xl gap-4">
                    @foreach($microsite->menu as $item)
                        <div class="flex items-start justify-between gap-6 rounded-2xl bg-white p-6">
                            <div>
                                <p class="font-semibold">{{ $item['name'] }}</p>
                                @if(!empty($item['desc']))
                                    <p class="mt-1 text-sm text-neutral-500">{{ $item['desc'] }}</p>
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
        <section class="py-16">
            <div class="ng-container">
                <h2 class="ng-section-title">Galeri</h2>
                <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3">
                    @foreach($microsite->gallery as $image)
                        <img src="{{ $image }}" alt="Galeri {{ $place->name }}" class="aspect-square w-full rounded-2xl object-cover" loading="lazy">
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="border-t border-neutral-200 bg-neutral-50 py-16">
        <div class="ng-container grid gap-10 md:grid-cols-2">
            <div>
                <h2 class="ng-section-title">Lokasi &amp; Jam Buka</h2>
                <ul class="mt-6 space-y-4 text-sm">
                    <li class="flex gap-3">
                        <span class="text-base">📍</span>
                        <span class="text-neutral-600">{{ $place->address }}</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-base">⏰</span>
                        <span class="text-neutral-600">
                            @if($place->open_days && $place->open_time)
                                {{ $place->open_time }} – {{ $place->close_time ?? 'habis' }}
                            @else
                                Tidak tersedia
                            @endif
                        </span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-base">💳</span>
                        <span class="text-neutral-600">{{ $place->price_range ?? 'Tidak tersedia' }}</span>
                    </li>
                </ul>
                <a href="{{ route('place.show', $place->slug) }}" class="ng-btn-outline mt-8">Lihat di NGuliner</a>
            </div>
            <div>
                @if($microsite->map_embed)
                    <div class="overflow-hidden rounded-2xl border border-neutral-200">
                        {!! $microsite->map_embed !!}
                    </div>
                @elseif($place->latitude && $place->longitude)
                    <div class="overflow-hidden rounded-2xl border border-neutral-200">
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
                            <a href="{{ $microsite->socials['instagram'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-neutral-900">Instagram</a>
                        @endif
                        @if(!empty($microsite->socials['tiktok']))
                            <a href="{{ $microsite->socials['tiktok'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-neutral-900">TikTok</a>
                        @endif
                        @if(!empty($microsite->socials['whatsapp']))
                            <a href="https://wa.me/{{ $microsite->socials['whatsapp'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-neutral-900">WhatsApp</a>
                        @endif
                        @if(!empty($microsite->socials['website']))
                            <a href="{{ $microsite->socials['website'] }}" target="_blank" rel="noopener" class="ng-tag-light hover:border-neutral-900">Website</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
