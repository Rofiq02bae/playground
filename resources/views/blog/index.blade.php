@extends('blog.layouts.app')

@php
    $landing = $isLanding ?? false;
@endphp

@section('title', ($isSearchPage ?? false) ? 'Pencarian Artikel - FunFarm' : 'Blog & Artikel - FunFarm')
@section('meta_description', 'Baca artikel terbaru seputar pertanian, peternakan, dan teknologi pertanian dari FunFarm.')
@section('canonical', ($isSearchPage ?? false) ? route('blog.search', request()->query()) : route('blog.index', request()->query()))

@section('content')
    @if ($landing)
        {{-- A. Blog Header --}}
        <section class="blog-header-section">
            <div class="blog-container-wide">
                <div class="blog-header-inner">
                    <div class="blog-header-copy">
                        <p class="section-label section-label-light">BLOG / ARTIKEL</p>
                        <h1 class="blog-header-title">Wawasan Terkini untuk Pertanian &amp; Peternakan yang Lebih Produktif</h1>
                        <p class="blog-header-desc">Temukan informasi pertanian dan peternakan, tips budidaya, serta teknologi yang membantu Anda mengelola usaha secara lebih cerdas dan berkelanjutan.</p>
                    </div>

                    <form action="{{ route('blog.search') }}" method="GET" class="blog-header-search" role="search">
                        <label for="q" class="sr-only">Cari artikel</label>
                        <input
                            id="q"
                            name="q"
                            type="search"
                            value="{{ $search ?? '' }}"
                            maxlength="100"
                            placeholder="Cari artikel berdasarkan judul..."
                            class="blog-search-input"
                        >
                        <button type="submit" class="blog-search-button">Cari</button>
                    </form>

                    <nav class="blog-category-nav" aria-label="Navigasi kategori artikel">
                        <a href="{{ route('blog.index') }}" class="category-chip is-active">Semua</a>
                        @foreach ($categories as $cat)
                            <a href="{{ route('blog.category', $cat->slug) }}" class="category-chip">{{ $cat->name }}</a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </section>

        {{-- B. Latest Articles --}}
        <section class="blog-latest-section">
            <div class="blog-container-wide">
                <div class="blog-section-top">
                    <x-blog.section-heading
                        label="UPDATE"
                        title="Informasi Terbaru dari FunFarm"
                        description="Baca artikel terbaru seputar pertanian, peternakan, dan teknologi."
                    />
                    <a href="{{ route('blog.index', ['show' => 'all']) }}" class="blog-view-all-link">Lihat seluruh artikel</a>
                </div>

                @if ($articles->count() > 0)
                    <div class="blog-articles-grid-compact">
                        @foreach ($articles as $article)
                            <x-blog.article-card :article="$article" />
                        @endforeach
                    </div>
                @else
                    <div class="blog-empty-state">
                        <h3 class="blog-empty-title">Belum ada artikel</h3>
                        <p class="blog-empty-text">Artikel terbaru akan muncul di sini setelah dipublikasikan.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- C. Review Section --}}
        <section class="blog-review-section">
            <div class="blog-container-wide">
                <x-blog.section-heading
                    label="TESTIMONIAL"
                    title="Apa Kata Mereka?"
                    description="Cerita dan pengalaman mereka dalam menggunakan layanan FunFarm."
                    align="center"
                />

                @if (($testimonials ?? collect())->count() > 0)
                    <div class="blog-reviews-grid">
                        @foreach ($testimonials as $testimonial)
                            <x-blog.review-card
                                :name="$testimonial->name"
                                :role="$testimonial->role"
                                :avatar="$testimonial->avatar"
                                :content="$testimonial->content"
                                :rating="$testimonial->rating"
                            />
                        @endforeach
                    </div>
                @else
                    <div class="review-preview-note" role="note">
                        <p><strong>Pratinjau komponen (data demonstrasi).</strong> Belum ada data testimonial di database. Tampilan berikut menggunakan data demonstrasi dan <em>bukan</em> berasal dari pelanggan nyata. Integrasi database testimonial telah disiapkan untuk tahap selanjutnya.</p>
                    </div>
                    <div class="blog-reviews-grid">
                        <x-blog.review-card
                            name="Nama Pengguna (Contoh)"
                            role="Peran / Profesi (Contoh)"
                            content="Contoh isi ulasan: layanan dan informasi yang ditampilkan membantu memahami langkah pengelolaan usaha dengan lebih terarah. (Data demonstrasi, bukan pelanggan nyata.)"
                            :rating="5"
                            :demo="true"
                        />
                        <x-blog.review-card
                            name="Nama Pengguna (Contoh)"
                            role="Peran / Profesi (Contoh)"
                            content="Contoh isi ulasan: artikel dan panduan yang tersedia mudah diikuti dan relevan dengan kebutuhan di lapangan. (Data demonstrasi, bukan pelanggan nyata.)"
                            :rating="5"
                            :demo="true"
                        />
                        <x-blog.review-card
                            name="Nama Pengguna (Contoh)"
                            role="Peran / Profesi (Contoh)"
                            content="Contoh isi ulasan: informasi yang disajikan ringkas dan membantu dalam mengambil keputusan. (Data demonstrasi, bukan pelanggan nyata.)"
                            :rating="4"
                            :demo="true"
                        />
                    </div>
                @endif
            </div>
        </section>
    @else
        {{-- Listing / search / filter / all articles --}}
        <section class="blog-header-section blog-header-section-compact">
            <div class="blog-container-wide">
                <div class="blog-header-inner">
                    <div class="blog-header-copy">
                        <p class="section-label section-label-light">BLOG / ARTIKEL</p>
                        @if (!empty($search))
                            <h1 class="blog-header-title">Hasil Pencarian Artikel</h1>
                            <p class="blog-header-desc">Kata kunci: "<strong>{{ $search }}</strong>"</p>
                        @elseif ($activeCategory)
                            <h1 class="blog-header-title">{{ $activeCategory->name }}</h1>
                            <p class="blog-header-desc">Jelajahi artikel dalam kategori {{ $activeCategory->name }}.</p>
                        @else
                            <h1 class="blog-header-title">Seluruh Artikel FunFarm</h1>
                            <p class="blog-header-desc">Jelajahi informasi pertanian dan peternakan dari FunFarm.</p>
                        @endif
                    </div>

                    <form action="{{ route('blog.search') }}" method="GET" class="blog-header-search" role="search">
                        <label for="q" class="sr-only">Cari artikel</label>
                        <input
                            id="q"
                            name="q"
                            type="search"
                            value="{{ $search ?? '' }}"
                            maxlength="100"
                            placeholder="Cari artikel berdasarkan judul..."
                            class="blog-search-input"
                        >
                        <button type="submit" class="blog-search-button">Cari</button>
                    </form>

                    <nav class="blog-category-nav" aria-label="Navigasi kategori artikel">
                        <a href="{{ route('blog.index', ['show' => 'all']) }}" class="category-chip {{ !$activeCategory ? 'is-active' : '' }}">Semua</a>
                        @foreach ($categories as $cat)
                            <a href="{{ route('blog.category', $cat->slug) }}" class="category-chip {{ $activeCategory && $activeCategory->id === $cat->id ? 'is-active' : '' }}">{{ $cat->name }}</a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </section>

        <section class="blog-latest-section">
            <div class="blog-container-wide">
                <div class="blog-section-top">
                    <div class="min-w-0">
                        @if (!empty($search))
                            <p class="section-label">HASIL PENCARIAN</p>
                            <h2 class="section-title mt-2 break-anywhere">Kata kunci: "{{ $search }}"</h2>
                        @elseif ($activeCategory)
                            <p class="section-label">FILTER KATEGORI</p>
                            <h2 class="section-title mt-2 break-anywhere">{{ $activeCategory->name }}</h2>
                        @else
                            <p class="section-label">DAFTAR ARTIKEL</p>
                            <h2 class="section-title mt-2">Semua artikel</h2>
                        @endif
                        <p class="mt-3 text-sm text-stone-600">Menampilkan {{ $articles->total() }} artikel.</p>
                    </div>
                    @if (!empty($search) || $activeCategory)
                        <a href="{{ route('blog.index') }}" class="blog-view-all-link">Reset filter</a>
                    @endif
                </div>

                @if ($articles->count() > 0)
                    <div class="blog-articles-grid-compact">
                        @foreach ($articles as $article)
                            <x-blog.article-card :article="$article" />
                        @endforeach
                    </div>

                    <div class="blog-pagination mt-10">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="blog-empty-state">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-stone-100 text-stone-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <h3 class="blog-empty-title mt-4">Artikel tidak ditemukan</h3>
                        <p class="blog-empty-text mt-2">
                            @if (!empty($search))
                                Tidak ada artikel yang cocok dengan kata kunci "{{ $search }}". Coba gunakan kata kunci lain.
                            @else
                                Belum ada artikel yang tersedia untuk kategori ini.
                            @endif
                        </p>
                        <a href="{{ route('blog.index') }}" class="mt-6 inline-flex min-h-[44px] items-center justify-center rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Kembali ke beranda blog</a>
                    </div>
                @endif
            </div>
        </section>
    @endif
@endsection
