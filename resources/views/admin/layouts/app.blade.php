<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ url('/img/hero.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 text-neutral-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 flex w-60 flex-col border-r border-neutral-200 bg-white">
            <a href="{{ route('admin.dashboard') }}" class="flex h-16 items-center gap-2 border-b border-neutral-200 px-5">
                <span class="text-lg font-extrabold tracking-tighter">NGULINER</span>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Admin</span>
            </a>
            <nav class="flex-1 space-y-1 overflow-y-auto p-3 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.dashboard') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">📊 Dashboard</a>
                <a href="{{ route('admin.places.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.places.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">🍽️ Kuliner</a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.categories.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">🏷️ Kategori</a>
                <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.pages.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">📄 Halaman CMS</a>
                <a href="{{ route('admin.microsites.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.microsites.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">🌐 Microsite</a>

                <p class="px-3 pb-1 pt-5 text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Komunikasi</p>
                <a href="{{ route('admin.collaborations.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.collaborations.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">
                    💼 Kolaborasi
                    @if($pendingCollaborations = \App\Models\Collaboration::where('status', 'new')->count())
                        <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingCollaborations }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.reviews.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">
                    ⭐ Review
                    @if($pendingReviews = \App\Models\Review::where('is_approved', false)->count())
                        <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingReviews }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.suggestions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.suggestions.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">
                    💡 Saran Tempat
                    @if($pendingSuggestions = \App\Models\PlaceSuggestion::where('status', 'new')->count())
                        <span class="ml-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingSuggestions }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.feed.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.feed.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">📷 Feed Instagram</a>

                <p class="px-3 pb-1 pt-5 text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Sistem</p>
                <a href="{{ route('admin.backup.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.backup.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">💾 Backup</a>
                <a href="{{ route('admin.audit.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.audit.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">📜 Audit Log</a>
                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition hover:bg-neutral-100 {{ request()->routeIs('admin.users.*') ? 'bg-neutral-900 text-white hover:bg-neutral-900' : '' }}">👥 Pengguna</a>
                    @endif
                @endauth
            </nav>
            <div class="border-t border-neutral-200 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-neutral-900 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] capitalize text-neutral-400">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2 text-xs">
                    <a href="{{ route('home') }}" class="flex-1 rounded-lg border border-neutral-200 py-2 text-center transition hover:bg-neutral-100">Lihat Situs</a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-neutral-900 py-2 text-white transition hover:bg-neutral-700">Keluar</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="ml-60 flex-1">
            <main class="p-8">
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-green-50 px-5 py-4 text-sm text-green-700">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-700">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-700">
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
