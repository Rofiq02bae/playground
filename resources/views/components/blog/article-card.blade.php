@props(['article'])

@php
    $gradients = [
        'pertanian' => 'linear-gradient(135deg,#2e9e5b,#177245)',
        'peternakan' => 'linear-gradient(135deg,#d97706,#b45309)',
        'teknologi-pertanian' => 'linear-gradient(135deg,#0284c7,#0369a1)',
        'informasi-umum' => 'linear-gradient(135deg,#7c3aed,#5b21b6)',
    ];
    $gradient = $gradients[$article->category?->slug] ?? 'linear-gradient(135deg,#475569,#1e293b)';
@endphp

<article class="flex h-full flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:shadow-md" data-testid="article-card">
    <a href="{{ route('blog.show', $article->slug) }}" class="block" aria-label="{{ $article->title }}">
        @if ($article->thumbnail_url)
            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" loading="lazy" decoding="async" class="aspect-[16/9] w-full object-cover">
        @else
            <span class="flex aspect-[16/9] w-full items-center justify-center px-4 text-center text-sm font-semibold tracking-wide text-white" style="background:{{ $gradient }}">
                {{ $article->category?->name ?? 'FunFarm' }}
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-stone-500">
            @if ($article->category)
                <a href="{{ route('blog.category', $article->category->slug) }}" class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-800 transition hover:bg-emerald-100">{{ $article->category->name }}</a>
            @endif
            <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->translatedFormat('d F Y') }}</time>
        </div>

        <h3 class="text-base font-semibold leading-snug text-stone-900">
            <a href="{{ route('blog.show', $article->slug) }}" class="transition hover:text-emerald-800">{{ $article->title }}</a>
        </h3>

        @if ($article->excerpt)
            <p class="text-sm leading-relaxed text-stone-600 line-clamp-3">{{ $article->excerpt }}</p>
        @endif

        <a href="{{ route('blog.show', $article->slug) }}" class="mt-auto inline-flex items-center gap-1 pt-1 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
            Baca selengkapnya
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
        </a>
    </div>
</article>
