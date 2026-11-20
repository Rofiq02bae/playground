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
    <div id="admin-menu-backdrop" class="invisible pointer-events-none fixed inset-0 z-30 bg-stone-950/40 opacity-0 transition-opacity duration-300 lg:hidden"></div>

    {{-- Sidebar desktop ala ChatGPT (lg ke atas): laci kiri berisi navigasi + profil di bawah; bisa dilipat dari tombol kiri atas --}}
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 hidden w-64 flex-col border-r border-stone-200 bg-white transition-transform duration-300 lg:flex">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 font-semibold text-white">F</span>
                <span class="font-semibold text-stone-900">FunFarm <span class="font-normal text-stone-500">Admin</span></span>
            </a>
            <button type="button" id="admin-sidebar-close" class="rounded-lg p-2 text-stone-500 transition hover:bg-stone-100 hover:text-stone-800" aria-label="Lipat sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2" /><path stroke-linecap="round" d="M9.5 4v16" /></svg>
            </button>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-2 text-sm font-medium">
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.articles.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z" /><path d="M15 3v4h4" /><path d="M9 12h6M9 16h6" /></svg>
                Artikel
            </a>
            <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.projects.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.6-7-11a7 7 0 1 1 14 0c0 5.4-7 11-7 11z" /><circle cx="12" cy="10" r="2.5" /></svg>
                Proyek Sawah
            </a>
            <a href="{{ route('admin.calendar.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.calendar.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="16" rx="2" /><path d="M4 10h16M8 3v4M16 3v4" /></svg>
                Kalender
            </a>
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 20v-6M12 20V6M17 20v-9" /><path d="M3 20h18" /></svg>
                Laporan
            </a>
            <a href="{{ route('admin.approvals.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.approvals.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M8.5 12.5l2.5 2.5 4.5-5" /></svg>
                Persetujuan
                @if ($pendingNav > 0)<span class="ml-auto rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $pendingNav }}</span>@endif
            </a>
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.services.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h8l10 10-8 8L3 11z" /><circle cx="7.5" cy="7.5" r="1.5" /></svg>
                Layanan &amp; Harga
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 font-semibold text-emerald-900' : 'text-stone-600 hover:bg-stone-100 hover:text-emerald-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5" /><path d="M3 20c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5" /><path d="M16 5a3.5 3.5 0 0 1 0 6.5M21 20c0-3-1.6-4.7-4-5.3" /></svg>
                Pengguna
            </a>
            <a href="{{ route('app.home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-100 hover:text-emerald-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2.5" width="10" height="19" rx="2" /><path d="M11 18.5h2" /></svg>
                WebApp
            </a>
            <a href="{{ route('blog.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-stone-600 hover:bg-stone-100 hover:text-emerald-800" target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3z" /></svg>
                Lihat Blog
            </a>
        </nav>
        <div class="border-t border-stone-200 p-3">
            <div class="flex items-center gap-3 rounded-lg px-2 py-1.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-stone-800">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-stone-500">Administrator</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="ml-auto">
                    @csrf
                    <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Keluar</button>
                </form>
            </div>
        </div>
    </aside>

    <header id="admin-topbar" class="fixed inset-x-0 top-0 z-40 border-b border-stone-200 bg-white shadow-md transition-all duration-300 lg:left-64">
        <div class="mx-auto flex max-w-6xl items-center gap-x-6 px-5 py-3">
            {{-- Tombol buka/tutup laci di kiri atas: drawer overlay di mobile, sidebar lipat di desktop --}}
            <button type="button" id="admin-menu-button" class="-ml-1 inline-flex items-center justify-center rounded-lg border border-stone-300 p-2 text-stone-700 transition hover:bg-stone-50" aria-expanded="false" aria-controls="admin-mobile-menu" aria-label="Buka atau tutup menu navigasi">
                <svg id="admin-menu-icon-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg id="admin-menu-icon-close" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 font-semibold text-white">F</span>
                <span class="font-semibold text-stone-900">FunFarm <span class="font-normal text-stone-500">Admin</span></span>
            </a>

            {{-- Navigasi desktop kini di sidebar kiri ala ChatGPT; bar atas hanya berisi tombol laci + logo --}}
            <span class="ml-auto hidden text-sm text-stone-500 lg:block">{{ auth()->user()->name }}</span>
        </div>

        {{-- Drawer navigasi mobile: laci meluncur dari kiri dengan fade in/out, dibuka dari tombol kiri atas --}}
        <nav id="admin-mobile-menu" class="invisible pointer-events-none fixed inset-y-0 left-0 z-40 w-72 max-w-[85vw] -translate-x-full bg-white opacity-0 shadow-2xl transition-all duration-300 ease-out lg:hidden">
            <div class="flex h-full flex-col overflow-y-auto px-5 py-4 text-sm font-medium">
                <div class="mb-2 flex items-center justify-between px-3">
                    <span class="font-semibold text-stone-900">Navigasi</span>
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

    <main id="admin-main" class="mx-auto max-w-6xl px-5 pb-8 pt-24 transition-all duration-300 lg:pl-64">
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <script>
        // Toggle laci navigasi: di mobile drawer overlay dari kiri; di desktop sidebar lipat ala ChatGPT (konten ikut bergeser).
        (function () {
            var button = document.getElementById('admin-menu-button');
            var menu = document.getElementById('admin-mobile-menu');
            var backdrop = document.getElementById('admin-menu-backdrop');
            var closeButton = document.getElementById('admin-menu-close');
            var sidebar = document.getElementById('admin-sidebar');
            var sidebarClose = document.getElementById('admin-sidebar-close');
            var topbar = document.getElementById('admin-topbar');
            var main = document.getElementById('admin-main');
            if (!button || !menu) return;
            var iconOpen = document.getElementById('admin-menu-icon-open');
            var iconClose = document.getElementById('admin-menu-icon-close');
            var menuClosedClasses = ['invisible', '-translate-x-full', 'opacity-0', 'pointer-events-none'];
            var backdropClosedClasses = ['invisible', 'opacity-0', 'pointer-events-none'];
            var desktopQuery = window.matchMedia('(min-width: 1024px)');
            function setMobileOpen(open) {
                menuClosedClasses.forEach(function (c) { menu.classList.toggle(c, !open); });
                if (backdrop) backdropClosedClasses.forEach(function (c) { backdrop.classList.toggle(c, !open); });
                button.setAttribute('aria-expanded', String(open));
                if (iconOpen) iconOpen.classList.toggle('hidden', open);
                if (iconClose) iconClose.classList.toggle('hidden', !open);
            }
            function setSidebarOpen(open) {
                if (!sidebar) return;
                sidebar.classList.toggle('-translate-x-full', !open);
                if (topbar) topbar.classList.toggle('lg:left-64', open);
                if (main) main.classList.toggle('lg:pl-64', open);
                button.setAttribute('aria-expanded', String(open));
            }
            function sidebarIsOpen() { return sidebar && !sidebar.classList.contains('-translate-x-full'); }
            button.addEventListener('click', function () {
                if (desktopQuery.matches) setSidebarOpen(!sidebarIsOpen());
                else setMobileOpen(menu.classList.contains('invisible'));
            });
            if (closeButton) closeButton.addEventListener('click', function () { setMobileOpen(false); });
            if (sidebarClose) sidebarClose.addEventListener('click', function () { setSidebarOpen(false); });
            if (backdrop) backdrop.addEventListener('click', function () { setMobileOpen(false); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMobileOpen(false); });
        })();
    </script>
</body>
</html>
