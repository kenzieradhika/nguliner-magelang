@extends('admin.layouts.app')

@section('title', 'Audit Log')

@section('content')
    <div class="mb-8">
        <h1 class="ng-page-title">Audit Log</h1>
        <p class="mt-1 text-sm text-ink-500">Riwayat aktivitas admin di panel NGuliner</p>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="px-5 py-3.5">Waktu</th>
                    <th class="px-5 py-3.5">Admin</th>
                    <th class="px-5 py-3.5">Aksi</th>
                    <th class="px-5 py-3.5">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse($logs as $log)
                    <tr class="transition hover:bg-cream-50">
                        <td class="whitespace-nowrap px-5 py-3.5 text-xs text-ink-400">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3.5">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="rounded-full bg-cream-100 px-2.5 py-1 text-[10px] font-semibold uppercase text-ink-600">{{ $log->action }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-ink-500">
                            @if($log->details)
                                {{ collect($log->details)->map(fn ($v, $k) => "$k: $v")->implode(' · ') }}
                            @else
                                {{ $log->model_type ? class_basename($log->model_type) . ' #' . $log->model_id : '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-16 text-center text-sm text-ink-400">Belum ada aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
