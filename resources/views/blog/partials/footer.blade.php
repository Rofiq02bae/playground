<footer class="blog-footer">
    <div class="blog-container-wide blog-footer-grid">
        <div class="blog-footer-brand">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-lg font-semibold text-white">F</span>
                <span class="text-lg font-semibold text-white">FunFarm</span>
            </div>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-emerald-50/80">
                Platform pertanian dan peternakan terpadu untuk informasi, layanan, dan pengelolaan usaha yang lebih produktif dan berkelanjutan.
            </p>
        </div>

        <div>
            <h2 class="blog-footer-title">Navigasi</h2>
            <ul class="blog-footer-links">
                <li><a href="{{ url('/') }}">Beranda</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li><a href="{{ url('/') }}#layanan-pertanian">Layanan Pertanian</a></li>
                <li><a href="{{ url('/') }}#marketplace">Marketplace</a></li>
            </ul>
        </div>

        <div>
            <h2 class="blog-footer-title">Informasi</h2>
            <ul class="blog-footer-links">
                <li><a href="{{ url('/') }}#tentang-kami">Tentang Kami</a></li>
                <li><a href="{{ url('/') }}#kontak">Kontak</a></li>
                <li><a href="{{ url('/') }}#informasi-layanan">Informasi Layanan</a></li>
            </ul>
        </div>
    </div>

    <div class="blog-footer-bottom">
        <div class="blog-container-wide blog-footer-bottom-inner">
            <p>&copy; {{ date('Y') }} FunFarm. Hak cipta dilindungi.</p>
        </div>
    </div>
</footer>
