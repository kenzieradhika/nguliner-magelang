@extends('layouts.app')

@section('meta_title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <section class="border-b border-ink-100 bg-cream-100 py-16">
        <div class="ng-container">
            <nav class="mb-4 flex items-center gap-2 text-xs text-ink-400">
                <a href="{{ route('home') }}" class="flex items-center gap-1 transition hover:text-ink-900"><x-icon name="home" class="h-3 w-3" /> Beranda</a>
                <x-icon name="chevron-right" class="h-3 w-3" />
                <span class="font-semibold text-ink-600">{{ $page->title }}</span>
            </nav>
            <p class="ng-eyebrow">Halaman</p>
            <h1 class="ng-page-title">{{ $page->title }}</h1>
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container max-w-3xl">
            @foreach($page->sections ?? [] as $section)
                @switch($section['type'] ?? 'text')
                    @case('heading')
                        <h2 class="ng-section-title mt-10 first:mt-0">{{ $section['content'] ?? '' }}</h2>
                        @break
                    @case('text')
                        <p class="mt-4 text-[15px] leading-relaxed text-ink-600">{{ $section['content'] ?? '' }}</p>
                        @break
                    @case('image')
                        @php($src = $section['image'] ?? $section['content'] ?? '')
                        @if(!empty($src))
                            <img src="{{ str_starts_with($src, 'http') ? $src : asset('storage/'.$src) }}" alt="" class="mt-8 w-full rounded-2xl" loading="lazy">
                        @endif
                        @break
                    @case('list')
                        <ul class="mt-6 space-y-3">
                            @foreach($section['items'] ?? [] as $item)
                                <li class="flex items-start gap-3 text-[15px] text-neutral-600">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sambal-600"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                        @break
                    @case('quote')
                        <blockquote class="mt-8 border-l-4 border-sambal-600 pl-6 text-lg font-medium italic text-ink-700">
                            {{ $section['content'] ?? '' }}
                        </blockquote>
                        @break
                    @case('cta')
                        <div class="mt-10 rounded-2xl bg-ink-900 p-8 text-center text-white">
                            <p class="text-lg font-bold">{{ $section['content'] ?? '' }}</p>
                            @if(!empty($section['url']))
                                <a href="{{ $section['url'] }}" class="ng-btn-primary mt-5">
                                    {{ $section['button'] ?? 'Selengkapnya' }}
                                </a>
                            @endif
                        </div>
                        @break
                    @case('embed')
                        <div class="mt-8 overflow-hidden rounded-2xl border border-ink-100">
                            {!! $section['content'] ?? '' !!}
                        </div>
                        @break
                @endswitch
            @endforeach
        </div>
    </section>
@endsection
