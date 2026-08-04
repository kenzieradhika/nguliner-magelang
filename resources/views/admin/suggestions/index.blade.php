@extends('admin.layouts.app')

@section('title', 'Saran Tempat')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Saran Tempat Baru</h1>
            <p class="mt-1 text-sm text-neutral-500">Saran dari pengunjung untuk menambah data kuliner</p>
        </div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.suggestions.index') }}" class="ng-tag-light {{ !request('status') ? '!bg-neutral-900 !text-white' : '' }}">Semua</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'new']) }}" class="ng-tag-light {{ request('status') === 'new' ? '!bg-neutral-900 !text-white' : '' }}">Baru</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'reviewed']) }}" class="ng-tag-light {{ request('status') === 'reviewed' ? '!bg-neutral-900 !text-white' : '' }}">Direview</a>
            <a href="{{ route('admin.suggestions.index', ['status' => 'imported']) }}" class="ng-tag-light {{ request('status') === 'imported' ? '!bg-neutral-900 !text-white' : '' }}">Diimpor</a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($suggestions as $suggestion)
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 {{ $suggestion->status === 'new' ? 'border-l-4 !border-l-red-500' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold">{{ $suggestion->name }}</h2>
                            @if($suggestion->category)
                                <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-[10px] font-semibold uppercase text-neutral-500">{{ $suggestion->category }}</span>
                            @endif
                            @if($suggestion->status === 'new')
                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-[10px] font-semibold uppercase text-red-600">Baru</span>
                            @endif
                        </div>
                        @if($suggestion->address)
                            <p class="mt-1 text-sm text-neutral-500">📍 {{ $suggestion->address }}</p>
                        @endif
                        @if($suggestion->contact)
                            <p class="mt-1 text-sm text-neutral-500">📞 {{ $suggestion->contact }}</p>
                        @endif
                        @if($suggestion->description)
                            <p class="mt-3 max-w-3xl rounded-xl bg-neutral-50 p-4 text-sm leading-relaxed text-neutral-600">{{ $suggestion->description }}</p>
                        @endif
                        <p class="mt-2 text-xs text-neutral-400">{{ $suggestion->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.places.create') }}?name={{ urlencode($suggestion->name) }}&category_id={{ optional(\App\Models\Category::where('name', $suggestion->category)->first())->id }}&address={{ urlencode($suggestion->address) }}&description={{ urlencode($suggestion->description) }}" class="ng-btn !px-4 !py-2 !text-xs">Konversi ke Kuliner</a>
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
                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-neutral-200 bg-white p-16 text-center text-sm text-neutral-400">Belum ada saran tempat.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $suggestions->links() }}</div>
@endsection
