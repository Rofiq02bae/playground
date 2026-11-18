@extends('blog.layouts.app')

@section('title', $category->name . ' - Kategori Blog FunFarm')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($category->description ?: ('Artikel kategori ' . $category->name . ' di FunFarm.')), 160, ''))
@section('canonical', route('blog.category', $category->slug))

@section('content')
    <section class="border-b border-stone-200 bg-white">
        <div class="blog-container blog-page-section">
            <nav class="mb-6 text-sm text-stone-500" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <li><a href="{{ route('blog.index') }}" class="inline-flex min-h-[44px] items-center font-medium text-emerald-700 hover:text-emerald-800">Blog</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-stone-700">Kategori</li>
                </ol>
            </nav>
            <div class="max-w-3xl">
                <p class="text-sm font-medium uppercase tracking-wide text-emerald-700">Kategori Artikel</p>
                <h1 class="blog-section-title mt-2 break-anywhere text-stone-900" style="font-size: clamp(1.875rem, 2.5vw + 1rem, 2.5rem);">{{ $category->name }}</h1>
                @if ($category->description)
                    <p class="mt-4 text-base leading-relaxed text-stone-600 break-anywhere">{{ $category->description }}</p>
                @else
                    <p class="mt-4 text-base leading-relaxed text-stone-600">Kumpulan artikel dalam kategori {{ $category->name }}.</p>
                @endif
                <p class="mt-4 text-sm text-stone-500">Menampilkan {{ $articles->total() }} artikel.</p>
            </div>
        </div>
    </section>

    <section class="blog-page-section">
        <div class="blog-container">
            <div class="blog-layout">
                <div class="blog-main">
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
                        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
                            <h3 class="text-lg font-semibold text-stone-900">Belum ada artikel di kategori ini</h3>
                            <p class="mt-2 text-sm text-stone-600">Artikel untuk kategori {{ $category->name }} akan ditampilkan di sini ketika tersedia.</p>
                            <a href="{{ route('blog.index') }}" class="mt-6 inline-flex min-h-[44px] items-center justify-center rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Lihat semua artikel</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
