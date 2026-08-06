@extends('admin.layouts.app')

@section('title', 'Audit Log')
@section('section', 'Sistem')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Jejak aktivitas</p>
            <h2 class="adm-page-title">Audit Log</h2>
            <p class="adm-page-subtitle">Riwayat aktivitas admin di panel NGuliner</p>
        </div>
    </div>

    <div class="adm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="adm-table min-w-[700px]">
                <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                    <tr>
                        <th class="adm-th">Waktu</th>
                        <th class="adm-th">Admin</th>
                        <th class="adm-th">Aksi</th>
                        <th class="adm-th">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/[0.05]">
                    @forelse($logs as $log)
                        <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                            <td class="whitespace-nowrap px-5 py-3.5 text-xs tabular-nums text-ink-400">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-ink-800">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full border border-ink-900/10 bg-cream-100/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-ink-600">{{ $log->action }}</span>
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
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
