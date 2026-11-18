<nav class="sticky top-0 z-30 border-b border-stone-200/80 bg-white/95 backdrop-blur" aria-label="Navigasi utama">
    <div class="blog-container">
        <div class="blog-nav-inner py-3">
            <a href="{{ route('blog.index') }}" class="blog-nav-brand">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-lg font-semibold text-white shadow-sm">F</span>
                <span class="leading-tight">
                    <span class="block text-base font-semibold text-stone-900">FunFarm</span>
                    <span class="block text-xs text-stone-500">Blog Pertanian & Peternakan</span>
                </span>
            </a>

            <button
                type="button"
                class="blog-nav-toggle"
                data-nav-toggle
                aria-expanded="false"
                aria-controls="blog-nav-menu"
                aria-label="Buka menu navigasi"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <div id="blog-nav-menu" class="blog-nav-menu" data-nav-menu>
                <a href="{{ url('/') }}" class="blog-nav-link">Beranda</a>
                <a href="{{ route('blog.index') }}" class="blog-nav-link is-active" aria-current="page">Blog</a>
            </div>
        </div>
    </div>
</nav>
