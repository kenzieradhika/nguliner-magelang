@extends('admin.layouts.app')

@section('title', 'Backup Database')
@section('section', 'Sistem')

@section('content')
    <div class="adm-page-header">
        <div>
            <p class="adm-kicker">Keamanan data</p>
            <h2 class="adm-page-title">Backup Database</h2>
            <p class="adm-page-subtitle">Backup otomatis setiap hari pukul 03.00, rotasi 14 file terakhir</p>
        </div>
        <form action="{{ route('admin.backup.run') }}" method="POST">
            @csrf
            <button type="submit" class="adm-btn"><x-icon name="refresh" class="h-4 w-4" /> Backup Sekarang</button>
        </form>
    </div>

    <div class="adm-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="adm-table min-w-[600px]">
                <thead class="border-b border-ink-900/[0.06] bg-cream-100/60">
                    <tr>
                        <th class="adm-th">File</th>
                        <th class="adm-th">Ukuran</th>
                        <th class="adm-th">Waktu</th>
                        <th class="adm-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-900/[0.05]">
                    @forelse($files as $file)
                        <tr class="transition-colors duration-150 hover:bg-cream-100/50">
                            <td class="adm-td font-mono text-xs text-ink-800">{{ $file['name'] }}</td>
                            <td class="adm-td text-ink-500">{{ number_format($file['size'] / 1024, 1) }} KB</td>
                            <td class="adm-td text-ink-500">{{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified'])->format('d M Y H:i') }}</td>
                            <td class="adm-td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('admin.backup.download', ['file' => $file['name']]) }}" class="adm-btn-ghost"><x-icon name="download" class="h-3.5 w-3.5" /> Download</a>
                                    <form action="{{ route('admin.backup.destroy') }}" method="POST" onsubmit="return confirm('Hapus backup ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file" value="{{ $file['name'] }}">
                                        <button type="submit" class="adm-btn-danger"><x-icon name="trash" class="h-3.5 w-3.5" /> Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center text-sm text-ink-400">Belum ada backup. Klik "Backup Sekarang".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
