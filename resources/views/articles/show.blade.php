@extends('layouts.app')

@section('title', $article->title.' - Blog FunFarm')
@section('meta_description', $article->excerpt)

@section('content')
    <p><a href="{{ route('articles.index') }}">&larr; Kembali ke daftar artikel</a></p>

    <article class="article-full">
        <div class="card-meta">
            @if ($article->category)
                <a class="badge" href="{{ route('articles.index', ['kategori' => $article->category->slug]) }}">{{ $article->category->name }}</a>
            @endif
            <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->translatedFormat('d F Y') }}</time>
            <span>{{ $article->reading_time }} menit baca</span>
        </div>

        <h1>{{ $article->title }}</h1>
        <p style="color:var(--muted); font-size:1.05rem;">{{ $article->excerpt }}</p>

        @if ($article->thumbnail)
            <div class="card-thumb" style="border-radius:.6rem; margin:1.25rem 0;">
                <img src="{{ \Illuminate\Support\Str::startsWith($article->thumbnail, 'http') ? $article->thumbnail : asset('storage/'.$article->thumbnail) }}" alt="{{ $article->title }}">
            </div>
        @endif

        <div class="content">
            @foreach (preg_split('/\R{2,}/', trim($article->content)) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </article>

    @if ($related->isNotEmpty())
        <h2 class="section-title">Artikel terkait</h2>
        <section class="article-grid" data-testid="related-grid">
            @foreach ($related as $item)
                @include('articles._card', ['article' => $item])
            @endforeach
        </section>
    @endif
@endsection
