<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Produk - FunFarm')</title>
    <meta name="description" content="@yield('meta_description', 'Toko FunFarm: hasil panen, jasa pertanian, dan alat peternakan dari PT Sarana Akses Adikarya.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 antialiased">
    <header class="sticky top-0 z-30 border-b border-stone-200/80 bg-white/95 backdrop-blur">
        <div class="mx-auto max-w-6xl px-4">
            <div class="flex items-center gap-3 py-3 sm:gap-5">
                <a href="{{ route('shop.index') }}" class="flex shrink-0 items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-lg font-semibold text-white shadow-sm">F</span>
                    <span class="leading-tight">
                        <span class="block text-base font-semibold text-stone-900">FunFarm</span>
                        <span class="block text-xs text-stone-500">Toko Pertanian &amp; Peternakan</span>
                    </span>
                </a>

                <form method="GET" action="{{ route('shop.index') }}" class="mx-auto hidden w-full max-w-md md:block" role="search">
                    @if (request('kategori'))<input type="hidden" name="kategori" value="{{ request('kategori') }}">@endif
                    <div class="relative">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk, jasa, hasil panen..." class="w-full rounded-full border border-stone-300 py-2.5 pl-4 pr-11 text-sm outline-none focus:border-emerald-600" aria-label="Cari produk">
                        <button type="submit" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-emerald-700 text-white transition hover:bg-emerald-800" aria-label="Cari">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="M20 20l-3.5-3.5" /></svg>
                        </button>
                    </div>
                </form>

                <nav class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2" aria-label="Navigasi toko">
                    <a href="{{ route('blog.index') }}" class="hidden rounded-full px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-emerald-800 sm:block">Blog</a>
                    <a href="{{ route('shop.cart.index') }}" class="relative rounded-full p-2 text-stone-600 transition hover:bg-stone-100 hover:text-emerald-800" aria-label="Keranjang belanja">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.6" /><circle cx="17" cy="20" r="1.6" /><path d="M3 3h2l2.4 12.2a1.5 1.5 0 0 0 1.5 1.2h7.9a1.5 1.5 0 0 0 1.5-1.2L20 7H6" /></svg>
                        @if (\App\Support\Cart::count() > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold text-white">{{ \App\Support\Cart::count() }}</span>
                        @endif
                    </a>
                    @auth
                        @unless (auth()->user()->isAdmin())
                            <a href="{{ route('shop.orders.index') }}" class="hidden rounded-full px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-emerald-800 sm:block">Pesanan Saya</a>
                        @endunless
                        <span class="hidden max-w-[140px] truncate px-1 text-sm text-stone-600 lg:block">Halo, {{ auth()->user()->name }}</span>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.products.index') }}" class="rounded-full border border-stone-300 px-3.5 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Kelola Produk</a>
                        @endif
                        <form method="POST" action="{{ route('shop.logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-stone-300 px-3.5 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('shop.login') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50">Masuk</a>
                        <a href="{{ route('shop.register') }}" class="rounded-full bg-emerald-700 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Daftar</a>
                    @endauth
                </nav>
            </div>

            <form method="GET" action="{{ route('shop.index') }}" class="pb-3 md:hidden" role="search">
                @if (request('kategori'))<input type="hidden" name="kategori" value="{{ request('kategori') }}">@endif
                <div class="relative">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk, jasa, hasil panen..." class="w-full rounded-full border border-stone-300 py-2.5 pl-4 pr-11 text-base outline-none focus:border-emerald-600" aria-label="Cari produk">
                    <button type="submit" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-emerald-700 text-white transition hover:bg-emerald-800" aria-label="Cari">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="M20 20l-3.5-3.5" /></svg>
                    </button>
                </div>
            </form>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:py-8">
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-stone-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between">
            <p><span class="font-semibold text-stone-800">FunFarm</span> — Toko resmi PT Sarana Akses Adikarya.</p>
            <p>Hasil panen, jasa pertanian &amp; peternakan, dan alat — langsung dari perusahaan.</p>
        </div>
    </footer>
</body>
</html>
