@extends('admin.layouts.app')

@section('title', 'Saran Tempat')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Saran Tempat Baru</h1>
            <p class="ng-page-subtitle">Saran dari pengunjung untuk menambah data kuliner</p>
        </div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.suggestions.index') }}" class="ng-tag-light {{ !request('status') ? '!bg-ink-900 !text-white' : '' }}">Semua</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'new']) }}" class="ng-tag-light {{ request('status') === 'new' ? '!bg-ink-900 !text-white' : '' }}">Baru</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'reviewed']) }}" class="ng-tag-light {{ request('status') === 'reviewed' ? '!bg-ink-900 !text-white' : '' }}">Direview</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'imported']) }}" class="ng-tag-light {{ request('status') === 'imported' ? '!bg-ink-900 !text-white' : '' }}">Diimpor</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($suggestions as $suggestion)
            <div class="ng-card p-6 {{ $suggestion->status === 'new' ? 'border-l-4 !border-l-sambal-600' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold">{{ $suggestion->name }}</h2>
                            @if($suggestion->category)
                                <span class="rounded-full bg-cream-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-ink-500">{{ $suggestion->category }}</span>
                            @endif
                            @if($suggestion->status === 'new')
                                <span class="ng-badge ng-badge-red">Baru</span>
                            @endif
                        </div>
                        @if($suggestion->address)
                            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-ink-500"><x-icon name="map-pin" class="h-3.5 w-3.5 text-sambal-600" /> {{ $suggestion->address }}</p>
                        @endif
                        @if($suggestion->contact)
                            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-ink-500"><x-icon name="phone" class="h-3.5 w-3.5 text-sambal-600" /> {{ $suggestion->contact }}</p>
                        @endif
                        @if($suggestion->description)
                            <p class="mt-3 max-w-3xl rounded-xl bg-cream-50 p-4 text-sm leading-relaxed text-ink-600">{{ $suggestion->description }}</p>
                        @endif
                        <p class="mt-2 flex items-center gap-1.5 text-xs text-ink-400"><x-icon name="clock" class="h-3 w-3" /> {{ $suggestion->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.places.create') }}?name={{ urlencode($suggestion->name) }}&category_id={{ optional(\App\Models\Category::where('name', $suggestion->category)->first())->id }}&address={{ urlencode($suggestion->address) }}&description={{ urlencode($suggestion->description) }}" class="ng-btn-primary !px-4 !py-2 !text-xs"><x-icon name="arrow-up-right" class="h-3 w-3" /> Konversi ke Kuliner</a>
                        <form action="{{ route('admin.suggestions.status', $suggestion) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" class="ng-input !w-32 !py-2" onchange="this.form.submit()">
                                @foreach(\App\Models\PlaceSuggestion::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($suggestion->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{ route('admin.suggestions.destroy', $suggestion) }}" method="POST" onsubmit="return confirm('Hapus saran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ng-btn-danger"><x-icon name="trash" class="h-3 w-3" /> Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="ng-card p-16 text-center text-sm text-ink-400">Belum ada saran tempat.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $suggestions->links() }}</div>
@endsection
