@extends('layouts.app')

@section('meta_title', "Kuliner {$category->name} Magelang — Rekomendasi Makan Terbaik")

@section('head')
    <script type="application/ld+json">
    {
        {{ '@context' }}: "https://schema.org",
        "@type": "ItemList",
        "name": {{ json_encode("Kuliner {$category->name} Magelang") }},
        "itemListElement": [
            @foreach($places as $i => $place)
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
    <section class="border-b border-ink-100 bg-cream-100 py-16">
        <div class="ng-container">
            <nav class="mb-4 flex items-center gap-2 text-xs text-ink-400">
                <a href="{{ route('home') }}" class="flex items-center gap-1 transition hover:text-ink-900"><x-icon name="home" class="h-3 w-3" /> Beranda</a>
                <x-icon name="chevron-right" class="h-3 w-3" />
                <span class="font-semibold text-ink-600">{{ $category->name }}</span>
            </nav>
            <p class="ng-eyebrow">Kategori Kuliner</p>
            <h1 class="ng-page-title">{{ $category->name }}</h1>
            @if($category->description)
                <p class="ng-page-subtitle max-w-2xl">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container">
            @if($places->isEmpty())
                <div class="flex flex-col items-center py-20 text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-cream-100 text-ink-300">
                        <x-icon name="utensils" class="h-7 w-7" />
                    </span>
                    <p class="mt-5 text-sm text-ink-500">Belum ada kuliner di kategori ini.</p>
                </div>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($places as $place)
                        <x-place-card :place="$place" />
                    @endforeach
                </div>
                <div class="mt-14">
                    {{ $places->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
