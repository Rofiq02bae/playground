<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#047857">
    <title>@yield('title', 'FunFarm WebApp')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased">
    <header class="sticky top-0 z-10 border-b border-stone-200 bg-white/95 shadow-[0_1px_8px_rgba(0,0,0,0.04)] backdrop-blur">
        <div class="mx-auto flex max-w-3xl items-center gap-3 px-4 py-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div class="min-w-0 leading-tight">
                <p class="truncate font-semibold text-stone-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-stone-500">{{ auth()->user()->roleLabel() }} &middot; FunFarm</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="flex min-h-10 items-center gap-1.5 rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 active:bg-stone-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" /><path d="M8.25 12h13.5m0 0-3.75-3.75M21 12l-3.75 3.75" /></svg>
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-5 pb-32">
        @if (session('success'))
            <div class="mb-4 flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M8.5 12.5l2.5 2.5 4.5-5" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Navigasi bawah: ikon + label, halaman aktif ter-highlight, target sentuh besar, aman dari home-indicator --}}
    <nav class="fixed inset-x-0 bottom-0 border-t border-stone-200 bg-white/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-2px_12px_rgba(0,0,0,0.05)] backdrop-blur" aria-label="Navigasi utama">
        <div class="mx-auto grid max-w-3xl grid-cols-5">
            @php($navItems = [
                ['route' => 'app.home', 'active' => 'app.home', 'label' => 'Beranda', 'icon' => '<path d="M3 10.5 12 3l9 7.5" /><path d="M5.25 9.75V21h13.5V9.75" /><path d="M9.75 21v-6h4.5v6" />'],
                ['route' => 'app.sawah.index', 'active' => 'app.sawah.*', 'label' => 'Sawah', 'icon' => '<path d="M12 21s-7-5.6-7-11a7 7 0 1 1 14 0c0 5.4-7 11-7 11z" /><circle cx="12" cy="10" r="2.5" />'],
                ['route' => 'app.calendar.index', 'active' => 'app.calendar.*', 'label' => 'Kalender', 'icon' => '<rect x="4" y="5" width="16" height="16" rx="2" /><path d="M4 10h16M8 3v4M16 3v4" />'],
                ['route' => 'app.reports.index', 'active' => 'app.reports.*', 'label' => 'Laporan', 'icon' => '<path d="M7 20v-6M12 20V6M17 20v-9" /><path d="M3 20h18" />'],
                ['route' => 'app.agreements.index', 'active' => 'app.agreements.*', 'label' => 'Surat', 'icon' => '<path d="M6 3h9l4 4v14H6z" /><path d="M15 3v4h4" /><path d="M9 12h6M9 16h6" />'],
            ])
            @foreach ($navItems as $item)
                @php($isActive = request()->routeIs($item['active']))
                <a href="{{ route($item['route']) }}" @if ($isActive) aria-current="page" @endif class="flex flex-col items-center gap-1 px-1 py-2 text-[11px] {{ $isActive ? 'font-semibold text-emerald-900' : 'font-medium text-stone-500 transition hover:text-emerald-800' }}">
                    <span class="flex h-7 w-14 items-center justify-center rounded-full {{ $isActive ? 'bg-emerald-100' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                    </span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </nav>
</body>
</html>
