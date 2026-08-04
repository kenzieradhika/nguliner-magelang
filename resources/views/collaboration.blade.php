@extends('layouts.app')

@section('meta_title', 'Kolaborasi — Iklan, Endorse & Review Bersama NGuliner Magelang')

@section('content')
    <section class="border-b border-ink-100 bg-cream-100 py-16">
        <div class="ng-container">
            <p class="ng-eyebrow">Iklan, Endorse &amp; Partnership</p>
            <h1 class="ng-page-title">Kolaborasi</h1>
            <p class="ng-page-subtitle max-w-2xl">
                Terbuka untuk iklan &amp; endorse, review resto/UMKM, dan partnership konten. Isi form di bawah dan tim kami akan menghubungi kamu.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="ng-container grid gap-12 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <h2 class="ng-section-title">Kenapa Kolaborasi?</h2>
                <ul class="mt-6 space-y-6 text-sm">
                    <li class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sambal-600 font-bold text-white shadow-sm shadow-sambal-600/30">1</span>
                        <div>
                            <p class="font-semibold">Jangkauan Lokal</p>
                            <p class="mt-1 leading-relaxed text-neutral-500">Terhubung dengan pecinta kuliner Magelang dan sekitarnya.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sambal-600 font-bold text-white shadow-sm shadow-sambal-600/30">2</span>
                        <div>
                            <p class="font-semibold">Konten Otentik</p>
                            <p class="mt-1 leading-relaxed text-neutral-500">Review jujur yang dipercaya audiens, fokus pada rasa dan pengalaman.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sambal-600 font-bold text-white shadow-sm shadow-sambal-600/30">3</span>
                        <div>
                            <p class="font-semibold">Support UMKM</p>
                            <p class="mt-1 leading-relaxed text-neutral-500">Kami bangga membantu resto dan pelaku usaha lokal lebih dikenal.</p>
                        </div>
                    </li>
                </ul>
                <div class="mt-10 rounded-2xl border border-ink-100 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold">Atau hubungi langsung:</p>
                    <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 text-sm text-neutral-600 underline-offset-4 hover:underline">
                        <x-icon name="instagram" class="h-4 w-4" /> Instagram @ngulinermagelang
                    </a>
                </div>
            </div>

            <div class="lg:col-span-3">
                <form action="{{ route('collaboration.store') }}" method="POST" class="ng-card p-8 md:p-10">
                    @csrf
                    @if(session('success'))
                        <p class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</p>
                    @endif
                    <h2 class="text-xl font-bold">Form Kolaborasi</h2>
                    <p class="mt-2 text-sm text-neutral-500">Semua data hanya dipakai untuk keperluan tindak lanjut kolaborasi.</p>
                    <div class="mt-8 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="ng-label" for="name">Nama *</label>
                            <input id="name" name="name" required maxlength="100" class="ng-input" placeholder="Nama lengkap">
                            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ng-label" for="email">Email *</label>
                            <input id="email" name="email" type="email" required maxlength="150" class="ng-input" placeholder="email@contoh.com">
                            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="ng-label" for="whatsapp">WhatsApp</label>
                            <input id="whatsapp" name="whatsapp" maxlength="30" class="ng-input" placeholder="628xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="ng-label" for="business_name">Nama Bisnis</label>
                            <input id="business_name" name="business_name" maxlength="150" class="ng-input" placeholder="Nama resto/UMKM">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="ng-label" for="type">Jenis Kolaborasi *</label>
                            <select id="type" name="type" required class="ng-input">
                                <option value="iklan">Iklan</option>
                                <option value="endorse">Endorse</option>
                                <option value="review">Review resto / UMKM</option>
                                <option value="partnership">Partnership konten</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="ng-label" for="message">Pesan</label>
                            <textarea id="message" name="message" rows="5" maxlength="2000" class="ng-input" placeholder="Ceritakan kebutuhan kolaborasimu..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="ng-btn-primary mt-8 w-full !py-4"><x-icon name="send" class="h-4 w-4" /> Kirim Pengajuan</button>
                </form>
            </div>
        </div>
    </section>
@endsection
