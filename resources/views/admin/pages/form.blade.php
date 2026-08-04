@extends('admin.layouts.app')

@section('title', $page->exists ? 'Edit Halaman' : 'Buat Halaman')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.pages.index') }}" class="text-sm text-ink-400 hover:text-ink-900">&larr; Kembali</a>
        <h1 class="mt-2 ng-page-title">{{ $page->exists ? 'Edit Halaman' : 'Buat Halaman' }}</h1>
        <p class="mt-1 text-sm text-ink-500">Susun halaman dari blok-blok section di bawah.</p>
    </div>

    <form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="POST" class="max-w-4xl space-y-6">
        @csrf
        @if($page->exists)
            @method('PUT')
        @endif

        <div class="rounded-2xl border border-ink-100 bg-white p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="ng-label" for="title">Judul *</label>
                    <input id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="150" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="slug">Slug (kosongkan = otomatis)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $page->slug) }}" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="meta_title">SEO Title</label>
                    <input id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" maxlength="150" class="ng-input">
                </div>
                <div>
                    <label class="ng-label" for="meta_description">SEO Description</label>
                    <input id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" maxlength="255" class="ng-input">
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published)) class="h-4 w-4 accent-neutral-900">
                    <span class="text-sm">Tayang di publik</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-ink-100 bg-white p-6">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-widest text-ink-400">Blok Section</h2>
                <button type="button" id="add-block" class="ng-btn !px-4 !py-2 !text-xs">+ Tambah Blok</button>
            </div>
            <div id="sections" class="space-y-4">
                @foreach(old('sections', $page->sections ?? []) as $index => $section)
                    <div class="block-item rounded-xl border border-ink-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <select name="sections[{{ $index }}][type]" class="block-type ng-input !w-44" onchange="updateBlock(this)">
                                @foreach(['heading' => 'Heading', 'text' => 'Text', 'image' => 'Gambar', 'list' => 'Daftar', 'quote' => 'Kutipan', 'cta' => 'Tombol CTA', 'embed' => 'Embed (HTML)'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($section['type'] ?? 'text') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="this.closest('.block-item').remove()" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus Blok</button>
                        </div>
                        <div class="mt-4 block-fields">
                            @include('admin.pages._block_fields', ['section' => $section, 'index' => $index])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="ng-btn-primary"><x-icon name="check-circle" class="h-4 w-4" /> Simpan Halaman</button>
            <a href="{{ route('admin.pages.index') }}" class="ng-btn-outline">Batal</a>
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
            div.className = 'block-item rounded-xl border border-ink-100 p-4';
            div.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <select name="sections[${blockIndex}][type]" class="block-type ng-input !w-44" onchange="updateBlock(this)">
                        ${Object.entries({heading:'Heading',text:'Text',image:'Gambar',list:'Daftar',quote:'Kutipan',cta:'Tombol CTA',embed:'Embed (HTML)'}).map(([v,l]) => `<option value="${v}">${l}</option>`).join('')}
                    </select>
                    <button type="button" onclick="this.closest('.block-item').remove()" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus Blok</button>
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
                heading: `<label class="ng-label">Judul</label><input name="sections[${i}][content]" class="ng-input">`,
                text: `<label class="ng-label">Paragraf</label><textarea name="sections[${i}][content]" rows="3" class="ng-input"></textarea>`,
                image: `<label class="ng-label">URL Gambar</label><input name="sections[${i}][content]" class="ng-input" placeholder="https://... atau /img/...">`,
                list: `<label class="ng-label">Item Daftar (satu per baris)</label><textarea name="sections[${i}][items]" rows="4" class="ng-input" placeholder="Item 1&#10;Item 2"></textarea>`,
                quote: `<label class="ng-label">Kutipan</label><textarea name="sections[${i}][content]" rows="3" class="ng-input"></textarea>`,
                cta: `
                    <label class="ng-label">Kalimat Ajakan</label><input name="sections[${i}][content]" class="ng-input">
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div><label class="ng-label">URL</label><input name="sections[${i}][url]" class="ng-input" placeholder="/kolaborasi"></div>
                        <div><label class="ng-label">Teks Tombol</label><input name="sections[${i}][button]" class="ng-input" placeholder="Hubungi Kami"></div>
                    </div>`,
                embed: `<label class="ng-label">HTML Embed</label><textarea name="sections[${i}][content]" rows="4" class="ng-input" placeholder="<iframe src='...'></iframe>"></textarea>`,
            };

            fieldsContainer.innerHTML = inputs[type] || '';
        }
    </script>
@endpush
