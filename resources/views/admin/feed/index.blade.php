@extends('admin.layouts.app')

@section('title', 'Feed Instagram')
@section('section', 'Komunikasi')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Konten sosial</p>
            <h2 class="adm-page-title">Feed Instagram</h2>
            <p class="adm-page-subtitle">{{ $posts->total() }} post — tampil di beranda</p>
        </div>
        <div class="adm-card p-3">
            <form action="{{ route('admin.feed.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".json" required class="text-sm text-ink-500">
                <button type="submit" class="adm-btn px-4 py-2 text-xs">Import JSON</button>
            </form>
            <p class="mt-2 text-[11px] text-ink-400">File hasil <code>python/scraper/instagram_feed.py</code> (data/instagram.json)</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
        @forelse($posts as $post)
            <div class="adm-card group overflow-hidden p-3 transition-all duration-300 hover:-translate-y-1">
                <div class="overflow-hidden rounded-xl">
                    <img src="{{ $post->image_url }}" alt="" class="aspect-square w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                </div>
                <p class="mt-3 line-clamp-2 text-xs leading-relaxed text-ink-500">{{ $post->caption }}</p>
                <p class="mt-1 text-[10px] text-ink-400">{{ $post->posted_at?->diffForHumans() ?? 'Tanpa tanggal' }}</p>
                <div class="mt-3 flex gap-2">
                    <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="flex flex-1 items-center justify-center gap-1 rounded-lg border border-ink-900/10 py-1.5 text-xs font-semibold transition hover:bg-ink-900 hover:text-white"><x-icon name="external-link" class="h-3 w-3" /> Buka</a>
                    <form action="{{ route('admin.feed.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus post ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-600 transition hover:bg-red-600 hover:text-white">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="adm-empty col-span-full">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream-100 text-sambal-600"><x-icon name="instagram" class="h-6 w-6" /></span>
                <p class="text-sm font-semibold text-ink-500">Belum ada post.</p>
                <p class="text-xs text-ink-400">Jalankan scraper atau import file JSON.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
