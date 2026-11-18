<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - FunFarm')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-6 gap-y-2 px-5 py-3">
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 font-semibold text-white">F</span>
                <span class="font-semibold text-stone-900">FunFarm <span class="font-normal text-stone-500">Admin</span></span>
            </a>
            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('admin.articles.index') }}" class="text-emerald-800">Artikel</a>
                <a href="{{ route('blog.index') }}" class="text-stone-600 hover:text-emerald-800" target="_blank" rel="noopener">Lihat Blog</a>
            </nav>
            <div class="ml-auto flex items-center gap-4">
                <span class="hidden text-sm text-stone-500 sm:block">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-8">
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
