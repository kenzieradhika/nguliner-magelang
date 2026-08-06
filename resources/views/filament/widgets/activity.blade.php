<x-filament-widgets::widget>
    <x-filament::section :heading="'Aktivitas Terakhir'" :description="'Aktivitas admin terbaru (audit log).'" icon="heroicon-m-clock">
        <div class="flow-root">
            <ul role="list" class="divide-y divide-gray-100">
                @forelse ($activities as $activity)
                    <li class="flex items-center gap-3 py-2.5">
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-orange-50 text-orange-700">
                            <x-filament::icon icon="{{ \App\Filament\Widgets\ActivityWidget::iconFor($activity->action) }}" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm text-gray-800">
                                <span class="font-medium">{{ $activity->user?->name ?? 'Sistem' }}</span>
                                <span class="text-gray-500">{{ $activity->action }}</span>
                            </p>
                        </div>
                        <span class="flex-none text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-400">Belum ada aktivitas tercatat.</li>
                @endforelse
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>