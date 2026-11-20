<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - FunFarm')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased">
    @php($pendingNav = \App\Models\ProjectExpense::where('status', 'pending')->count())
    {{-- Backdrop gelap tipis di belakang drawer menu mobile (klik untuk menutup) --}}
    <div id="admin-menu-backdrop" class="invisible fixed inset-0 z-30 bg-stone-950/40 opacity-0 transition-opacity duration-300 lg:hidden"></div>
    <header class="fixed inset-x-0 top-0 z-40 border-b border-stone-200 bg-white shadow-md">
        <div class="mx-auto flex max-w-6xl items-center gap-x-6 px-5 py-3">
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 font-semibold text-white">F</span>
                <span class="font-semibold text-stone-900">FunFarm <span class="font-normal text-stone-500">Admin</span></span>
            </a>

            {{-- Mode desktop: navbar panjang (tampil di layar lg ke atas) --}}
            <nav class="hidden items-center gap-4 text-sm font-medium lg:flex">
                <a href="{{ route('admin.articles.index') }}" class="text-emerald-800">Artikel</a>
                <a href="{{ route('admin.projects.index') }}" class="text-stone-600 hover:text-emerald-800">Proyek Sawah</a>
                <a href="{{ route('admin.calendar.index') }}" class="text-stone-600 hover:text-emerald-800">Kalender</a>
                <a href="{{ route('admin.reports.index') }}" class="text-stone-600 hover:text-emerald-800">Laporan</a>
                <a href="{{ route('admin.approvals.index') }}" class="text-stone-600 hover:text-emerald-800">Persetujuan
                    @if ($pendingNav > 0)<span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $pendingNav }}</span>@endif
                </a>
                <a href="{{ route('admin.services.index') }}" class="text-stone-600 hover:text-emerald-800">Layanan &amp; Harga</a>
                <a href="{{ route('admin.users.index') }}" class="text-stone-600 hover:text-emerald-800">Pengguna</a>
                <a href="{{ route('app.home') }}" class="text-stone-600 hover:text-emerald-800">WebApp</a>
                <a href="{{ route('blog.index') }}" class="text-stone-600 hover:text-emerald-800" target="_blank" rel="noopener">Lihat Blog</a>
            </nav>
            <div class="ml-auto hidden items-center gap-4 lg:flex">
                <span class="text-sm text-stone-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Keluar</button>
                </form>
            </div>

            {{-- Mode mobile/webapp: tombol hamburger (tampil di bawah lg) --}}
            <button type="button" id="admin-menu-button" class="ml-auto inline-flex items-center justify-center rounded-lg border border-stone-300 p-2 text-stone-700 transition hover:bg-stone-50 lg:hidden" aria-expanded="false" aria-controls="admin-mobile-menu" aria-label="Buka atau tutup menu navigasi">
                <svg id="admin-menu-icon-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg id="admin-menu-icon-close" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        {{-- Drawer menu mobile: melayang dari sisi kanan dengan fade in/out, dibuka dari hamburger --}}
        <nav id="admin-mobile-menu" class="invisible fixed inset-y-0 right-0 z-40 w-72 max-w-[85vw] translate-x-full bg-white opacity-0 shadow-2xl transition-all duration-300 ease-out lg:hidden">
            <div class="flex h-full flex-col overflow-y-auto px-5 py-4 text-sm font-medium">
                <div class="mb-2 flex items-center justify-between px-3">
                    <span class="font-semibold text-stone-900">Menu</span>
                    <button type="button" id="admin-menu-close" class="rounded-lg p-1.5 text-stone-500 transition hover:bg-stone-50 hover:text-stone-800" aria-label="Tutup menu navigasi">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
                    </button>
                </div>
                <a href="{{ route('admin.articles.index') }}" class="block rounded-lg px-3 py-2.5 text-emerald-800 hover:bg-stone-50">Artikel</a>
                <a href="{{ route('admin.projects.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">Proyek Sawah</a>
                <a href="{{ route('admin.calendar.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">Kalender</a>
                <a href="{{ route('admin.reports.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">Laporan</a>
                <a href="{{ route('admin.approvals.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">Persetujuan
                    @if ($pendingNav > 0)<span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $pendingNav }}</span>@endif
                </a>
                <a href="{{ route('admin.services.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">Layanan &amp; Harga</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">Pengguna</a>
                <a href="{{ route('app.home') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800">WebApp</a>
                <a href="{{ route('blog.index') }}" class="block rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-50 hover:text-emerald-800" target="_blank" rel="noopener">Lihat Blog</a>
                <div class="mt-auto flex items-center justify-between border-t border-stone-100 px-3 pt-3">
                    <span class="text-sm text-stone-500">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Keluar</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-5 pb-8 pt-24">
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <script>
        // Toggle drawer hamburger: geser + fade dari kanan untuk navbar mode mobile/webapp.
        (function () {
            var button = document.getElementById('admin-menu-button');
            var menu = document.getElementById('admin-mobile-menu');
            var backdrop = document.getElementById('admin-menu-backdrop');
            var closeButton = document.getElementById('admin-menu-close');
            if (!button || !menu) return;
            var iconOpen = document.getElementById('admin-menu-icon-open');
            var iconClose = document.getElementById('admin-menu-icon-close');
            var menuClosedClasses = ['invisible', 'translate-x-full', 'opacity-0'];
            var backdropClosedClasses = ['invisible', 'opacity-0'];
            function setOpen(open) {
                menuClosedClasses.forEach(function (c) { menu.classList.toggle(c, !open); });
                if (backdrop) backdropClosedClasses.forEach(function (c) { backdrop.classList.toggle(c, !open); });
                button.setAttribute('aria-expanded', String(open));
                if (iconOpen) iconOpen.classList.toggle('hidden', open);
                if (iconClose) iconClose.classList.toggle('hidden', !open);
            }
            button.addEventListener('click', function () {
                setOpen(menu.classList.contains('invisible'));
            });
            if (closeButton) closeButton.addEventListener('click', function () { setOpen(false); });
            if (backdrop) backdrop.addEventListener('click', function () { setOpen(false); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        })();
    </script>
</body>
</html>
