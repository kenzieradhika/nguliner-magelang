<x-dynamic-component :component="$getFieldComponent()" :field="$field">
    <div
        x-data="{
            title: $wire.entangle('data.meta_title').defer,
            desc: $wire.entangle('data.meta_description').defer,
        }"
        class="rounded-xl border border-gray-200 bg-white p-4"
    >
        <p class="mb-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">Pratinjau hasil pencarian Google</p>
        <p class="truncate text-sm text-emerald-800">nguliner.test › halaman</p>
        <p class="truncate text-lg text-blue-700 hover:underline" x-text="title || 'Judul halaman akan tampil di sini'"></p>
        <p class="mt-1 line-clamp-2 text-sm text-gray-600" x-text="desc || 'Deskripsi meta akan tampil di sini. Tulis ringkasan 140–160 karakter.'"></p>
    </div>
</x-dynamic-component>