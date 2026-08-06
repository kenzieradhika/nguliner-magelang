@extends('admin.layouts.app')

@section('title', $page->exists ? 'Edit Halaman' : 'Buat Halaman')
@section('section', 'Konten')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Konten statis</p>
            <h2 class="adm-page-title">{{ $page->exists ? 'Edit Halaman' : 'Buat Halaman' }}</h2>
            <p class="adm-page-subtitle">Susun halaman dari blok-blok section di bawah.</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="adm-btn-ghost"><x-icon name="arrow-right" class="h-3.5 w-3.5 rotate-180" /> Kembali</a>
    </div>

    <form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="POST" class="max-w-4xl space-y-6">
        @csrf
        @if($page->exists)
            @method('PUT')
        @endif

        <div class="adm-card p-6 lg:p-7">
            <h2 class="adm-card-title"><x-icon name="file-text" class="h-3.5 w-3.5 text-sambal-600" /> Informasi Halaman</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="adm-label" for="title">Judul *</label>
                    <input id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="150" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="slug">Slug (kosongkan = otomatis)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $page->slug) }}" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="meta_title">SEO Title</label>
                    <input id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" maxlength="150" class="adm-input">
                </div>
                <div>
                    <label class="adm-label" for="meta_description">SEO Description</label>
                    <input id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" maxlength="255" class="adm-input">
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published)) class="h-4 w-4 accent-neutral-900">
                    <span class="text-sm text-ink-700">Tayang di publik</span>
                </div>
            </div>
        </div>

        <div class="adm-card p-6 lg:p-7">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="adm-card-title mb-0"><x-icon name="layout-grid" class="h-3.5 w-3.5 text-sambal-600" /> Blok Section</h2>
                <button type="button" id="add-block" class="adm-btn-secondary !px-4 !py-2 !text-xs"><x-icon name="plus" class="h-3 w-3" /> Tambah Blok</button>
            </div>
            <div id="sections" class="space-y-4">
                @foreach(old('sections', $page->sections ?? []) as $index => $section)
                    <div class="block-item rounded-xl border border-ink-900/[0.06] bg-cream-100/40 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <select name="sections[{{ $index }}][type]" class="block-type adm-input !w-44" onchange="updateBlock(this)">
                                @foreach(['heading' => 'Heading', 'text' => 'Text', 'image' => 'Gambar', 'list' => 'Daftar', 'quote' => 'Kutipan', 'cta' => 'Tombol CTA', 'embed' => 'Embed (HTML)'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($section['type'] ?? 'text') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="this.closest('.block-item').remove()" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white">Hapus Blok</button>
                        </div>
                        <div class="mt-4 block-fields">
                            @include('admin.pages._block_fields', ['section' => $section, 'index' => $index])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="adm-btn px-7"><x-icon name="check-circle" class="h-4 w-4" /> Simpan Halaman</button>
            <a href="{{ route('admin.pages.index') }}" class="adm-btn-secondary px-7">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        let blockIndex = document.querySelectorAll('.block-item').length;

        const fields = {
            heading: { content: 'Judul section' },
            text: { content: 'Isi paragraf' },
            image: { content: 'URL gambar' },
            list: { items: [] },
            quote: { content: 'Isi kutipan' },
            cta: { content: 'Kalimat ajakan', url: '', button: 'Teks tombol' },
            embed: { content: '<iframe src="..."></iframe>' },
        };

        document.getElementById('add-block')?.addEventListener('click', () => {
            const container = document.getElementById('sections');
            const div = document.createElement('div');
            div.className = 'block-item rounded-xl border border-ink-900/[0.06] bg-cream-100/40 p-4';
            div.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <select name="sections[${blockIndex}][type]" class="block-type adm-input !w-44" onchange="updateBlock(this)">
                        ${Object.entries({heading:'Heading',text:'Text',image:'Gambar',list:'Daftar',quote:'Kutipan',cta:'Tombol CTA',embed:'Embed (HTML)'}).map(([v,l]) => `<option value="${v}">${l}</option>`).join('')}
                    </select>
                    <button type="button" onclick="this.closest('.block-item').remove()" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white">Hapus Blok</button>
                </div>
                <div class="mt-4 block-fields"></div>`;
            container.appendChild(div);
            updateBlock(div.querySelector('.block-type'));
            blockIndex++;
        });

        function updateBlock(select) {
            const item = select.closest('.block-item');
            const fieldsContainer = item.querySelector('.block-fields');
            const type = select.value;
            const i = [...item.parentElement.children].indexOf(item);

            const inputs = {
                heading: `<label class="adm-label">Judul</label><input name="sections[${i}][content]" class="adm-input">`,
                text: `<label class="adm-label">Paragraf</label><textarea name="sections[${i}][content]" rows="3" class="adm-input"></textarea>`,
                image: `<label class="adm-label">URL Gambar</label><input name="sections[${i}][content]" class="adm-input" placeholder="https://... atau /img/...">`,
                list: `<label class="adm-label">Item Daftar (satu per baris)</label><textarea name="sections[${i}][items]" rows="4" class="adm-input" placeholder="Item 1&#10;Item 2"></textarea>`,
                quote: `<label class="adm-label">Kutipan</label><textarea name="sections[${i}][content]" rows="3" class="adm-input"></textarea>`,
                cta: `
                    <label class="adm-label">Kalimat Ajakan</label><input name="sections[${i}][content]" class="adm-input">
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div><label class="adm-label">URL</label><input name="sections[${i}][url]" class="adm-input" placeholder="/kolaborasi"></div>
                        <div><label class="adm-label">Teks Tombol</label><input name="sections[${i}][button]" class="adm-input" placeholder="Hubungi Kami"></div>
                    </div>`,
                embed: `<label class="adm-label">HTML Embed</label><textarea name="sections[${i}][content]" rows="4" class="adm-input" placeholder="<iframe src='...'></iframe>"></textarea>`,
            };

            fieldsContainer.innerHTML = inputs[type] || '';
        }
    </script>
@endpush