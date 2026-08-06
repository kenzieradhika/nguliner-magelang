@extends('admin.layouts.app')

@section('title', $place->exists ? 'Edit Kuliner' : 'Tambah Kuliner')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Direktori kuliner</p>
            <h2 class="adm-page-title">{{ $place->exists ? 'Edit Kuliner' : 'Tambah Kuliner' }}</h2>
        </div>
        <a href="{{ route('admin.places.index') }}" class="adm-btn-ghost"><x-icon name="arrow-right" class="h-3.5 w-3.5 rotate-180" /> Kembali</a>
    </div>

    <form action="{{ $place->exists ? route('admin.places.update', $place) : route('admin.places.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @if($place->exists)
            @method('PUT')
        @endif

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="file-text" class="h-3.5 w-3.5 text-sambal-600" /> Informasi Dasar</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="adm-label" for="name">Nama *</label>
                    <input id="name" name="name" value="{{ old('name', $place->name) }}" required class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="category_id">Kategori *</label>
                    <select id="category_id" name="category_id" required class="adm-input">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="adm-label" for="slug">Slug (kosongkan = otomatis)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $place->slug) }}" class="adm-input" placeholder="otomatis-dari-nama">
                </div>
                <div class="sm:col-span-2">
                    <label class="adm-label" for="tagline">Tagline</label>
                    <input id="tagline" name="tagline" value="{{ old('tagline', $place->tagline) }}" maxlength="255" class="adm-input" placeholder="Satu kalimat menarik">
                </div>
                <div class="sm:col-span-2">
                    <label class="adm-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="5" class="adm-input">{{ old('description', $place->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="map-pin" class="h-3.5 w-3.5 text-sambal-600" /> Lokasi &amp; Info Praktis</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="adm-label" for="address">Alamat</label>
                    <input id="address" name="address" value="{{ old('address', $place->address) }}" maxlength="255" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="latitude">Latitude</label>
                    <input id="latitude" name="latitude" value="{{ old('latitude', $place->latitude) }}" step="any" class="adm-input" placeholder="-7.4750">
                </div>
                <div>
                    <label class="adm-label" for="longitude">Longitude</label>
                    <input id="longitude" name="longitude" value="{{ old('longitude', $place->longitude) }}" step="any" class="adm-input" placeholder="110.2150">
                </div>
                <div>
                    <label class="adm-label" for="whatsapp">WhatsApp (62...)</label>
                    <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $place->whatsapp) }}" maxlength="30" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="price_range">Kisaran Harga</label>
                    <input id="price_range" name="price_range" value="{{ old('price_range', $place->price_range) }}" maxlength="100" class="adm-input" placeholder="Mulai Rp5.000">
                </div>
                <div>
                    <label class="adm-label" for="open_days">Hari Buka</label>
                    <input id="open_days" name="open_days" value="{{ old('open_days', $place->open_days) }}" maxlength="255" class="adm-input" placeholder="Mon,Tue,Wed,Thu,Fri,Sat,Sun">
                    <p class="mt-1 text-[11px] text-ink-400">Format: Mon,Tue,Wed,Thu,Fri,Sat,Sun</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="adm-label" for="open_time">Jam Buka</label>
                        <input id="open_time" name="open_time" value="{{ old('open_time', $place->open_time) }}" maxlength="10" class="adm-input" placeholder="10:00">
                    </div>
                    <div>
                        <label class="adm-label" for="close_time">Jam Tutup</label>
                        <input id="close_time" name="close_time" value="{{ old('close_time', $place->close_time) }}" maxlength="10" class="adm-input" placeholder="17:30 / kosong = habis">
                    </div>
                </div>
                <div>
                    <label class="adm-label" for="since_year">Sejak Tahun</label>
                    <input id="since_year" name="since_year" type="number" value="{{ old('since_year', $place->since_year) }}" class="adm-input" placeholder="2001">
                </div>
                <div class="sm:col-span-2">
                    <label class="adm-label" for="tips">Tips</label>
                    <textarea id="tips" name="tips" rows="2" maxlength="500" class="adm-input">{{ old('tips', $place->tips) }}</textarea>
                </div>
            </div>
        </div>

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="image" class="h-3.5 w-3.5 text-sambal-600" /> Media &amp; Status</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="adm-label" for="image">Foto</label>
                    <input id="image" name="image" type="file" accept="image/*" class="adm-input !p-2">
                    @if($place->image && !str_starts_with($place->image, '/img/'))
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $place->image) }}" alt="" class="h-24 w-32 rounded-xl border border-ink-900/[0.06] object-cover">
                        </div>
                    @endif
                </div>
                <div class="space-y-3 pt-1">
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink-900/[0.06] px-4 py-3 text-sm transition hover:bg-cream-100/60">
                        <input type="checkbox" name="is_legendary" value="1" @checked(old('is_legendary', $place->is_legendary)) class="h-4 w-4 accent-neutral-900"> Legendaris
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink-900/[0.06] px-4 py-3 text-sm transition hover:bg-cream-100/60">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $place->is_featured)) class="h-4 w-4 accent-neutral-900"> Featured (kandidat rekomendasi harian)
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ink-900/[0.06] px-4 py-3 text-sm transition hover:bg-cream-100/60">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $place->is_published)) class="h-4 w-4 accent-neutral-900"> Tayang di publik
                    </label>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="adm-btn px-7"><x-icon name="check-circle" class="h-4 w-4" /> Simpan</button>
            <a href="{{ route('admin.places.index') }}" class="adm-btn-secondary px-7">Batal</a>
        </div>
    </form>
@endsection
