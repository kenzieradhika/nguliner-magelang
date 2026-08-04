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
    <section class="border-b border-neutral-200 bg-neutral-50 py-16">
        <div class="ng-container">
            <nav class="mb-4 text-xs text-neutral-400">
                <a href="{{ route('home') }}" class="hover:text-neutral-900">Beranda</a>
                <span class="mx-2">/</span>
                <span class="text-neutral-600">{{ $category->name }}</span>
            </nav>
            <h1 class="text-3xl font-bold tracking-tight md:text-4xl">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-neutral-500">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container">
            @if($places->isEmpty())
                <p class="text-sm text-neutral-500">Belum ada kuliner di kategori ini.</p>
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
