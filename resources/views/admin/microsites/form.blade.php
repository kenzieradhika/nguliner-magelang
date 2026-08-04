@extends('admin.layouts.app')

@section('title', $microsite->exists ? 'Edit Microsite' : 'Buat Microsite')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.microsites.index') }}" class="text-sm text-neutral-400 hover:text-neutral-900">&larr; Kembali</a>
        <h1 class="mt-2 text-2xl font-bold tracking-tight">{{ $microsite->exists ? "Edit Microsite: {$place->name}" : "Buat Microsite: {$place->name}" }}</h1>
    </div>

    <form action="{{ $microsite->exists ? route('admin.microsites.update', $microsite) : route('admin.microsites.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @if($microsite->exists)
            @method('PUT')
        @endif
        <input type="hidden" name="place_id" value="{{ $microsite->place_id ?: $place->id }}">

        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="mb-5 text-sm font-bold uppercase tracking-widest text-neutral-400">Hero</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="ng-label" for="hero_title">Judul Hero</label>
                    <input id="hero_title" name="hero_title" value="{{ old('hero_title', $microsite->hero_title) }}" maxlength="200" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="accent_color">Warna Tema</label>
                    <div class="flex items-center gap-3">
                        <input id="accent_color" name="accent_color" type="color" value="{{ old('accent_color', $microsite->accent_color ?? '#111111') }}" class="h-11 w-14 rounded-lg border border-neutral-200">
                        <input value="{{ old('accent_color', $microsite->accent_color ?? '#111111') }}" class="ng-input" readonly>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="ng-label" for="hero_image">Gambar Hero</label>
                    <input id="hero_image" name="hero_image" type="file" accept="image/*" class="ng-input !p-2">
                    @if($microsite->hero_image)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $microsite->hero_image) }}" alt="" class="h-24 w-36 rounded-lg object-cover">
                        </div>
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="ng-label" for="about">Tentang</label>
                    <textarea id="about" name="about" rows="5" class="ng-input">{{ old('about', $microsite->about) }}</textarea>
                </div>
                <div>
                    <label class="ng-label" for="cta_text">Teks Tombol CTA</label>
                    <input id="cta_text" name="cta_text" value="{{ old('cta_text', $microsite->cta_text) }}" maxlength="100" class="ng-input" placeholder="Pesan via WhatsApp">
                </div>
                <div class="flex items-end gap-3 pb-1">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $microsite->is_active)) class="h-4 w-4 accent-neutral-900">
                    <span class="text-sm">Microsite aktif (bisa diakses publik)</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="mb-5 text-sm font-bold uppercase tracking-widest text-neutral-400">Menu</h2>
            <div id="menu-items" class="space-y-3">
                @foreach(old('menu', $microsite->menu ?? []) as $index => $item)
                    <div class="menu-item grid grid-cols-12 gap-3">
                        <input name="menu[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" placeholder="Nama menu" class="ng-input col-span-5">
                        <input name="menu[{{ $index }}][desc]" value="{{ $item['desc'] ?? '' }}" placeholder="Deskripsi" class="ng-input col-span-5">
                        <input name="menu[{{ $index }}][price]" value="{{ $item['price'] ?? '' }}" placeholder="Harga" class="ng-input col-span-1">
                        <button type="button" onclick="this.closest('.menu-item').remove()" class="rounded-lg border border-red-200 text-xs text-red-600 hover:bg-red-50">✕</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-menu" class="ng-btn-outline mt-4 !px-4 !py-2 !text-xs">+ Tambah Menu</button>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6">
            <h2 class="mb-5 text-sm font-bold uppercase tracking-widest text-neutral-400">Galeri &amp; Media Sosial</h2>
            <div class="grid gap-5">
                <div>
                    <label class="ng-label">Galeri (URL gambar, satu per baris)</label>
                    <textarea name="gallery" rows="3" class="ng-input" placeholder="https://...&#10;https://...">{{ implode("\n", old('gallery', $microsite->gallery ?? [])) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="ng-label" for="s_ig">Instagram URL</label>
                        <input id="s_ig" name="socials[instagram]" value="{{ old('socials.instagram', $microsite->socials['instagram'] ?? '') }}" class="ng-input">
                    </div>
                    <div>
                        <label class="ng-label" for="s_tt">TikTok URL</label>
                        <input id="s_tt" name="socials[tiktok]" value="{{ old('socials.tiktok', $microsite->socials['tiktok'] ?? '') }}" class="ng-input">
                    </div>
                    <div>
                        <label class="ng-label" for="s_wa">WhatsApp (62...)</label>
                        <input id="s_wa" name="socials[whatsapp]" value="{{ old('socials.whatsapp', $microsite->socials['whatsapp'] ?? '') }}" maxlength="30" class="ng-input">
                    </div>
                    <div>
                        <label class="ng-label" for="s_web">Website URL</label>
                        <input id="s_web" name="socials[website]" value="{{ old('socials.website', $microsite->socials['website'] ?? '') }}" class="ng-input">
                    </div>
                </div>
                <div>
                    <label class="ng-label" for="map_embed">Embed Peta (HTML iframe)</label>
                    <textarea id="map_embed" name="map_embed" rows="4" maxlength="2000" class="ng-input" placeholder="<iframe src='https://maps.google.com/maps?...'></iframe>">{{ old('map_embed', $microsite->map_embed) }}</textarea>
                    <p class="mt-1 text-[11px] text-neutral-400">Kosongkan untuk memakai koordinat dari data kuliner.</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="ng-btn">Simpan Microsite</button>
            <a href="{{ route('admin.microsites.index') }}" class="ng-btn-outline">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        let menuIndex = document.querySelectorAll('.menu-item').length;
        document.getElementById('add-menu')?.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'menu-item grid grid-cols-12 gap-3';
            div.innerHTML = `
                <input name="menu[${menuIndex}][name]" placeholder="Nama menu" class="ng-input col-span-5">
                <input name="menu[${menuIndex}][desc]" placeholder="Deskripsi" class="ng-input col-span-5">
                <input name="menu[${menuIndex}][price]" placeholder="Harga" class="ng-input col-span-1">
                <button type="button" onclick="this.closest('.menu-item').remove()" class="rounded-lg border border-red-200 text-xs text-red-600 hover:bg-red-50">✕</button>`;
            document.getElementById('menu-items').appendChild(div);
            menuIndex++;
        });
    </script>
@endpush
