<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ url('/img/hero.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-cream-100 text-ink-900 antialiased">
    <x-icons />
    @php
        $nav = [
            [
                'section' => 'Konten',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard')],
                    ['label' => 'Kuliner', 'icon' => 'utensils', 'route' => 'admin.places.index', 'pattern' => 'admin.places.*', 'active' => request()->routeIs('admin.places.*')],
                    ['label' => 'Kategori', 'icon' => 'tag', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories.*', 'active' => request()->routeIs('admin.categories.*')],
                    ['label' => 'Halaman CMS', 'icon' => 'file-text', 'route' => 'admin.pages.index', 'pattern' => 'admin.pages.*', 'active' => request()->routeIs('admin.pages.*')],
                    ['label' => 'Microsite', 'icon' => 'globe', 'route' => 'admin.microsites.index', 'pattern' => 'admin.microsites.*', 'active' => request()->routeIs('admin.microsites.*')],
                ],
            ],
            [
                'section' => 'Komunikasi',
                'items' => [
                    ['label' => 'Kolaborasi', 'icon' => 'briefcase', 'route' => 'admin.collaborations.index', 'pattern' => 'admin.collaborations.*', 'active' => request()->routeIs('admin.collaborations.*'), 'badge' => $pendingCollaborations = \App\Models\Collaboration::where('status', 'new')->count()],
                    ['label' => 'Review', 'icon' => 'star', 'route' => 'admin.reviews.index', 'pattern' => 'admin.reviews.*', 'active' => request()->routeIs('admin.reviews.*'), 'badge' => $pendingReviews = \App\Models\Review::where('is_approved', false)->count()],
                    ['label' => 'Saran Tempat', 'icon' => 'lightbulb', 'route' => 'admin.suggestions.index', 'pattern' => 'admin.suggestions.*', 'active' => request()->routeIs('admin.suggestions.*'), 'badge' => $pendingSuggestions = \App\Models\PlaceSuggestion::where('status', 'new')->count()],
                    ['label' => 'Feed Instagram', 'icon' => 'instagram', 'route' => 'admin.feed.index', 'pattern' => 'admin.feed.*', 'active' => request()->routeIs('admin.feed.*')],
                ],
            ],
            [
                'section' => 'Sistem',
                'items' => [
                    ['label' => 'Keamanan', 'icon' => 'shield', 'route' => 'admin.security.index', 'pattern' => 'admin.security.*', 'active' => request()->routeIs('admin.security.*'), 'badge' => $securityUnread = \App\Models\SecurityEvent::unread()->count(), 'danger' => true],
                    ['label' => 'Backup', 'icon' => 'database', 'route' => 'admin.backup.index', 'pattern' => 'admin.backup.*', 'active' => request()->routeIs('admin.backup.*')],
                    ['label' => 'Audit Log', 'icon' => 'scroll-text', 'route' => 'admin.audit.index', 'pattern' => 'admin.audit.*', 'active' => request()->routeIs('admin.audit.*')],
                ],
            ],
        ];
        if (auth()->user()->isSuperAdmin()) {
            $nav[count($nav) - 1]['items'][] = ['label' => 'Pengguna', 'icon' => 'users', 'route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'active' => request()->routeIs('admin.users.*')];
        }
    @endphp
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-ink-900/40 backdrop-blur-sm lg:hidden"></div>

        <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-white adm-sidebar transition-transform duration-300 ease-out lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <a href="{{ route('admin.dashboard') }}" class="flex h-16 items-center gap-3 px-6">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-ink-900 text-white shadow-[0_4px_12px_-2px_rgba(28,25,23,0.4)]">
                    <x-icon name="utensils" class="h-4 w-4" />
                </span>
                <span class="leading-none">
                    <span class="block text-lg font-extrabold tracking-tighter">NGULINER</span>
                    <span class="mt-1 block text-[9px] font-bold uppercase tracking-[0.35em] text-sambal-600">Console</span>
                </span>
            </a>

            <div class="px-3">
                <div class="adm-divider"></div>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                @foreach($nav as $group)
                    <div>
                        <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.25em] text-ink-300">{{ $group['section'] }}</p>
                        <div class="space-y-0.5">
                            @foreach($group['items'] as $item)
                                <a href="{{ route($item['route']) }}" class="adm-snav-item {{ $item['active'] ? 'adm-snav-active' : 'text-ink-500' }}">
                                    <x-icon name="{{ $item['icon'] }}" class="h-[18px] w-[18px] shrink-0" />
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if(($item['badge'] ?? 0) > 0)
                                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ ($item['danger'] ?? false) ? 'bg-red-600 text-white' : 'bg-sambal-600 text-white' }}">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="border-t border-ink-900/5 p-4">
                <div class="flex items-center gap-3 rounded-xl bg-cream-100/70 p-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sambal-600 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-ink-400">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                </div>
                <div class="mt-3 flex gap-2 text-xs">
                    <a href="{{ route('home') }}" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-ink-900/10 py-2 font-semibold text-ink-600 transition hover:bg-ink-900 hover:text-white">
                        <x-icon name="external-link" class="h-3.5 w-3.5" /> Situs
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-ink-900/10 py-2 font-semibold text-ink-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                            <x-icon name="x" class="h-3.5 w-3.5" /> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-30 border-b border-ink-900/5 bg-cream-100/80 backdrop-blur-xl">
                <div class="flex h-16 items-center justify-between gap-4 px-6 lg:px-10">
                    <div class="min-w-0">
                        <p class="truncate text-[10px] font-bold uppercase tracking-[0.3em] text-ink-400">
                            @yield('section', 'Admin')
                        </p>
                        <h1 class="truncate font-display text-lg font-bold tracking-tight text-ink-900">@yield('title')</h1>
                    </div>
                    <div class="flex shrink-0 items-center gap-5">
                        <button @click="sidebarOpen = true" class="rounded-lg border border-ink-900/10 p-2 text-ink-600 transition hover:bg-ink-900 hover:text-white lg:hidden">
                            <x-icon name="menu" class="h-4 w-4" />
                        </button>
                        <span class="hidden text-sm font-medium text-ink-500 md:block">{{ now()->format('l, d M Y') }}</span>
                        <span class="hidden h-4 w-px bg-ink-900/10 md:block"></span>
                        <a href="{{ route('home') }}" class="hidden items-center gap-1.5 text-xs font-semibold text-ink-500 transition hover:text-ink-900 md:flex">
                            <x-icon name="external-link" class="h-3.5 w-3.5" /> Lihat Situs
                        </a>
                    </div>
                </div>
            </header>

            <main class="adm-bg flex-1 px-6 py-8 lg:px-10 lg:py-10">
                @php
                    $securityUnread = \App\Models\SecurityEvent::unread()->count();
                    $securityUnreadHigh = \App\Models\SecurityEvent::unread()->whereIn('severity', ['high', 'critical'])->count();
                @endphp
                @if($securityUnread)
                    <a href="{{ route('admin.security.index') }}" class="mb-6 flex items-center gap-3 rounded-2xl border px-5 py-4 text-sm font-semibold transition {{ $securityUnreadHigh ? 'border-red-200 bg-red-50/70 text-red-800 hover:bg-red-50' : 'border-amber-200 bg-amber-50/70 text-amber-900 hover:bg-amber-50' }}">
                        <x-icon name="{{ $securityUnreadHigh ? 'alert-triangle' : 'alert-circle' }}" class="h-5 w-5 shrink-0" />
                        <span class="flex-1">{{ $securityUnread }} insiden keamanan belum dibaca{{ $securityUnreadHigh ? ' — termasuk serangan serius' : '' }}. Klik untuk meninjau.</span>
                        <x-icon name="arrow-right" class="h-4 w-4 shrink-0" />
                    </a>
                @endif
                @if(session('success'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-green-200 bg-green-50/70 px-5 py-4 text-sm font-medium text-green-800">
                        <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-green-600" /> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50/70 px-5 py-4 text-sm font-medium text-red-800">
                        <x-icon name="alert-circle" class="h-4 w-4 shrink-0 text-red-600" /> {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/70 px-5 py-4 text-sm text-red-800">
                        <ul class="list-disc space-y-1 pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
