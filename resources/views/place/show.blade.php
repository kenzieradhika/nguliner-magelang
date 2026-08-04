@extends('layouts.app')

@section('meta_title', "{$place->name} — {$place->category?->name} Magelang")
@section('meta_description', Str::limit($place->tagline ?? $place->description, 160))
@section('og_image', url($place->image))

@section('head')
    <script type="application/ld+json">
    {
        {{ '@context' }}: "https://schema.org",
        "@type": "Restaurant",
        "name": {{ json_encode($place->name) }},
        "description": {{ json_encode($place->tagline ?: $place->description) }},
        "servesCuisine": {{ json_encode($place->category?->name) }},
        "image": {{ json_encode(url($place->image)) }},
        "url": {{ json_encode(url()->current()) }},
        "address": { "@type": "PostalAddress", "streetAddress": {{ json_encode($place->address) }} },
        "priceRange": {{ json_encode($place->price_range) }}
        @if($place->latitude && $place->longitude)
        , "geo": { "@type": "GeoCoordinates", "latitude": {{ $place->latitude }}, "longitude": {{ $place->longitude }} }
        @endif
        @if($place->open_time)
        , "openingHoursSpecification": [{
            "@type": "OpeningHoursSpecification",
            "opens": {{ json_encode($place->open_time) }},
            "closes": {{ json_encode($place->close_time) }}
        }]
        @endif
    }
    </script>
@endsection

