@extends('blog.layouts.app')

@section('title', $article->title . ' - FunFarm')
@section('meta_description', $article->meta_description_value)
@section('canonical', route('blog.show', $article->slug))
@section('og_type', 'article')
@section('og_title', $article->title)
@section('og_description', $article->meta_description_value)
@section('og_image', $article->thumbnail_url ?: asset('favicon.ico'))

@php
    $gradients = [
        'pertanian' => 'linear-gradient(135deg,#2e9e5b,#177245)',
        'peternakan' => 'linear-gradient(135deg,#d97706,#b45309)',
        'teknologi-pertanian' => 'linear-gradient(135deg,#0284c7,#0369a1)',
        'informasi-umum' => 'linear-gradient(135deg,#7c3aed,#5b21b6)',
    ];
    $gradient = $gradients[$article->category?->slug] ?? 'linear-gradient(135deg,#475569,#1e293b)';
@endphp

@section('content')
    <div class="blog-container-wide blog-page-section">
        <div class="grid gap-8 lg:grid-cols-3">

            {{-- Kolom kiri 2/3: judul, gambar, konten --}}
            <article class="min-w-0 lg:col-span-2">
                <nav class="mb-5 text-sm text-stone-500" aria-label="Breadcrumb">
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

                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                        @if ($article->category)
                            <a href="{{ route('blog.category', $article->category->slug) }}" class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 font-medium text-emerald-800 transition hover:bg-emerald-100">{{ $article->category->name }}</a>
                        @endif
                        <time datetime="{{ $article->published_at?->toDateString() }}" class="text-stone-500">
                            {{ $article->published_at?->translatedFormat('d F Y') }}
                        </time>
                        <span aria-hidden="true" class="text-stone-300">&bull;</span>
                        <span class="text-stone-500">baca {{ $article->reading_time }} menit</span>
                    </div>

                    {{-- 1. Judul --}}
                    <h1 class="blog-article-title mt-4 break-anywhere text-stone-900">{{ $article->title }}</h1>

                    @if ($article->excerpt)
                        <p class="mt-4 leading-relaxed text-stone-500 break-anywhere">{{ $article->excerpt }}</p>
                    @endif

                    {{-- 2. Gambar --}}
                    <figure class="mt-7 overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                        @if ($article->thumbnail_url)
                            <img
                                src="{{ $article->thumbnail_url }}"
                                alt="{{ $article->title }}"
                                loading="lazy"
                                decoding="async"
                                sizes="(min-width: 1024px) 768px, 100vw"
                                class="aspect-[16/9] w-full object-cover"
                            >
                        @else
                            <span class="flex aspect-[16/9] w-full items-center justify-center px-6 text-center text-lg font-semibold tracking-wide text-white" style="background:{{ $gradient }}">
                                {{ $article->category?->name ?? 'FunFarm' }}
                            </span>
                        @endif
                    </figure>

                    {{-- 3. Konten --}}
                    <div class="article-content mt-8 text-stone-700">
                        @if (!empty($article->blocks))
                            @foreach ($article->blocks as $block)
                                @includeIf('blog.blocks.'.$block['type'], ['block' => $block])
                            @endforeach
                        @else
                            {!! $article->safe_content !!}
                        @endif
                    </div>

                    <div class="mt-10 border-t border-stone-200 pt-6">
                        <a href="{{ route('blog.index') }}" class="inline-flex min-h-[44px] items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                            </svg>
                            Kembali ke daftar artikel
                        </a>
                    </div>
                </div>
            </article>

            {{-- Kolom kanan 1/3: artikel lain + kategori --}}
            <aside class="min-w-0 lg:col-span-1">
                <div class="flex flex-col gap-6 lg:sticky lg:top-24">

                    {{-- 4. Artikel lain (terbaru) --}}
                    @if ($latestArticles->isNotEmpty())
                        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" aria-label="Artikel terbaru">
                            <h2 class="flex items-center gap-2 text-base font-bold text-stone-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-amber-500" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M13.5 1.938a7 7 0 1 1-9.006 6.978c1.169-.297 2.25-.886 3.169-1.727a3.995 3.995 0 0 1 1.237 2.332 3.988 3.988 0 0 0 1.925 2.762c.243.144.596-.002.717-.283.34-.792.385-1.717.086-2.553A4.005 4.005 0 0 1 12.636 6.5c.87.01 1.857-.662 2.215-1.706.043-.127.058-.273.043-.42a6.93 6.93 0 0 0-1.395-3.437Z" clip-rule="evenodd" />
                                </svg>
                                Artikel Terbaru
                            </h2>

                            <div class="mt-4 flex flex-col divide-y divide-stone-100">
                                @foreach ($latestArticles as $other)
                                    <a href="{{ route('blog.show', $other->slug) }}" class="group flex items-start gap-3 py-3 first:pt-0 last:pb-0" data-testid="sidebar-article">
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-semibold leading-snug text-stone-800 line-clamp-2 transition group-hover:text-emerald-800">{{ $other->title }}</span>
                                            <span class="mt-1 block text-xs text-stone-500">
                                                {{ $other->published_at?->translatedFormat('d M Y') }} &bull; baca {{ $other->reading_time }} menit
                                            </span>
                                        </span>
                                        @if ($other->thumbnail_url)
                                            <img src="{{ $other->thumbnail_url }}" alt="" loading="lazy" class="h-14 w-14 shrink-0 rounded-lg object-cover">
                                        @else
                                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg text-base font-bold text-white" style="background:{{ $gradients[$other->category?->slug] ?? 'linear-gradient(135deg,#475569,#1e293b)' }}" aria-hidden="true">
                                                {{ strtoupper(substr($other->category?->name ?? 'F', 0, 1)) }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" aria-label="Kategori artikel">
                        <h2 class="text-base font-bold text-stone-900">Kategori</h2>
                        <ul class="mt-3 flex flex-col">
                            @foreach ($categories as $cat)
                                <li>
                                    <a href="{{ route('blog.category', $cat->slug) }}" class="flex min-h-[44px] items-center justify-between gap-3 border-b border-stone-100 py-2 text-sm font-medium last:border-0 {{ $article->category && $article->category->id === $cat->id ? 'text-emerald-800' : 'text-stone-700' }} transition hover:text-emerald-800">
                                        <span>{{ $cat->name }}</span>
                                        <span class="rounded-full bg-stone-100 px-2 py-0.5 text-xs font-semibold text-stone-600">{{ $cat->published_articles_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                </div>
            </aside>
        </div>
    </div>
@endsection
