@extends('admin.layouts.app')

@section('title', 'Keamanan')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Pusat Keamanan</h1>
            <p class="ng-page-subtitle">Notifikasi serangan & aktivitas mencurigakan — sekecil apa pun tetap dicatat.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($totalUnread)
                <span class="ng-badge ng-badge-red"><x-icon name="bell" class="h-3 w-3" /> {{ $totalUnread }} belum dibaca</span>
            @endif
            <form action="{{ route('admin.security.read-all') }}" method="POST" onsubmit="return confirm('Tandai semua sebagai sudah dibaca?')">
                @csrf
                @method('PUT')
                <button type="submit" class="ng-btn-outline"><x-icon name="check-circle" class="h-4 w-4" /> Tandai Dibaca Semua</button>
            </form>
            <form action="{{ route('admin.security.destroy-all') }}" method="POST" onsubmit="return confirm('Hapus seluruh riwayat insiden keamanan?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="ng-btn-danger"><x-icon name="trash" class="h-4 w-4" /> Hapus Semua</button>
            </form>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.security.index') }}" class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-ink-100 bg-white p-4">
        <select name="severity" class="ng-input sm:!w-40" onchange="this.form.submit()">
            <option value="">Semua Severity</option>
            @foreach(\App\Models\SecurityEvent::SEVERITIES as $sev)
                <option value="{{ $sev }}" @selected(request('severity') === $sev)>{{ ucfirst($sev) }}</option>
            @endforeach
        </select>
        <select name="type" class="ng-input sm:!w-52" onchange="this.form.submit()">
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
            <a href="{{ route('admin.security.index') }}" class="ng-btn-outline !px-3 !py-2 !text-xs"><x-icon name="x" class="h-3 w-3" /> Reset</a>
        @endif
    </form>

    <div class="space-y-4">
        @forelse($events as $event)
            <div class="ng-card p-5 {{ $event->read_at ? '' : '!border-l-4 !border-l-sambal-600' }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="ng-badge {{ $event->severityColor() }}">{{ strtoupper($event->severity) }}</span>
                            <span class="text-sm font-bold">{{ $event->typeLabel() }}</span>
                            @if(!$event->read_at)
                                <span class="ng-badge ng-badge-red"><x-icon name="bell" class="h-3 w-3" /> baru</span>
                            @else
                                <span class="ng-badge ng-badge-green"><x-icon name="check" class="h-3 w-3" /> dibaca</span>
                            @endif
                            @if($event->count > 1)
                                <span class="ng-badge ng-badge-amber">× {{ $event->count }}</span>
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
                                <button type="submit" class="ng-btn-outline !px-3 !py-2 !text-xs"><x-icon name="check" class="h-3 w-3" /> Sudah dibaca</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.security.destroy', $event) }}" method="POST" onsubmit="return confirm('Hapus insiden ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ng-btn-danger"><x-icon name="trash" class="h-3 w-3" /></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="ng-card flex flex-col items-center gap-3 p-16 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream-100 text-sambal-600">
                    <x-icon name="shield" class="h-7 w-7" />
                </span>
                <p class="font-semibold text-ink-600">Tidak ada insiden keamanan</p>
                <p class="max-w-sm text-sm text-ink-400">Login gagal, percobaan bajak sesi, dan CSRF mismatch akan muncul di sini otomatis.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $events->links() }}</div>
@endsection