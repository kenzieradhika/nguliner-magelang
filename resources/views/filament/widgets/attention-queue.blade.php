<x-filament-widgets::widget>
    <x-filament::section :heading="'Perlu Perhatian'" :description="'Antrean tugas yang menunggu tindakanmu.'" icon="heroicon-o-bell-alert">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($counts as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="group flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-{{ $item['color'] }}-500"></span>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-950">{{ $item['label'] }}</span>
                    </div>
                    <span class="font-display text-xl font-bold text-gray-950">{{ $item['count'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-3 text-xs text-gray-500">
            @if ($lastBackup)
                Backup terakhir: <span class="font-medium text-gray-700">{{ $lastBackup }}</span>
            @else
                Belum ada backup. Buat backup pertama dari menu Sistem → Backup Database.
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
