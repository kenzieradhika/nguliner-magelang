<div class="rounded-xl border border-gray-200 bg-white p-4">
    <p class="mb-3 text-[11px] font-bold uppercase tracking-widest text-gray-400">Riwayat Audit</p>
    @php
        $auditLogs = \App\Models\AuditLog::query()
            ->where('model_type', get_class($record))
            ->where('model_id', $record->id)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();
    @endphp
    <ul class="divide-y divide-gray-100">
        @forelse ($auditLogs as $log)
            <li class="flex items-center justify-between gap-3 py-2 text-sm">
                <span class="min-w-0 truncate text-gray-700">
                    <span class="font-medium">{{ $log->user?->name ?? 'Sistem' }}</span>
                    <span class="text-gray-500">· {{ $log->action }}</span>
                </span>
                <span class="flex-none text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
            </li>
        @empty
            <li class="py-3 text-sm text-gray-400">Belum ada aktivitas tercatat untuk data ini.</li>
        @endforelse
    </ul>
</div>