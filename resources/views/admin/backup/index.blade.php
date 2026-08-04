@extends('admin.layouts.app')

@section('title', 'Backup Database')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="ng-page-title">Backup Database</h1>
            <p class="mt-1 text-sm text-ink-500">Backup otomatis setiap hari pukul 03.00, rotasi 14 file terakhir</p>
        </div>
        <form action="{{ route('admin.backup.run') }}" method="POST">
            @csrf
            <button type="submit" class="ng-btn-primary"><x-icon name="refresh" class="h-4 w-4" /> Backup Sekarang</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-ink-100 bg-white">
        <table class="w-full min-w-[600px] text-left text-sm">
            <thead class="border-b border-ink-100 bg-cream-50 text-xs uppercase tracking-wider text-ink-400">
                <tr>
                    <th class="px-5 py-3.5">File</th>
                    <th class="px-5 py-3.5">Ukuran</th>
                    <th class="px-5 py-3.5">Waktu</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse($files as $file)
                    <tr class="transition hover:bg-cream-50">
                        <td class="px-5 py-4 font-mono text-xs">{{ $file['name'] }}</td>
                        <td class="px-5 py-4 text-ink-500">{{ number_format($file['size'] / 1024, 1) }} KB</td>
                        <td class="px-5 py-4 text-ink-500">{{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified'])->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.backup.download', ['file' => $file['name']]) }}" class="rounded-lg border border-ink-100 px-3 py-1.5 text-xs transition hover:bg-cream-100">Download</a>
                                <form action="{{ route('admin.backup.destroy') }}" method="POST" onsubmit="return confirm('Hapus backup ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="file" value="{{ $file['name'] }}">
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Hapus</button>
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
@endsection
