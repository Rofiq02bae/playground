@extends('blog.layouts.app')

@section('title', $article->title . ' - FunFarm')
@section('meta_description', $article->meta_description_value)
@section('canonical', route('blog.show', $article->slug))
@section('og_type', 'article')
@section('og_title', $article->title)
@section('og_description', $article->meta_description_value)
@section('og_image', $article->thumbnail_url ?: asset('favicon.ico'))

@section('content')
    <article>
        <header class="border-b border-stone-200 bg-white">
            <div class="blog-container blog-page-section max-w-4xl">
                <nav class="mb-6 text-sm text-stone-500" aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <li><a href="{{ route('blog.index') }}" class="inline-flex min-h-[44px] items-center font-medium text-emerald-700 hover:text-emerald-800">Blog</a></li>
                        @if ($article->category)
                            <li aria-hidden="true">/</li>
                            <li><a href="{{ route('blog.category', $article->category->slug) }}" class="inline-flex min-h-[44px] items-center hover:text-stone-700 break-anywhere">{{ $article->category->name }}</a></li>
                        @endif
                        <li aria-hidden="true">/</li>
                        <li class="text-stone-700">Artikel</li>
                    </ol>
                </nav>

                <div class="flex flex-wrap items-center gap-3 text-sm">
                    @if ($article->category)
                        <a href="{{ route('blog.category', $article->category->slug) }}" class="inline-flex min-h-[44px] items-center rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-800 transition hover:bg-emerald-100">{{ $article->category->name }}</a>
                    @endif
                    <time datetime="{{ optional($article->published_at)->toDateString() }}" class="text-stone-500">
                        {{ optional($article->published_at)->translatedFormat('d F Y') ?? '-' }}
                    </time>
                </div>

                <h1 class="blog-article-title mt-4 break-anywhere text-stone-900">{{ $article->title }}</h1>

                @if ($article->excerpt)
                    <p class="mt-5 text-[clamp(1rem,0.6vw+0.9rem,1.125rem)] leading-relaxed text-stone-600 break-anywhere">{{ $article->excerpt }}</p>
                @endif

                @if ($article->thumbnail_url)
                    <figure class="mt-8 overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                        <img
                            src="{{ $article->thumbnail_url }}"
                            alt="{{ $article->title }}"
                            loading="lazy"
                            decoding="async"
                            sizes="(min-width: 1024px) 896px, 100vw"
                            class="aspect-[16/9] w-full object-cover"
                        >
                    </figure>
                @endif
            </div>
        </header>

        <div class="blog-container blog-page-section max-w-4xl">
            <div class="article-prose-wrap">
                <div class="article-content text-stone-700">
                    {!! $article->safe_content !!}
                </div>
            </div>

            <div class="mt-12 border-t border-stone-200 pt-8">
                <a href="{{ route('blog.index') }}" class="inline-flex min-h-[44px] items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke daftar artikel
                </a>
            </div>
        </div>
    </article>

    @if ($relatedArticles->isNotEmpty())
        <section class="border-t border-stone-200 bg-stone-100/70">
            <div class="blog-container blog-page-section">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-wide text-emerald-700">Baca Juga</p>
                        <h2 class="blog-section-title mt-1 text-stone-900">Artikel terkait</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="inline-flex min-h-[44px] items-center text-sm font-medium text-emerald-700 hover:text-emerald-800">Lihat semua</a>
                </div>
                <div class="blog-articles-grid-compact">
                    @foreach ($relatedArticles as $related)
                        <x-blog.article-card :article="$related" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
