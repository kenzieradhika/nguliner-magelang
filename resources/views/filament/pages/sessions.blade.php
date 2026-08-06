<x-filament-panels::page>
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/5">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Perangkat yang sedang login dengan akunmu. Cabut sesi perangkat yang tidak kamu kenali — pengguna tersebut akan langsung di-logout.
        </p>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-white/5">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Perangkat</th>
                    <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">IP</th>
                    <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Aktivitas Terakhir</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->sessions as $session)
                    <tr class="border-b border-gray-100 last:border-b-0 dark:border-white/5">
                        <td class="max-w-sm truncate px-4 py-3 text-gray-700 dark:text-gray-300">
                            <span class="font-medium">{{ $session['is_current'] ? 'Perangkat ini' : 'Perangkat lain' }}</span>
                            <span class="block truncate text-xs text-gray-400">{{ $session['agent'] }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $session['ip'] }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $session['last_activity'] }}</td>
                        <td class="px-4 py-3 text-right">
                            @unless ($session['is_current'])
                                <x-filament::button
                                    wire:click="revoke('{{ $session['id'] }}')"
                                    wire:confirm="Cabut sesi perangkat ini?"
                                    size="sm"
                                    color="danger"
                                    icon="heroicon-m-x-circle"
                                >
                                    Cabut
                                </x-filament::button>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-sm text-gray-400">Tidak ada sesi aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>