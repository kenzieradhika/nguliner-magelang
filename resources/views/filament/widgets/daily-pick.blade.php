<x-filament-widgets::widget>
    <x-filament::section :heading="'Sorotan Hari Ini'" :description="'Rekomendasi harian berotasi yang tampil di beranda situs.'" icon="heroicon-o-sparkles">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @if ($pick)
                <a
                    href="{{ \App\Filament\Widgets\DailyPickWidget::getUrl($pick) }}"
                    class="group flex items-center gap-4 rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-amber-50 p-4 transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    @if ($pick->image)
                        <img src="{{ asset('storage/'.$pick->image) }}" alt="{{ $pick->name }}" class="h-16 w-16 flex-none rounded-xl object-cover">
                    @else
                        <span class="flex h-16 w-16 flex-none items-center justify-center rounded-xl bg-orange-100">
                            <x-filament::icon icon="heroicon-m-fire" class="h-8 w-8 text-orange-600" />
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-widest text-orange-700">Pilihan hari ini</p>
                        <h3 class="truncate font-display text-lg font-bold text-gray-950 group-hover:text-orange-800">{{ $pick->name }}</h3>
                        <p class="truncate text-sm text-gray-500">{{ $pick->tagline ?? $pick->category?->name }}</p>
                    </div>
                    <span class="ml-auto flex-none text-sm font-medium text-orange-700">Edit →</span>
                </a>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500">
                    Belum ada kuliner unggulan (featured). Tandai kuliner sebagai <strong>Unggulan</strong> agar masuk rotasi rekomendasi harian.
                </div>
            @endif

            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Unggulan lainnya</p>
                @forelse ($featured as $place)
                    <a href="{{ \App\Filament\Widgets\DailyPickWidget::getUrl($place) }}" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white px-3 py-2 text-sm transition hover:border-orange-200 hover:bg-orange-50">
                        <span class="truncate font-medium text-gray-700">{{ $place->name }}</span>
                        <span class="ml-3 flex-none text-xs text-gray-400">{{ number_format((int) $place->views) }} views</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400">Belum ada kuliner unggulan.</p>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
