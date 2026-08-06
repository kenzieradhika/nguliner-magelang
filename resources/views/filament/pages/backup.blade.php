<x-filament-panels::page>
    <div class="space-y-4">
        <div class="rounded-xl border border-primary/10 bg-surface p-6 shadow-sm">
            <h2 class="text-base font-semibold text-text">Backup database SQLite</h2>
            <p class="mt-1 text-sm leading-relaxed text-text-muted">
                Backup disimpan di folder
                <code class="rounded bg-primary/10 px-1.5 py-0.5 font-mono text-xs text-primary-dark">storage/app/backups</code>
                dengan rotasi otomatis 14 file terbaru. Klik <strong>Backup Sekarang</strong> untuk cadangan manual,
                atau gunakan <strong>Restore Backup</strong> untuk memulihkan database dari file .sqlite
                (backup keselamatan dibuat otomatis sebelumnya).
            </p>
        </div>

        @if (count($this->files))
            <div class="overflow-hidden rounded-xl border border-primary/10 bg-surface shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-primary/10 bg-primary/5">
                            <th class="px-4 py-3 font-semibold text-text-muted">File</th>
                            <th class="px-4 py-3 font-semibold text-text-muted">Ukuran</th>
                            <th class="px-4 py-3 font-semibold text-text-muted">Dibuat</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->files as $file)
                            <tr class="border-b border-primary/10 last:border-b-0 hover:bg-primary/5">
                                <td class="max-w-xs truncate px-4 py-3 font-mono text-xs text-text" title="{{ $file['name'] }}">
                                    {{ $file['name'] }}
                                </td>
                                <td class="px-4 py-3 text-text-muted">{{ number_format($file['size'] / 1024, 1) }} KB</td>
                                <td class="px-4 py-3 text-text-muted">{{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified'])->diffForHumans() }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <x-filament::button
                                            wire:click="download('{{ $file['name'] }}')"
                                            size="sm"
                                            color="gray"
                                            icon="heroicon-m-arrow-down-tray"
                                        >
                                            Unduh
                                        </x-filament::button>
                                        <x-filament::button
                                            wire:click="delete('{{ $file['name'] }}')"
                                            wire:confirm="Hapus backup ini?"
                                            size="sm"
                                            color="danger"
                                            icon="heroicon-m-trash"
                                        >
                                            Hapus
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-primary/20 bg-surface p-12 text-center text-sm text-text-muted shadow-sm">
                Belum ada backup. Klik "Backup Sekarang" di kanan atas untuk membuat cadangan pertama.
            </div>
        @endif
    </div>
</x-filament-panels::page>