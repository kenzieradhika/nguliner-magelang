@extends('admin.layouts.app')

@section('title', 'Keamanan')
@section('section', 'Sistem')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Monitoring & proteksi</p>
            <h2 class="adm-page-title">Pusat Keamanan</h2>
            <p class="adm-page-subtitle">Notifikasi serangan & aktivitas mencurigakan — sekecil apa pun tetap dicatat.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($totalUnread)
                <span class="adm-badge adm-badge-soft-red"><x-icon name="bell" class="h-3 w-3" /> {{ $totalUnread }} belum dibaca</span>
            @endif
            <form action="{{ route('admin.security.read-all') }}" method="POST" onsubmit="return confirm('Tandai semua sebagai sudah dibaca?')">
                @csrf
                @method('PUT')
                <button type="submit" class="adm-btn-secondary"><x-icon name="check-circle" class="h-4 w-4" /> Tandai Dibaca Semua</button>
            </form>
            <form action="{{ route('admin.security.destroy-all') }}" method="POST" onsubmit="return confirm('Hapus seluruh riwayat insiden keamanan?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="adm-btn-secondary !text-red-600 hover:!bg-red-600 hover:!text-white"><x-icon name="trash" class="h-4 w-4" /> Hapus Semua</button>
            </form>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.security.index') }}" class="adm-card mb-6 flex flex-wrap items-center gap-3 p-4">
        <select name="severity" class="adm-input sm:!w-40" onchange="this.form.submit()">
            <option value="">Semua Severity</option>
            @foreach(\App\Models\SecurityEvent::SEVERITIES as $sev)
                <option value="{{ $sev }}" @selected(request('severity') === $sev)>{{ ucfirst($sev) }}</option>
            @endforeach
        </select>
        <select name="type" class="adm-input sm:!w-52" onchange="this.form.submit()">
            <option value="">Semua Jenis</option>
            @foreach(\App\Models\SecurityEvent::TYPES as $type => $label)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ $label }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm font-semibold text-ink-600">
            <input type="checkbox" name="unread" value="1" @checked(request('unread') === '1') class="accent-sambal-600" onchange="this.form.submit()">
            Hanya belum dibaca
        </label>
        @if(request('severity') || request('type') || request('unread'))
            <a href="{{ route('admin.security.index') }}" class="adm-btn-ghost"><x-icon name="x" class="h-3 w-3" /> Reset</a>
        @endif
    </form>

    <div class="space-y-4">
        @forelse($events as $event)
            <div class="adm-card p-5 transition-shadow duration-300 hover:shadow-[0_8px_32px_-16px_rgba(28,25,23,0.2)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ match ($event->severity) {
                                'critical' => 'border-red-300 bg-red-50 text-red-700',
                                'high' => 'border-orange-300 bg-orange-50 text-orange-700',
                                'medium' => 'border-amber-300 bg-amber-50 text-amber-700',
                                default => 'border-ink-900/10 bg-cream-100 text-ink-600',
                            } }}">{{ strtoupper($event->severity) }}</span>
                            <span class="text-sm font-bold text-ink-900">{{ $event->typeLabel() }}</span>
                            @if(!$event->read_at)
                                <span class="adm-badge adm-badge-soft-amber"><x-icon name="bell" class="h-3 w-3" /> baru</span>
                            @else
                                <span class="adm-badge adm-badge-soft-green"><x-icon name="check" class="h-3 w-3" /> dibaca</span>
                            @endif
                            @if($event->count > 1)
                                <span class="adm-badge adm-badge-soft-neutral">× {{ $event->count }}</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm font-semibold text-ink-900">{{ $event->message }}</p>
                        <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs text-ink-400">
                            <span class="flex items-center gap-1.5"><x-icon name="clock" class="h-3 w-3" /> {{ $event->created_at->diffForHumans() }}</span>
                            @if($event->ip)
                                <span class="flex items-center gap-1.5"><x-icon name="map-pin" class="h-3 w-3" /> {{ $event->ip }}</span>
                            @endif
                            @if($event->details)
                                @foreach($event->details as $k => $v)
                                    @if(!is_array($v) && !is_null($v))
                                        <span class="flex items-center gap-1.5"><x-icon name="info" class="h-3 w-3" /> <b>{{ $k }}</b>: {{ $v }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        @if($event->user_agent)
                            <p class="mt-1.5 truncate max-w-2xl text-[11px] text-ink-400" title="{{ $event->user_agent }}">{{ $event->user_agent }}</p>
                        @endif
                        @if($event->url)
                            <p class="mt-0.5 truncate max-w-2xl text-[11px] text-ink-400">{{ $event->url }}</p>
                        @endif
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        @if(!$event->read_at)
                            <form action="{{ route('admin.security.read', $event) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="adm-btn-secondary px-3 py-1.5 text-xs"><x-icon name="check" class="h-3 w-3" /> Sudah dibaca</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.security.destroy', $event) }}" method="POST" onsubmit="return confirm('Hapus insiden ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="adm-empty">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream-100 text-sambal-600">
                    <x-icon name="shield" class="h-7 w-7" />
                </span>
                <p class="font-semibold text-ink-600">Tidak ada insiden keamanan</p>
                <p class="max-w-sm text-xs text-ink-400">Login gagal, percobaan bajak sesi, dan CSRF mismatch akan muncul di sini otomatis.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $events->links() }}</div>
@endsection
