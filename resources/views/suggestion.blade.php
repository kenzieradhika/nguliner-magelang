@extends('layouts.app')

@section('meta_title', 'Saran Tempat Baru — Bantu Kami Kenal Kuliner Magelang')

@section('content')
    <section class="border-b border-ink-100 bg-cream-100 py-16">
        <div class="ng-container">
            <p class="ng-eyebrow">Bantu Kami Menemukan</p>
            <h1 class="ng-page-title">Saran Tempat Baru</h1>
            <p class="ng-page-subtitle max-w-2xl">
                Tahu tempat makan enak yang belum ada di NGuliner? Bantu kami mengenalkannya ke lebih banyak orang.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container grid gap-12 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <h2 class="ng-section-title">Cara Kerja</h2>
                <ul class="mt-6 space-y-6 text-sm">
                    <li class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sambal-600 font-bold text-white shadow-sm shadow-sambal-600/30">1</span>
                        <div>
                            <p class="font-semibold">Kirim Saran</p>
                            <p class="mt-1 leading-relaxed text-neutral-500">Isi form di samping dengan info tempat yang kamu tahu.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sambal-600 font-bold text-white shadow-sm shadow-sambal-600/30">2</span>
                        <div>
                            <p class="font-semibold">Tim Review</p>
                            <p class="mt-1 leading-relaxed text-neutral-500">Tim NGuliner memverifikasi informasi dan kualitas tempat.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sambal-600 font-bold text-white shadow-sm shadow-sambal-600/30">3</span>
                        <div>
                            <p class="font-semibold">Tayang</p>
                            <p class="mt-1 leading-relaxed text-neutral-500">Tempat muncul di rekomendasi dan bisa dibuatkan microsite.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <form action="{{ route('suggestion.store') }}" method="POST" class="ng-card p-8 md:p-10">
                    @csrf
                    @if(session('success'))
                        <p class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</p>
                    @endif
                    <h2 class="text-xl font-bold">Form Saran Tempat</h2>
                    <div class="mt-8 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="ng-label" for="name">Nama Tempat *</label>
                            <input id="name" name="name" required maxlength="150" class="ng-input" placeholder="mis. Sate Kambing Bu Sri">
                            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ng-label" for="category">Kategori</label>
                            <input id="category" name="category" maxlength="100" class="ng-input" placeholder="Bakso, Es Dawet, Street Food...">
                        </div>
                        <div>
                            <label class="ng-label" for="contact">Kontak Kamu</label>
                            <input id="contact" name="contact" maxlength="150" class="ng-input" placeholder="IG/WA/email (opsional)">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="ng-label" for="address">Alamat</label>
                            <input id="address" name="address" maxlength="255" class="ng-input" placeholder="Jl. ...">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="ng-label" for="description">Cerita Singkat</label>
                            <textarea id="description" name="description" rows="5" maxlength="2000" class="ng-input" placeholder="Kenapa tempat ini layak direkomendasikan? Menu apa yang wajib dicoba?"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="ng-btn-primary mt-8 w-full !py-4"><x-icon name="send" class="h-4 w-4" /> Kirim Saran</button>
                </form>
            </div>
        </div>
    </section>
@endsection
