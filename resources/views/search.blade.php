@extends('layouts.app')

@section('meta_title', 'Cari Kuliner Magelang — Rekomendasi Makan Magelang')

@section('content')
    <section class="border-b border-ink-100 bg-cream-100 py-12">
        <div class="ng-container">
            <p class="ng-eyebrow">Pencarian</p>
            <h1 class="ng-page-title">Cari Kuliner</h1>
            <form action="{{ route('search') }}" method="GET" class="mt-6 flex flex-col gap-3 md:flex-row">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari bakso, es dawet, martabak..." class="ng-input flex-1 !py-3.5">
                <button type="submit" class="ng-btn-primary">
                    <x-icon name="search" class="h-4 w-4" /> Cari
                </button>
            </form>
            <form action="{{ route('search') }}" method="GET" class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                <select name="kategori" class="ng-input !w-auto !py-2" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="buka" value="1" @checked(request('buka')) onchange="this.form.submit()" class="h-4 w-4 accent-sambal-600">
                    Buka sekarang
                </label>
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="legendaris" value="1" @checked(request('legendaris')) onchange="this.form.submit()" class="h-4 w-4 accent-sambal-600">
                    Legendaris
                </label>
                <select name="sort" class="ng-input !w-auto !py-2" onchange="this.form.submit()">
                    <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Terbaru</option>
                    <option value="rating" @selected(request('sort') === 'rating')>Rating Tertinggi</option>
                    <option value="view" @selected(request('sort') === 'view')>Paling Dilihat</option>
                    <option value="harga" @selected(request('sort') === 'harga')>Harga Termurah</option>
                </select>
            </form>
        </div>
    </section>

    <section class="py-14">
        <div class="ng-container">
            @if($places->isEmpty())
                <div class="flex flex-col items-center py-20 text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-cream-100 text-ink-300">
                        <x-icon name="search" class="h-7 w-7" />
                    </span>
                    <p class="mt-5 text-base font-bold text-ink-900">Tidak ada hasil yang cocok</p>
                    <p class="mt-1.5 text-sm text-ink-500">Coba kata kunci lain atau hapus filter pencarian.</p>
                    <a href="{{ route('search') }}" class="ng-btn-outline ng-btn-sm mt-6">
                        <x-icon name="refresh" class="h-3.5 w-3.5" /> Reset Pencarian
                    </a>
                </div>
            @else
                <p class="mb-8 text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $places->total() }} hasil ditemukan</p>
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
