@extends('layouts.app')

@section('meta_title', 'Cari Kuliner Magelang — Rekomendasi Makan Magelang')

@section('content')
    <section class="border-b border-neutral-200 bg-neutral-50 py-12">
        <div class="ng-container">
            <h1 class="text-3xl font-bold tracking-tight">Cari Kuliner</h1>
            <form action="{{ route('search') }}" method="GET" class="mt-6 flex flex-col gap-3 md:flex-row">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari bakso, es dawet, martabak..." class="ng-input flex-1 !py-3.5">
                <button type="submit" class="ng-btn">Cari</button>
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
                    <input type="checkbox" name="buka" value="1" @checked(request('buka')) onchange="this.form.submit()" class="h-4 w-4 accent-neutral-900">
                    Buka sekarang
                </label>
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="legendaris" value="1" @checked(request('legendaris')) onchange="this.form.submit()" class="h-4 w-4 accent-neutral-900">
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
                <div class="py-20 text-center">
                    <p class="text-4xl">🍽️</p>
                    <p class="mt-4 text-sm text-neutral-500">Tidak ada hasil yang cocok. Coba kata kunci lain.</p>
                </div>
            @else
                <p class="mb-8 text-xs text-neutral-400">{{ $places->total() }} hasil ditemukan</p>
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
