<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Referensi kuliner Magelang: bakso, es dawet, martabak, nasi goreng magelangan, street food. Rekomendasi makan Magelang terpercaya.')">
    <meta property="og:title" content="@yield('meta_title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', 'Referensi kuliner Magelang: bakso, es dawet, martabak, nasi goreng magelangan, street food. Rekomendasi makan Magelang terpercaya.')">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <title>@yield('meta_title', config('app.name'))</title>
    <link rel="icon" href="{{ url('/img/hero.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
    @stack('head')
</head>
<body class="bg-white text-neutral-900 antialiased">
    <header class="border-b border-neutral-200 bg-white/90 backdrop-blur sticky top-0 z-40">
        <div class="ng-container flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-xl font-extrabold tracking-tighter">NGULINER</span>
                <span class="hidden text-[11px] font-semibold uppercase tracking-[0.25em] text-neutral-400 sm:block">Magelang</span>
            </a>
            <nav class="hidden items-center gap-7 text-sm font-medium md:flex">
                <a href="{{ route('home') }}#rekomendasi" class="transition hover:text-neutral-500">Rekomendasi</a>
                <a href="{{ route('home') }}#kategori" class="transition hover:text-neutral-500">Kategori</a>
                <a href="{{ route('map') }}" class="transition hover:text-neutral-500">Peta</a>
                <a href="{{ route('collaboration.create') }}" class="transition hover:text-neutral-500">Kolaborasi</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="ng-btn !px-4 !py-2">Admin</a>
                @endauth
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('search') }}" aria-label="Cari" class="flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 transition hover:border-neutral-900">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.34-4.34M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                </a>
                <button id="nav-toggle" class="flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 md:hidden" aria-label="Menu">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
        <div id="nav-mobile" class="hidden border-t border-neutral-100 bg-white px-5 py-4 md:hidden">
            <nav class="flex flex-col gap-3 text-sm font-medium">
                <a href="{{ route('home') }}#rekomendasi">Rekomendasi</a>
                <a href="{{ route('home') }}#kategori">Kategori</a>
                <a href="{{ route('map') }}">Peta</a>
                <a href="{{ route('collaboration.create') }}">Kolaborasi</a>
                <a href="{{ route('search') }}">Cari Kuliner</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-neutral-200 bg-neutral-50">
        <div class="ng-container grid gap-10 py-14 md:grid-cols-3">
            <div>
                <p class="text-lg font-extrabold tracking-tighter">NGULINER <span class="text-neutral-400">MAGELANG</span></p>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-neutral-500">
                    Referensi kuliner Magelang. Support resto &amp; UMKM lokal, dari yang legendaris hingga yang baru muncul.
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Jelajahi</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('map') }}" class="transition hover:text-neutral-900">Peta Kuliner</a></li>
                    <li><a href="{{ route('search') }}" class="transition hover:text-neutral-900">Pencarian</a></li>
                    <li><a href="{{ route('collaboration.create') }}" class="transition hover:text-neutral-900">Kolaborasi</a></li>
                    <li><a href="{{ route('suggestion.create') }}" class="transition hover:text-neutral-900">Saran Tempat</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Halaman</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('page.show', 'tentang') }}" class="transition hover:text-neutral-900">Tentang</a></li>
                    <li><a href="{{ route('page.show', 'kerja-sama') }}" class="transition hover:text-neutral-900">Kerja Sama</a></li>
                    <li>
                        <a href="https://www.instagram.com/ngulinermagelang/" target="_blank" rel="noopener" class="transition hover:text-neutral-900">Instagram @ngulinermagelang</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-neutral-200">
            <div class="ng-container flex flex-col items-center justify-between gap-2 py-6 text-xs text-neutral-400 md:flex-row">
                <p>&copy; {{ now()->year }} NGuliner Magelang</p>
                <p>Support Resto &amp; UMKM Lokal</p>
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
