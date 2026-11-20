<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#047857">
    <title>@yield('title', 'FunFarm WebApp')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased">
    <header class="sticky top-0 z-10 border-b border-stone-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-3xl items-center gap-3 px-4 py-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 font-semibold text-white">F</span>
            <div class="leading-tight">
                <p class="font-semibold text-stone-900">FunFarm <span class="font-normal text-stone-500">WebApp</span></p>
                <p class="text-xs text-stone-500">{{ auth()->user()->name }} &middot; {{ auth()->user()->roleLabel() }}</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Keluar</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-5 pb-24">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <nav class="fixed inset-x-0 bottom-0 border-t border-stone-200 bg-white">
        <div class="mx-auto grid max-w-3xl grid-cols-5 text-center text-xs font-medium text-stone-600">
            <a href="{{ route('app.home') }}" class="px-2 py-3 text-emerald-800">Beranda</a>
            <a href="{{ route('app.sawah.index',) }}" class="px-2 py-3 hover:text-emerald-800">Sawah</a>
            <a href="{{ route('app.calendar.index') }}" class="px-2 py-3 hover:text-emerald-800">Kalender</a>
            @if (auth()->user()->isPenggarap())
                <span class="px-2 py-3 text-stone-400">Laporan</span>
            @else
                <a href="{{ route('app.reports.index') }}" class="px-2 py-3 hover:text-emerald-800">Laporan</a>
            @endif
        </div>
    </nav>
</body>
</html>
