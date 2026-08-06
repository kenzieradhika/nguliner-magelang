@extends('admin.layouts.app')

@section('title', 'Saran Tempat')
@section('section', 'Komunikasi')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Inisiatif pengunjung</p>
            <h2 class="adm-page-title">Saran Tempat Baru</h2>
            <p class="adm-page-subtitle">Saran dari pengunjung untuk menambah data kuliner</p>
        </div>
        <div class="flex flex-wrap gap-1.5 rounded-full border border-ink-900/[0.08] bg-white p-1 text-xs font-semibold">
            <a href="{{ route('admin.suggestions.index') }}" class="rounded-full px-4 py-1.5 transition {{ !request('status') ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Semua</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'new']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'new' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Baru</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'reviewed']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'reviewed' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Direview</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'imported']) }}" class="rounded-full px-4 py-1.5 transition {{ request('status') === 'imported' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:text-ink-900' }}">Diimpor</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($suggestions as $suggestion)
            <div class="adm-card p-6 transition-shadow duration-300 hover:shadow-[0_8px_32px_-16px_rgba(28,25,23,0.2)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold text-ink-900">{{ $suggestion->name }}</h2>
                            @if($suggestion->category)
                                <span class="rounded-full bg-cream-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ink-500">{{ $suggestion->category }}</span>
                            @endif
                            @if($suggestion->status === 'new')
                                <span class="adm-badge adm-badge-soft-amber">Baru</span>
                            @else
                                <span class="adm-badge adm-badge-soft-neutral">{{ $suggestion->status }}</span>
                            @endif
                        </div>
                        @if($suggestion->address)
                            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-ink-500"><x-icon name="map-pin" class="h-3.5 w-3.5 text-sambal-600" /> {{ $suggestion->address }}</p>
                        @endif
                        @if($suggestion->contact)
                            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-ink-500"><x-icon name="phone" class="h-3.5 w-3.5 text-sambal-600" /> {{ $suggestion->contact }}</p>
                        @endif
                        @if($suggestion->description)
                            <p class="mt-3 max-w-3xl rounded-xl border-l-2 border-sambal-600/40 bg-cream-100/60 px-5 py-4 text-sm leading-relaxed text-ink-600">{{ $suggestion->description }}</p>
                        @endif
                        <p class="mt-2 flex items-center gap-1.5 text-xs text-ink-400"><x-icon name="clock" class="h-3 w-3" /> {{ $suggestion->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <a href="{{ route('admin.places.create') }}?name={{ urlencode($suggestion->name) }}&category_id={{ optional(\App\Models\Category::where('name', $suggestion->category)->first())->id }}&address={{ urlencode($suggestion->address) }}&description={{ urlencode($suggestion->description) }}" class="adm-btn px-4 py-2 text-xs"><x-icon name="arrow-up-right" class="h-3 w-3" /> Konversi ke Kuliner</a>
                        <form action="{{ route('admin.suggestions.status', $suggestion) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" class="adm-input !w-32 !py-2" onchange="this.form.submit()">
                                @foreach(\App\Models\PlaceSuggestion::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($suggestion->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{ route('admin.suggestions.destroy', $suggestion) }}" method="POST" onsubmit="return confirm('Hapus saran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="adm-empty">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream-100 text-sambal-600"><x-icon name="lightbulb" class="h-6 w-6" /></span>
                <p class="text-sm font-semibold text-ink-500">Belum ada saran tempat.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $suggestions->links() }}</div>
@endsection
