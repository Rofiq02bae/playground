<article class="card" data-testid="article-card">
    <a href="{{ route('articles.show', $article) }}" aria-label="{{ $article->title }}">
        @if ($article->thumbnail)
            <div class="card-thumb">
                <img src="{{ \Illuminate\Support\Str::startsWith($article->thumbnail, 'http') ? $article->thumbnail : asset('storage/'.$article->thumbnail) }}" alt="{{ $article->title }}">
            </div>
        @else
            <div class="card-thumb thumb-{{ $article->category?->slug ?? 'default' }}">
                <span>{{ $article->category?->name ?? 'FunFarm' }}</span>
            </div>
        @endif
    </a>
    <div class="card-body">
        <div class="card-meta">
            @if ($article->category)
                <span class="badge">{{ $article->category->name }}</span>
            @endif
            <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->translatedFormat('d F Y') }}</time>
            <span>{{ $article->reading_time }} menit baca</span>
        </div>
        <h3><a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h3>
        <p>{{ $article->excerpt }}</p>
        <a class="read-more" href="{{ route('articles.show', $article) }}">Baca selengkapnya &rarr;</a>
    </div>
</article>
