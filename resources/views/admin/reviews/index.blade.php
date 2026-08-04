@extends('admin.layouts.app')

@section('title', 'Moderasi Review')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Moderasi Review</h1>
            <p class="mt-1 text-sm text-neutral-500">Review hanya tampil di publik setelah disetujui</p>
        </div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.reviews.index') }}" class="ng-tag-light {{ !request('status') ? '!bg-neutral-900 !text-white' : '' }}">Semua</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="ng-tag-light {{ request('status') === 'pending' ? '!bg-neutral-900 !text-white' : '' }}">Menunggu</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="ng-tag-light {{ request('status') === 'approved' ? '!bg-neutral-900 !text-white' : '' }}">Disetujui</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 {{ !$review->is_approved ? 'border-l-4 !border-l-amber-500' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-bold">{{ $review->name }}</h2>
                            <x-rating-stars :rating="$review->rating" />
                            <span class="text-xs text-neutral-400">untuk {{ $review->place?->name }}</span>
                        </div>
                        @if($review->comment)
                            <p class="mt-3 max-w-3xl rounded-xl bg-neutral-50 p-4 text-sm leading-relaxed text-neutral-600">{{ $review->comment }}</p>
                        @endif
                        <p class="mt-2 text-xs text-neutral-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="ng-btn !px-4 !py-2 !text-xs">Setujui</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Hapus review ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-neutral-200 bg-white p-16 text-center text-sm text-neutral-400">Belum ada review.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $reviews->links() }}</div>
@endsection
