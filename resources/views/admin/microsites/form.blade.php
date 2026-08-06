@extends('admin.layouts.app')

@section('title', $microsite->exists ? 'Edit Microsite' : 'Buat Microsite')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Landing page UMKM</p>
            <h2 class="adm-page-title">{{ $microsite->exists ? "Edit Microsite: {$place->name}" : "Buat Microsite: {$place->name}" }}</h2>
        </div>
        <a href="{{ route('admin.microsites.index') }}" class="adm-btn-ghost"><x-icon name="arrow-right" class="h-3.5 w-3.5 rotate-180" /> Kembali</a>
    </div>

    <form action="{{ $microsite->exists ? route('admin.microsites.update', $microsite) : route('admin.microsites.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @if($microsite->exists)
            @method('PUT')
        @endif
        <input type="hidden" name="place_id" value="{{ $microsite->place_id ?: $place->id }}">

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="image" class="h-3.5 w-3.5 text-sambal-600" /> Hero</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="adm-label" for="hero_title">Judul Hero</label>
                    <input id="hero_title" name="hero_title" value="{{ old('hero_title', $microsite->hero_title) }}" maxlength="200" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="accent_color">Warna Tema</label>
                    <div class="flex items-center gap-3">
                        <input id="accent_color" name="accent_color" type="color" value="{{ old('accent_color', $microsite->accent_color ?? '#111111') }}" class="h-11 w-14 rounded-lg border border-ink-900/10">
                        <input value="{{ old('accent_color', $microsite->accent_color ?? '#111111') }}" class="adm-input" readonly>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="adm-label" for="hero_image">Gambar Hero</label>
                    <input id="hero_image" name="hero_image" type="file" accept="image/*" class="adm-input !p-2">
                    @if($microsite->hero_image)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $microsite->hero_image) }}" alt="" class="h-24 w-36 rounded-xl border border-ink-900/[0.06] object-cover">
                        </div>
                    @endif
                </div>
                <div class="sm:col-span-2">
                    <label class="adm-label" for="about">Tentang</label>
                    <textarea id="about" name="about" rows="5" class="adm-input">{{ old('about', $microsite->about) }}</textarea>
                </div>
                <div>
                    <label class="adm-label" for="cta_text">Teks Tombol CTA</label>
                    <input id="cta_text" name="cta_text" value="{{ old('cta_text', $microsite->cta_text) }}" maxlength="100" class="adm-input" placeholder="Pesan via WhatsApp">
                </div>
                <div class="flex items-end gap-3 pb-1">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $microsite->is_active)) class="h-4 w-4 accent-neutral-900">
                    <span class="text-sm text-ink-700">Microsite aktif (bisa diakses publik)</span>
                </div>
            </div>
        </div>

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="list" class="h-3.5 w-3.5 text-sambal-600" /> Menu</h2>
            <div id="menu-items" class="space-y-3">
                @foreach(old('menu', $microsite->menu ?? []) as $index => $item)
                    <div class="menu-item grid grid-cols-12 gap-3">
                        <input name="menu[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" placeholder="Nama menu" class="adm-input col-span-5">
                        <input name="menu[{{ $index }}][desc]" value="{{ $item['desc'] ?? '' }}" placeholder="Deskripsi" class="adm-input col-span-5">
                        <input name="menu[{{ $index }}][price]" value="{{ $item['price'] ?? '' }}" placeholder="Harga" class="adm-input col-span-1">
                        <button type="button" onclick="this.closest('.menu-item').remove()" class="rounded-lg border border-red-200 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white"><x-icon name="x" class="h-3 w-3" /></button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-menu" class="adm-btn-secondary mt-4 !px-4 !py-2 !text-xs"><x-icon name="plus" class="h-3 w-3" /> Tambah Menu</button>
        </div>

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="camera" class="h-3.5 w-3.5 text-sambal-600" /> Galeri &amp; Media Sosial</h2>
            <div class="grid gap-5">
                <div>
                    <label class="adm-label">Galeri (URL gambar, satu per baris)</label>
                    <textarea name="gallery" rows="3" class="adm-input" placeholder="https://...&#10;https://...">{{ implode("\n", old('gallery', $microsite->gallery ?? [])) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="adm-label" for="s_ig">Instagram URL</label>
                        <input id="s_ig" name="socials[instagram]" value="{{ old('socials.instagram', $microsite->socials['instagram'] ?? '') }}" class="adm-input">
                    </div>
                    <div>
                        <label class="adm-label" for="s_tt">TikTok URL</label>
                        <input id="s_tt" name="socials[tiktok]" value="{{ old('socials.tiktok', $microsite->socials['tiktok'] ?? '') }}" class="adm-input">
                    </div>
                    <div>
                        <label class="adm-label" for="s_wa">WhatsApp (62...)</label>
                        <input id="s_wa" name="socials[whatsapp]" value="{{ old('socials.whatsapp', $microsite->socials['whatsapp'] ?? '') }}" maxlength="30" class="adm-input">
                    </div>
                    <div>
                        <label class="adm-label" for="s_web">Website URL</label>
                        <input id="s_web" name="socials[website]" value="{{ old('socials.website', $microsite->socials['website'] ?? '') }}" class="adm-input">
                    </div>
                </div>
                <div>
                    <label class="adm-label" for="map_embed">Embed Peta (HTML iframe)</label>
                    <textarea id="map_embed" name="map_embed" rows="4" maxlength="2000" class="adm-input" placeholder="<iframe src='https://maps.google.com/maps?...'></iframe>">{{ old('map_embed', $microsite->map_embed) }}</textarea>
                    <p class="mt-1 text-[11px] text-ink-400">Kosongkan untuk memakai koordinat dari data kuliner.</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="adm-btn px-7"><x-icon name="check-circle" class="h-4 w-4" /> Simpan Microsite</button>
            <a href="{{ route('admin.microsites.index') }}" class="adm-btn-secondary px-7">Batal</a>
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
                <input name="menu[${menuIndex}][name]" placeholder="Nama menu" class="adm-input col-span-5">
                <input name="menu[${menuIndex}][desc]" placeholder="Deskripsi" class="adm-input col-span-5">
                <input name="menu[${menuIndex}][price]" placeholder="Harga" class="adm-input col-span-1">
                <button type="button" onclick="this.closest('.menu-item').remove()" class="rounded-lg border border-red-200 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white"><x-icon name="x" class="h-3 w-3" /></button>`;
            document.getElementById('menu-items').appendChild(div);
            menuIndex++;
        });
    </script>
@endpush