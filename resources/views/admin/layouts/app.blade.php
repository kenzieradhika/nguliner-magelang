<!DOCTYPE html>
<html lang="id">
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
<body class="bg-cream-100 text-ink-900 antialiased">
    <x-icons />
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 flex w-60 flex-col border-r border-ink-100 bg-white">
            <a href="{{ route('admin.dashboard') }}" class="flex h-16 items-center gap-2.5 border-b border-ink-100 px-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sambal-600 text-white">
                    <x-icon name="utensils" class="h-4 w-4" />
                </span>
                <span class="text-lg font-extrabold tracking-tighter">NGULINER</span>
                <span class="text-[10px] font-bold uppercase tracking-widest text-ink-400">Admin</span>
            </a>
            <nav class="flex-1 space-y-1 overflow-y-auto p-3 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="home" class="h-4 w-4 shrink-0" /> Dashboard
                </a>
                <a href="{{ route('admin.places.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.places.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="utensils" class="h-4 w-4 shrink-0" /> Kuliner
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.categories.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="tag" class="h-4 w-4 shrink-0" /> Kategori
                </a>
                <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.pages.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="file-text" class="h-4 w-4 shrink-0" /> Halaman CMS
                </a>
                <a href="{{ route('admin.microsites.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.microsites.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="globe" class="h-4 w-4 shrink-0" /> Microsite
                </a>

                <p class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-widest text-ink-400">Komunikasi</p>
                <a href="{{ route('admin.collaborations.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.collaborations.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="briefcase" class="h-4 w-4 shrink-0" /> Kolaborasi
                    @if($pendingCollaborations = \App\Models\Collaboration::where('status', 'new')->count())
                        <span class="ml-auto rounded-full bg-sambal-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingCollaborations }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.reviews.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="star" class="h-4 w-4 shrink-0" /> Review
                    @if($pendingReviews = \App\Models\Review::where('is_approved', false)->count())
                        <span class="ml-auto rounded-full bg-sambal-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingReviews }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.suggestions.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.suggestions.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="lightbulb" class="h-4 w-4 shrink-0" /> Saran Tempat
                    @if($pendingSuggestions = \App\Models\PlaceSuggestion::where('status', 'new')->count())
                        <span class="ml-auto rounded-full bg-sambal-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingSuggestions }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.feed.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.feed.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="instagram" class="h-4 w-4 shrink-0" /> Feed Instagram
                </a>

                <p class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-widest text-ink-400">Sistem</p>
                <a href="{{ route('admin.backup.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.backup.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="database" class="h-4 w-4 shrink-0" /> Backup
                </a>
                <a href="{{ route('admin.audit.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.audit.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                    <x-icon name="scroll-text" class="h-4 w-4 shrink-0" /> Audit Log
                </a>
                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-cream-100 hover:text-ink-900' }}">
                            <x-icon name="users" class="h-4 w-4 shrink-0" /> Pengguna
                        </a>
                    @endif
                @endauth
            </nav>
            <div class="border-t border-ink-100 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-sambal-600 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] capitalize text-ink-400">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2 text-xs">
                    <a href="{{ route('home') }}" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-ink-100 py-2 font-semibold transition hover:bg-cream-100">
                        <x-icon name="external-link" class="h-3.5 w-3.5" /> Lihat Situs
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-ink-900 py-2 font-semibold text-white transition hover:bg-ink-700">
                            <x-icon name="x" class="h-3.5 w-3.5" /> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="ml-60 flex-1">
            <main class="p-8">
                @if(session('success'))
                    <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-green-100 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                        <x-icon name="check-circle" class="h-4 w-4 shrink-0 text-green-600" /> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-medium text-red-800">
                        <x-icon name="alert-circle" class="h-4 w-4 shrink-0 text-red-600" /> {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-5 py-4 text-sm text-red-800">
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
