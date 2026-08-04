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
    <section class="border-b border-ink-100 bg-cream-100 py-16">
        <div class="ng-container">
            <nav class="mb-4 flex items-center gap-2 text-xs text-ink-400">
                <a href="{{ route('home') }}" class="flex items-center gap-1 transition hover:text-ink-900"><x-icon name="home" class="h-3 w-3" /> Beranda</a>
                <x-icon name="chevron-right" class="h-3 w-3" />
                <a href="{{ route('category.show', $place->category->slug) }}" class="transition hover:text-ink-900">{{ $place->category->name }}</a>
                <x-icon name="chevron-right" class="h-3 w-3" />
                <span class="font-semibold text-ink-600">{{ $place->name }}</span>
            </nav>
            <div class="flex flex-wrap items-center gap-2">
                @if($place->is_legendary)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-ink-900 px-3 py-1 text-xs font-bold text-white">
                        <x-icon name="trophy" class="h-3 w-3 text-amber-400" /> Legendaris
                    </span>
                @endif
                <span class="ng-tag-light">Sejak {{ $place->since_year ?? 'dulu' }}</span>
            </div>
            <h1 class="mt-4 font-display text-3xl font-bold tracking-tight md:text-5xl">{{ $place->name }}</h1>
            @if($place->tagline)
                <p class="mt-3 max-w-2xl text-base text-ink-500">{{ $place->tagline }}</p>
            @endif
            <div class="mt-5 flex flex-wrap items-center gap-5 text-sm text-ink-600">
                @if($place->averageRating() > 0)
                    <span class="flex items-center gap-2">
                        <x-rating-stars :rating="$place->averageRating()" />
                        <strong>{{ number_format($place->averageRating(), 1) }}</strong>
                        <span class="text-ink-400">({{ $place->reviewCount() }} review)</span>
                    </span>
                @endif
                <span class="flex items-center gap-1.5 text-ink-400"><x-icon name="eye" class="h-3.5 w-3.5" /> {{ number_format($place->views) }}x dilihat</span>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container grid gap-12 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <img src="{{ $place->image }}" alt="{{ $place->name }}" class="img-fade aspect-[4/3] w-full rounded-2xl object-cover shadow-sm" loading="eager">
                @if($place->description)
                    <p class="ng-eyebrow mt-12">Tentang Tempat Ini</p>
                    <h2 class="ng-section-title">Cerita {{ $place->name }}</h2>
                    <p class="mt-4 text-sm leading-relaxed text-ink-600 md:text-base">{{ $place->description }}</p>
                @endif
                @if($place->tips)
                    <h2 class="ng-section-title mt-12">Tips</h2>
                    <div class="mt-4 flex gap-3 rounded-2xl border border-ink-100 bg-white p-5 shadow-sm">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-50 text-sambal-600">
                            <x-icon name="lightbulb" class="h-4 w-4" />
                        </span>
                        <p class="text-sm leading-relaxed text-ink-600">{{ $place->tips }}</p>
                    </div>
                @endif

                <div class="mt-12">
                    <p class="ng-eyebrow">Review Pengunjung</p>
                    <h2 class="ng-section-title">Kata Mereka</h2>
                    <div class="mt-6 space-y-4">
                        @forelse($place->approvedReviews as $review)
                            <div class="rounded-2xl border border-ink-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">{{ strtoupper(substr($review->name, 0, 1)) }}</span>
                                        <div>
                                            <p class="text-sm font-bold">{{ $review->name }}</p>
                                            <x-rating-stars :rating="$review->rating" />
                                        </div>
                                    </div>
                                    <span class="text-xs text-ink-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-relaxed text-ink-600">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <div class="flex flex-col items-center rounded-2xl border border-dashed border-ink-100 py-10 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-cream-100 text-ink-300">
                                    <x-icon name="message-square" class="h-5 w-5" />
                                </span>
                                <p class="mt-3 text-sm text-ink-500">Belum ada review. Jadilah yang pertama!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-12 rounded-2xl bg-cream-100 p-6 md:p-8">
                    <h3 class="font-display text-lg font-bold">Tulis Review</h3>
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
                        <button type="submit" class="ng-btn-primary">
                            <x-icon name="send" class="h-4 w-4" /> Kirim Review
                        </button>
                    </form>
                </div>
            </div>

            <aside class="lg:col-span-2">
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-2xl border border-ink-100 bg-white p-6 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-ink-400">Info Praktis</h3>
                        <ul class="mt-5 space-y-5 text-sm">
                            <li class="flex gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-50 text-sambal-600">
                                    <x-icon name="map-pin" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="font-bold">Alamat</p>
                                    <p class="mt-0.5 text-ink-500">{{ $place->address ?? 'Belum tersedia' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-50 text-sambal-600">
                                    <x-icon name="clock" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="font-bold">Jam Buka</p>
                                    <p class="mt-0.5 text-ink-500">
                                        @if($place->open_days && $place->open_time)
                                            {{ $place->open_time }} – {{ $place->close_time ?? 'habis' }}
                                        @else
                                            Belum tersedia
                                        @endif
                                    </p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-50 text-sambal-600">
                                    <x-icon name="banknote" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="font-bold">Kisaran Harga</p>
                                    <p class="mt-0.5 text-ink-500">{{ $place->price_range ?? 'Belum tersedia' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-50 text-sambal-600">
                                    <x-icon name="bell" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="font-bold">Status</p>
                                    @if($place->isOpenNow())
                                        <p class="mt-0.5 inline-flex items-center gap-1.5 font-semibold text-green-600">
                                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span> Buka sekarang
                                        </p>
                                    @else
                                        <p class="mt-0.5 font-semibold text-red-500">{{ $place->openStatusText() }}</p>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>

                    @if($place->latitude && $place->longitude)
                        <div class="overflow-hidden rounded-2xl border border-ink-100 shadow-sm">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $place->latitude }},{{ $place->longitude }}&z=16&output=embed"
                                class="h-56 w-full"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Peta lokasi {{ $place->name }}"></iframe>
                        </div>
                    @endif

                    <div class="rounded-2xl bg-ink-900 p-6 text-white shadow-sm">
                        <h3 class="text-sm font-bold">Bagikan &amp; Hubungi</h3>
                        <div class="mt-4 grid grid-cols-2 gap-2.5">
                            <a href="https://wa.me/?text={{ urlencode($place->name . ' — ' . url()->current()) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-1.5 rounded-full bg-white/10 px-4 py-2.5 text-xs font-semibold transition hover:bg-white/20"><x-icon name="whatsapp" class="h-3.5 w-3.5" /> WhatsApp</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-1.5 rounded-full bg-white/10 px-4 py-2.5 text-xs font-semibold transition hover:bg-white/20"><x-icon name="facebook" class="h-3.5 w-3.5" /> Facebook</a>
                            <a href="https://x.com/intent/post?text={{ urlencode($place->name) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-1.5 rounded-full bg-white/10 px-4 py-2.5 text-xs font-semibold transition hover:bg-white/20"><x-icon name="external-link" class="h-3.5 w-3.5" /> X / Twitter</a>
                            <button type="button" onclick="navigator.clipboard.writeText(location.href); this.textContent='Tersalin!'" class="rounded-full bg-white/10 px-4 py-2.5 text-center text-xs font-semibold transition hover:bg-white/20">Salin Link</button>
                        </div>
                        @if($place->whatsapp)
                            <a href="https://wa.me/{{ $place->whatsapp }}" target="_blank" rel="noopener" class="ng-btn mt-4 w-full !bg-green-600 hover:!bg-green-700">
                                <x-icon name="whatsapp" class="h-4 w-4" /> Chat via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @if($related->isNotEmpty())
        <section class="border-t border-ink-100 bg-cream-100 py-16">
            <div class="ng-container">
                <p class="ng-eyebrow">Jangan Lewatkan</p>
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
