@extends('admin.layouts.app')

@section('title', 'Moderasi Review')
@section('section', 'Komunikasi')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Umpan balik pengunjung</p>
            <h2 class="adm-page-title">Moderasi Review</h2>
            <p class="adm-page-subtitle">Review hanya tampil di publik setelah disetujui</p>
        </div>
        <div class="flex gap-1.5 rounded-full border border-ink-900/[0.08] bg-white p-1 text-xs font-semibold">
            <a href="{{ route('admin.reviews.index') }}" class="rounded-full px-4 py-1.5 transition {{ !request('status') ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Semua</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'pending' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Menunggu</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'approved' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Disetujui</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="adm-card p-6 transition-shadow duration-300 hover:shadow-[0_8px_32px_-16px_rgba(28,25,23,0.2)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sambal-600 text-[11px] font-bold text-white">{{ strtoupper(substr($review->name, 0, 1)) }}</span>
                            <h2 class="text-sm font-bold text-ink-900">{{ $review->name }}</h2>
                            <x-rating-stars :rating="$review->rating" />
                            <span class="text-xs text-ink-400">untuk {{ $review->place?->name }}</span>
                            <span class="adm-badge {{ $review->is_approved ? 'adm-badge-soft-green' : 'adm-badge-soft-amber' }}">{{ $review->is_approved ? 'Disetujui' : 'Menunggu' }}</span>
                        </div>
                        @if($review->comment)
                            <p class="mt-3 max-w-3xl rounded-xl border-l-2 border-sambal-600/40 bg-cream-100/60 px-5 py-4 text-sm leading-relaxed text-ink-600">{{ $review->comment }}</p>
                        @endif
                        <p class="mt-2.5 text-xs text-ink-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex shrink-0 gap-1.5">
                        @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="adm-btn px-4 py-2 text-xs"><x-icon name="check" class="h-3.5 w-3.5" /> Setujui</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Hapus review ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="adm-empty">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream-100 text-sambal-600"><x-icon name="star" class="h-6 w-6" /></span>
                <p class="text-sm font-semibold text-ink-500">Belum ada review.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $reviews->links() }}</div>
@endsection
