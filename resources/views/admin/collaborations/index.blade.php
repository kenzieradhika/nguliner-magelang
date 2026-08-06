@extends('admin.layouts.app')

@section('title', 'Inbox Kolaborasi')
@section('section', 'Komunikasi')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Pengajuan mitra</p>
            <h2 class="adm-page-title">Inbox Kolaborasi</h2>
            <p class="adm-page-subtitle">{{ $collaborations->total() }} pengajuan kolaborasi</p>
        </div>
        <div class="flex flex-wrap gap-1.5 rounded-full border border-ink-900/[0.08] bg-white p-1 text-xs font-semibold">
            <a href="{{ route('admin.collaborations.index') }}" class="rounded-full px-4 py-1.5 transition {{ !request('status') ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Semua</a>
            <a href="{{ route('admin.collaborations.index', ['status' => 'new']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'new' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Baru</a>
            <a href="{{ route('admin.collaborations.index', ['status' => 'contacted']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'contacted' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Dihubungi</a>
            <a href="{{ route('admin.collaborations.index', ['status' => 'done']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'done' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Selesai</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($collaborations as $collab)
            <div class="adm-card p-6 transition-shadow duration-300 hover:shadow-[0_8px_32px_-16px_rgba(28,25,23,0.2)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sambal-600 text-[11px] font-bold text-white">{{ strtoupper(substr($collab->name, 0, 1)) }}</span>
                            <h2 class="text-base font-bold text-ink-900">{{ $collab->name }}</h2>
                            <span class="rounded-full bg-cream-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ink-500">{{ $collab->type }}</span>
                            @if($collab->status === 'new')
                                <span class="adm-badge adm-badge-soft-amber">Baru</span>
                            @else
                                <span class="adm-badge adm-badge-soft-neutral">{{ $collab->status }}</span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-sm text-ink-500">
                            {{ $collab->business_name ? $collab->business_name . ' · ' : '' }}{{ $collab->email }}{{ $collab->whatsapp ? ' · ' . $collab->whatsapp : '' }}
                        </p>
                        @if($collab->message)
                            <p class="mt-3 max-w-3xl rounded-xl border-l-2 border-sambal-600/40 bg-cream-100/60 px-5 py-4 text-sm leading-relaxed text-ink-600">{{ $collab->message }}</p>
                        @endif
                        <p class="mt-2.5 text-xs text-ink-400">{{ $collab->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <form action="{{ route('admin.collaborations.status', $collab) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status" class="adm-input !w-36 !py-2">
                                @foreach(\App\Models\Collaboration::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($collab->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="adm-btn px-4 py-2 text-xs">Simpan</button>
                        </form>
                        <form action="{{ route('admin.collaborations.destroy', $collab) }}" method="POST" onsubmit="return confirm('Hapus pengajuan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="adm-empty">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream-100 text-sambal-600"><x-icon name="briefcase" class="h-6 w-6" /></span>
                <p class="text-sm font-semibold text-ink-500">Belum ada pengajuan kolaborasi.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $collaborations->links() }}</div>
@endsection
