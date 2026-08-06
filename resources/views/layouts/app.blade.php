<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', site_setting('meta_description'))">
    <meta property="og:title" content="@yield('meta_title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', site_setting('meta_description'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @else
        <meta property="og:image" content="{{ url('/img/hero.svg') }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
    <title>@yield('meta_title', config('app.name'))</title>
    <link rel="icon" href="{{ url('/img/hero.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
    @stack('head')
</head>
<body class="bg-cream-50 text-ink-900 antialiased">
    <x-icons />
    <header class="sticky top-0 z-40 border-b border-ink-100 bg-cream-50/90 backdrop-blur">
        <div class="ng-container flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sambal-600 text-white shadow-sm shadow-sambal-600/30 transition group-hover:bg-sambal-700">
                    <x-icon name="utensils" class="h-4.5 w-4.5 h-[18px] w-[18px]" />
                </span>
                <span class="text-lg font-extrabold tracking-tighter">NGULINER</span>
                <span class="hidden text-[11px] font-semibold uppercase tracking-[0.25em] text-ink-400 sm:block">Magelang</span>
            </a>
            <nav class="hidden items-center gap-7 text-sm font-semibold md:flex">
                <a href="{{ route('home') }}#rekomendasi" class="text-ink-500 transition hover:text-ink-900">Rekomendasi</a>
                <a href="{{ route('home') }}#kategori" class="text-ink-500 transition hover:text-ink-900">Kategori</a>
                <a href="{{ route('map') }}" class="text-ink-500 transition hover:text-ink-900">Peta</a>
                <a href="{{ route('collaboration.create') }}" class="text-ink-500 transition hover:text-ink-900">Kolaborasi</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="ng-btn-primary !px-4 !py-2">Admin</a>
                @endauth
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('search') }}" aria-label="Cari" class="flex h-9 w-9 items-center justify-center rounded-full border border-ink-100 bg-white text-ink-600 transition hover:border-ink-900 hover:text-ink-900">
                    <x-icon name="search" class="h-4 w-4" />
                </a>
                <button id="nav-toggle" class="flex h-9 w-9 items-center justify-center rounded-full border border-ink-100 bg-white text-ink-600 md:hidden" aria-label="Menu">
                    <x-icon name="menu" class="h-4 w-4" />
                </button>
            </div>
        </div>
        <div id="nav-mobile" class="hidden border-t border-ink-100 bg-white px-5 py-4 md:hidden">
            <nav class="flex flex-col gap-3 text-sm font-semibold text-ink-700">
                <a href="{{ route('home') }}#rekomendasi">Rekomendasi</a>
                <a href="{{ route('home') }}#kategori">Kategori</a>
                <a href="{{ route('map') }}">Peta</a>
                <a href="{{ route('collaboration.create') }}">Kolaborasi</a>
                <a href="{{ route('search') }}">Cari Kuliner</a>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="ng-btn-primary !px-4 !py-2">Admin Panel</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-ink-100 bg-cream-100">
        <div class="ng-container grid gap-10 py-14 md:grid-cols-3">
            <div>
                <p class="flex items-center gap-2.5 font-display text-lg font-bold tracking-tight">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sambal-600 text-white">
                        <x-icon name="utensils" class="h-4 w-4" />
                    </span>
                    NGuliner <span class="font-sans text-ink-400">Magelang</span>
                </p>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-500">
                    {{ site_setting('tagline') }}
                </p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-ink-400">Jelajahi</p>
                <ul class="mt-4 space-y-2.5 text-sm font-medium">
                    <li><a href="{{ route('map') }}" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="map-pin" class="h-3.5 w-3.5 text-sambal-600" /> Peta Kuliner</a></li>
                    <li><a href="{{ route('search') }}" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="search" class="h-3.5 w-3.5 text-sambal-600" /> Pencarian</a></li>
                    <li><a href="{{ route('collaboration.create') }}" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="briefcase" class="h-3.5 w-3.5 text-sambal-600" /> Kolaborasi</a></li>
                    <li><a href="{{ route('suggestion.create') }}" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="lightbulb" class="h-3.5 w-3.5 text-sambal-600" /> Saran Tempat</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-ink-400">Halaman</p>
                <ul class="mt-4 space-y-2.5 text-sm font-medium">
                    <li><a href="{{ route('page.show', 'tentang') }}" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="info" class="h-3.5 w-3.5 text-sambal-600" /> Tentang</a></li>
                    <li><a href="{{ route('page.show', 'kerja-sama') }}" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="handshake" class="h-3.5 w-3.5 text-sambal-600" /> Kerja Sama</a></li>
                    <li>
                        <a href="{{ site_setting('instagram_url') }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-ink-500 transition hover:text-ink-900"><x-icon name="instagram" class="h-3.5 w-3.5 text-sambal-600" /> {{ site_setting('instagram') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-ink-100">
            <div class="ng-container flex flex-col items-center justify-between gap-2 py-6 text-xs text-ink-400 md:flex-row">
                <p>&copy; {{ now()->year }} {{ site_setting('copyright') }}</p>
                <p class="flex items-center gap-1.5">Support Resto &amp; UMKM Lokal</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('nav-toggle')?.addEventListener('click', () => {
            document.getElementById('nav-mobile')?.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
