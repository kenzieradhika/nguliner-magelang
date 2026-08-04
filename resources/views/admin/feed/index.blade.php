@extends('admin.layouts.app')

@section('title', 'Feed Instagram')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Feed Instagram</h1>
            <p class="mt-1 text-sm text-ink-500">{{ $posts->total() }} post — tampil di beranda</p>
        </div>
        <div class="rounded-2xl border border-ink-100 bg-white p-3">
            <form action="{{ route('admin.feed.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".json" required class="text-sm">
                <button type="submit" class="ng-btn !px-4 !py-2 !text-xs">Import JSON</button>
            </form>
            <p class="mt-2 text-[11px] text-ink-400">File hasil <code>python/scraper/instagram_feed.py</code> (data/instagram.json)</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
        @forelse($posts as $post)
            <div class="rounded-2xl border border-ink-100 bg-white p-3">
                <img src="{{ $post->image_url }}" alt="" class="aspect-square w-full rounded-xl object-cover" loading="lazy">
                <p class="mt-2 line-clamp-2 text-xs text-ink-500">{{ $post->caption }}</p>
                <p class="mt-1 text-[10px] text-ink-400">{{ $post->posted_at?->diffForHumans() ?? 'Tanpa tanggal' }}</p>
                <div class="mt-2 flex gap-2">
                    <a href="{{ $post->permalink }}" target="_blank" rel="noopener" class="flex-1 rounded-lg border border-ink-100 py-1.5 text-center text-xs transition hover:bg-cream-100">Buka</a>
                    <form action="{{ route('admin.feed.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus post ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-ink-100 bg-white p-16 text-center text-sm text-ink-400">
                Belum ada post. Jalankan scraper atau import file JSON.
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
