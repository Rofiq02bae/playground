@extends('layouts.app')

@section('title', 'Blog FunFarm - Artikel Pertanian, Peternakan, dan Teknologi')

@section('content')
    <section class="hero">
        <h1>Blog FunFarm</h1>
        <p>Artikel seputar pertanian, peternakan, dan teknologi pertanian untuk membantu petani bekerja lebih terstruktur dan efisien.</p>
    </section>

    <div class="toolbar">
        <form class="search" method="GET" action="{{ route('articles.index') }}" role="search">
            @if ($activeCategory)
                <input type="hidden" name="kategori" value="{{ $activeCategory }}">
            @endif
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari artikel..." aria-label="Cari artikel">
            <button type="submit">Cari</button>
        </form>
        <span>{{ $articles->count() }} artikel diterbitkan</span>
    </div>

    <nav class="pills" aria-label="Filter kategori">
        <a class="pill {{ $activeCategory ? '' : 'active' }}" href="{{ route('articles.index', array_filter(['q' => $search])) }}">Semua</a>
        @foreach ($categories as $category)
            <a class="pill {{ $activeCategory === $category->slug ? 'active' : '' }}"
               href="{{ route('articles.index', array_filter(['kategori' => $category->slug, 'q' => $search])) }}">
                {{ $category->name }} <span class="count">({{ $category->published_articles_count }})</span>
            </a>
        @endforeach
    </nav>

    @if ($articles->isNotEmpty())
        <section class="article-grid" data-testid="article-grid">
            @foreach ($articles as $article)
                @include('articles._card', ['article' => $article])
            @endforeach
        </section>
    @else
        <div class="empty-state" data-testid="empty-state">
            <strong>Belum ada artikel yang ditemukan</strong>
            Coba ubah kata kunci pencarian atau pilih kategori lain.
        </div>
    @endif
@endsection
