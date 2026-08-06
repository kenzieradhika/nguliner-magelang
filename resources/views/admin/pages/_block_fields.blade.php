<div class="space-y-3">
    @switch($section['type'] ?? 'text')
        @case('heading')
            <div>
                <label class="adm-label">Judul</label>
                <input name="sections[{{ $index }}][content]" value="{{ $section['content'] ?? '' }}" class="adm-input">
            </div>
            @break
        @case('text')
            <div>
                <label class="adm-label">Paragraf</label>
                <textarea name="sections[{{ $index }}][content]" rows="3" class="adm-input">{{ $section['content'] ?? '' }}</textarea>
            </div>
            @break
        @case('image')
            <div>
                <label class="adm-label">URL Gambar</label>
                <input name="sections[{{ $index }}][content]" value="{{ $section['content'] ?? '' }}" class="adm-input" placeholder="https://... atau /img/...">
            </div>
            @break
        @case('list')
            <div>
                <label class="adm-label">Item Daftar (satu per baris)</label>
                <textarea name="sections[{{ $index }}][items]" rows="4" class="adm-input" placeholder="Item 1&#10;Item 2">{{ implode("\n", $section['items'] ?? []) }}</textarea>
            </div>
            @break
        @case('quote')
            <div>
                <label class="adm-label">Kutipan</label>
                <textarea name="sections[{{ $index }}][content]" rows="3" class="adm-input">{{ $section['content'] ?? '' }}</textarea>
            </div>
            @break
        @case('cta')
            <div>
                <label class="adm-label">Kalimat Ajakan</label>
                <input name="sections[{{ $index }}][content]" value="{{ $section['content'] ?? '' }}" class="adm-input">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="adm-label">URL</label>
                    <input name="sections[{{ $index }}][url]" value="{{ $section['url'] ?? '' }}" class="adm-input" placeholder="/kolaborasi">
                </div>
                <div>
                    <label class="adm-label">Teks Tombol</label>
                    <input name="sections[{{ $index }}][button]" value="{{ $section['button'] ?? '' }}" class="adm-input" placeholder="Hubungi Kami">
                </div>
            </div>
            @break
        @case('embed')
            <div>
                <label class="adm-label">HTML Embed</label>
                <textarea name="sections[{{ $index }}][content]" rows="4" class="adm-input" placeholder="<iframe src='...'></iframe>">{{ $section['content'] ?? '' }}</textarea>
            </div>
            @break
    @endswitch
</div>