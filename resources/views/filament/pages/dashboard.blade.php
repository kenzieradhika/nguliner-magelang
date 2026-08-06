<x-filament-panels::page class="fi-dashboard-page">
    <div class="mb-4 flex flex-col gap-4 rounded-2xl border border-orange-100 bg-gradient-to-br from-orange-50 via-amber-50 to-white p-6">
        <div>
            <h1 class="font-display text-2xl font-bold tracking-tight text-gray-950">
                {{ $greeting }}, {{ auth()->user()->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ now()->translatedFormat('l, d F Y') }} — Ringkasan kondisi NGuliner Magelang hari ini.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-filament::badge color="primary" icon="heroicon-m-sparkles">Panel Kelola</x-filament::badge>
            @if (auth()->user()->role === 'superadmin')
                <x-filament::badge color="gray" icon="heroicon-m-shield-check">Super Admin</x-filament::badge>
            @else
                <x-filament::badge color="gray" icon="heroicon-m-user">Editor</x-filament::badge>
            @endif
        </div>
    </div>

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="[
            ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
            ...$this->getWidgetData(),
        ]"
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>