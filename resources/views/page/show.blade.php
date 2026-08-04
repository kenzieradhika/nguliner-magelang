@extends('layouts.app')

@section('meta_title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <section class="border-b border-neutral-200 bg-neutral-50 py-16">
        <div class="ng-container">
            <nav class="mb-4 text-xs text-neutral-400">
                <a href="{{ route('home') }}" class="hover:text-neutral-900">Beranda</a>
                <span class="mx-2">/</span>
                <span class="text-neutral-600">{{ $page->title }}</span>
            </nav>
            <h1 class="text-3xl font-bold tracking-tight md:text-4xl">{{ $page->title }}</h1>
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
                        <p class="mt-4 text-[15px] leading-relaxed text-neutral-600">{{ $section['content'] ?? '' }}</p>
                        @break
                    @case('image')
                        @if(!empty($section['content']))
                            <img src="{{ $section['content'] }}" alt="" class="mt-8 w-full rounded-2xl" loading="lazy">
                        @endif
                        @break
                    @case('list')
                        <ul class="mt-6 space-y-3">
                            @foreach($section['items'] ?? [] as $item)
                                <li class="flex items-start gap-3 text-[15px] text-neutral-600">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-neutral-900"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                        @break
                    @case('quote')
                        <blockquote class="mt-8 border-l-4 border-neutral-900 pl-6 text-lg font-medium italic text-neutral-700">
                            {{ $section['content'] ?? '' }}
                        </blockquote>
                        @break
                    @case('cta')
                        <div class="mt-10 rounded-2xl bg-neutral-950 p-8 text-center text-white">
                            <p class="text-lg font-bold">{{ $section['content'] ?? '' }}</p>
                            @if(!empty($section['url']))
                                <a href="{{ $section['url'] }}" class="ng-btn mt-5 !bg-white !text-neutral-900 hover:!bg-neutral-200">
                                    {{ $section['button'] ?? 'Selengkapnya' }}
                                </a>
                            @endif
                        </div>
                        @break
                    @case('embed')
                        <div class="mt-8 overflow-hidden rounded-2xl border border-neutral-200">
                            {!! $section['content'] ?? '' !!}
                        </div>
                        @break
                @endswitch
            @endforeach
        </div>
    </section>
@endsection
