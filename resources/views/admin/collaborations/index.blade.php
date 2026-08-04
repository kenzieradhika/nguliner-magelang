@extends('admin.layouts.app')

@section('title', 'Inbox Kolaborasi')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Inbox Kolaborasi</h1>
            <p class="mt-1 text-sm text-ink-500">{{ $collaborations->total() }} pengajuan</p>
        </div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.collaborations.index') }}" class="ng-tag-light {{ !request('status') ? '!bg-ink-900 !text-white' : '' }}">Semua</a>
            <a href="{{ route('admin.collaborations.index', ['status' => 'new']) }}" class="ng-tag-light {{ request('status') === 'new' ? '!bg-ink-900 !text-white' : '' }}">Baru</a>
            <a href="{{ route('admin.collaborations.index', ['status' => 'contacted']) }}" class="ng-tag-light {{ request('status') === 'contacted' ? '!bg-ink-900 !text-white' : '' }}">Dihubungi</a>
            <a href="{{ route('admin.collaborations.index', ['status' => 'done']) }}" class="ng-tag-light {{ request('status') === 'done' ? '!bg-ink-900 !text-white' : '' }}">Selesai</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($collaborations as $collab)
            <div class="rounded-2xl border border-ink-100 bg-white p-6 {{ $collab->status === 'new' ? 'border-l-4 !border-l-red-500' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold">{{ $collab->name }}</h2>
                            <span class="rounded-full bg-cream-100 px-2.5 py-0.5 text-[10px] font-semibold uppercase text-ink-500">{{ $collab->type }}</span>
                            @if($collab->status === 'new')
                                <span class="ng-badge ng-badge-red">Baru</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-ink-500">
                            {{ $collab->business_name ? $collab->business_name . ' · ' : '' }}{{ $collab->email }}{{ $collab->whatsapp ? ' · ' . $collab->whatsapp : '' }}
                        </p>
                        @if($collab->message)
                            <p class="mt-3 max-w-3xl rounded-xl bg-cream-50 p-4 text-sm leading-relaxed text-ink-600">{{ $collab->message }}</p>
                        @endif
                        <p class="mt-2 text-xs text-ink-400">{{ $collab->created_at->diffForHumans() }}</p>
                    </div>
                    <form action="{{ route('admin.collaborations.status', $collab) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <select name="status" class="ng-input !w-36 !py-2">
                            @foreach(\App\Models\Collaboration::STATUSES as $status)
                                <option value="{{ $status }}" @selected($collab->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="ng-btn !px-4 !py-2 !text-xs">Simpan</button>
                    </form>
                    <form action="{{ route('admin.collaborations.destroy', $collab) }}" method="POST" onsubmit="return confirm('Hapus pengajuan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-ink-100 bg-white p-16 text-center text-sm text-ink-400">Belum ada pengajuan kolaborasi.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $collaborations->links() }}</div>
@endsection