@section('content')
    <section class="border-b border-neutral-200 bg-neutral-50 py-16">
        <div class="ng-container">
            <nav class="mb-4 text-xs text-neutral-400">
                <a href="{{ route('home') }}" class="hover:text-neutral-900">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('category.show', $place->category->slug) }}" class="hover:text-neutral-900">{{ $place->category->name }}</a>
                <span class="mx-2">/</span>
                <span class="text-neutral-600">{{ $place->name }}</span>
            </nav>
            <div class="flex flex-wrap items-center gap-2">
                @if($place->is_legendary)
                    <span class="ng-tag">Legendaris</span>
                @endif
                <span class="ng-tag-light">Sejak {{ $place->since_year ?? 'dulu' }}</span>
            </div>
            <h1 class="mt-4 text-3xl font-bold tracking-tight md:text-5xl">{{ $place->name }}</h1>
            @if($place->tagline)
                <p class="mt-3 max-w-2xl text-base text-neutral-500">{{ $place->tagline }}</p>
            @endif
            <div class="mt-5 flex flex-wrap items-center gap-5 text-sm text-neutral-600">
                @if($place->averageRating() > 0)
                    <span class="flex items-center gap-2">
                        <x-rating-stars :rating="$place->averageRating()" />
                        <strong>{{ number_format($place->averageRating(), 1) }}</strong>
                        <span class="text-neutral-400">({{ $place->reviewCount() }} review)</span>
                    </span>
                @endif
                <span class="text-neutral-400">{{ number_format($place->views) }}x dilihat</span>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container grid gap-12 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <img src="{{ $place->image }}" alt="{{ $place->name }}" class="img-fade aspect-[4/3] w-full rounded-2xl object-cover" loading="eager">
                @if($place->description)
                    <h2 class="ng-section-title mt-10">Tentang Tempat Ini</h2>
                    <p class="text-sm leading-relaxed text-neutral-600 md:text-base">{{ $place->description }}</p>
                @endif
                @if($place->tips)
                    <h2 class="ng-section-title mt-10">Tips</h2>
                    <div class="flex gap-3 rounded-2xl border border-neutral-200 p-5">
                        <span class="text-lg">💡</span>
                        <p class="text-sm leading-relaxed text-neutral-600">{{ $place->tips }}</p>
                    </div>
                @endif

                <div class="mt-10">
                    <h2 class="ng-section-title">Review Pengunjung</h2>
                    <div class="space-y-4">
                        @forelse($place->approvedReviews as $review)
                            <div class="rounded-2xl border border-neutral-200 p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-neutral-900 text-xs font-bold text-white">{{ strtoupper(substr($review->name, 0, 1)) }}</span>
                                        <div>
                                            <p class="text-sm font-semibold">{{ $review->name }}</p>
                                            <x-rating-stars :rating="$review->rating" />
                                        </div>
                                    </div>
                                    <span class="text-xs text-neutral-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-relaxed text-neutral-600">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-500">Belum ada review. Jadilah yang pertama!</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-10 rounded-2xl bg-neutral-50 p-6 md:p-8">
                    <h3 class="text-base font-bold">Tulis Review</h3>
                    <form action="{{ route('place.review', $place->slug) }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        @if(session('success'))
                            <p class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</p>
                        @endif
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="ng-label" for="r-name">Nama *</label>
                                <input id="r-name" name="name" required maxlength="100" class="ng-input" placeholder="Nama kamu">
                            </div>
                            <div>
                                <label class="ng-label" for="r-rating">Rating *</label>
                                <select id="r-rating" name="rating" required class="ng-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}">{{ $i }} / 5 bintang</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="ng-label" for="r-comment">Review *</label>
                            <textarea id="r-comment" name="comment" rows="4" required maxlength="1000" class="ng-input" placeholder="Ceritakan pengalamanmu..."></textarea>
                        </div>
                        <button type="submit" class="ng-btn">Kirim Review</button>
                    </form>
                </div>
            </div>

            <aside class="lg:col-span-2">
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-2xl border border-neutral-200 p-6">
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Info Praktis</h3>
                        <ul class="mt-5 space-y-5 text-sm">
                            <li class="flex gap-3">
                                <span class="text-base">📍</span>
                                <div>
                                    <p class="font-medium">Alamat</p>
                                    <p class="mt-0.5 text-neutral-500">{{ $place->address ?? 'Belum tersedia' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-base">⏰</span>
                                <div>
                                    <p class="font-medium">Jam Buka</p>
                                    <p class="mt-0.5 text-neutral-500">
                                        @if($place->open_days && $place->open_time)
                                            {{ $place->open_time }} – {{ $place->close_time ?? 'habis' }}
                                        @else
                                            Belum tersedia
                                        @endif
                                    </p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-base">💳</span>
                                <div>
                                    <p class="font-medium">Kisaran Harga</p>
                                    <p class="mt-0.5 text-neutral-500">{{ $place->price_range ?? 'Belum tersedia' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="text-base">🕐</span>
                                <div>
                                    <p class="font-medium">Status</p>
                                    @if($place->isOpenNow())
                                        <p class="mt-0.5 inline-flex items-center gap-1.5 text-green-600">
                                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span> Buka sekarang
                                        </p>
                                    @else
                                        <p class="mt-0.5 text-red-500">{{ $place->openStatusText() }}</p>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>

                    @if($place->latitude && $place->longitude)
                        <div class="overflow-hidden rounded-2xl border border-neutral-200">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $place->latitude }},{{ $place->longitude }}&z=16&output=embed"
                                class="h-56 w-full"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Peta lokasi {{ $place->name }}"></iframe>
                        </div>
                    @endif

                    <div class="rounded-2xl bg-neutral-950 p-6 text-white">
                        <h3 class="text-sm font-bold">Bagikan &amp; Hubungi</h3>
                        <div class="mt-4 grid grid-cols-2 gap-2.5">
                            <a href="https://wa.me/?text={{ urlencode($place->name . ' — ' . url()->current()) }}" target="_blank" rel="noopener" class="rounded-full bg-white/10 px-4 py-2.5 text-center text-xs font-medium transition hover:bg-white/20">WhatsApp</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="rounded-full bg-white/10 px-4 py-2.5 text-center text-xs font-medium transition hover:bg-white/20">Facebook</a>
                            <a href="https://x.com/intent/post?text={{ urlencode($place->name) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="rounded-full bg-white/10 px-4 py-2.5 text-center text-xs font-medium transition hover:bg-white/20">X / Twitter</a>
                            <button type="button" onclick="navigator.clipboard.writeText(location.href); this.textContent='Tersalin!'" class="rounded-full bg-white/10 px-4 py-2.5 text-center text-xs font-medium transition hover:bg-white/20">Salin Link</button>
                        </div>
                        @if($place->whatsapp)
                            <a href="https://wa.me/{{ $place->whatsapp }}" target="_blank" rel="noopener" class="ng-btn mt-4 w-full !bg-green-600 hover:!bg-green-700">Chat via WhatsApp</a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @if($related->isNotEmpty())
        <section class="border-t border-neutral-200 bg-neutral-50 py-16">
            <div class="ng-container">
                <h2 class="ng-section-title">Kuliner Lainnya</h2>
                <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($related as $place)
                        <x-place-card :place="$place" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
