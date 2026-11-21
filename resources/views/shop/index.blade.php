@extends('shop.layouts.app')

@section('title', 'Produk FunFarm — Hasil Panen, Jasa & Alat Pertanian/Peternakan')
@section('meta_description', 'Belanja hasil panen segar, jasa semprot drone & mesin tanam, dan alat penetas telur langsung dari FunFarm.')

@section('content')
    <section class="mb-6 overflow-hidden rounded-3xl bg-emerald-800 text-white">
        <div class="px-5 py-7 sm:px-8 sm:py-9">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald-200">Marketplace FunFarm</p>
            <h1 class="mt-1.5 max-w-xl text-2xl font-bold leading-tight sm:text-3xl">Dari sawah &amp; peternakan kami, langsung ke tanganmu.</h1>
            <p class="mt-2 max-w-lg text-sm text-emerald-100">Hasil panen dari proyek yang tercatat di FunFarm, jasa pertanian profesional, dan alat peternakan — semua dikelola langsung oleh perusahaan.</p>
        </div>
    </section>

    <div class="mb-5 flex flex-wrap items-center gap-2" role="tablist" aria-label="Filter kategori">
        <a href="{{ route('shop.index', array_filter(['q' => request('q')])) }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ !request('kategori') ? 'bg-emerald-700 text-white' : 'border border-stone-300 bg-white text-stone-600 hover:border-emerald-400 hover:text-emerald-800' }}">Semua</a>
        @foreach ($categories as $key => $label)
            <a href="{{ route('shop.index', array_filter(['kategori' => $key, 'q' => request('q')])) }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request('kategori') === $key ? 'bg-emerald-700 text-white' : 'border border-stone-300 bg-white text-stone-600 hover:border-emerald-400 hover:text-emerald-800' }}">{{ $label }}</a>
        @endforeach
        @if (request('q'))
            <span class="ml-1 text-sm text-stone-500">Hasil untuk "<span class="font-medium text-stone-800">{{ request('q') }}</span>" · <a href="{{ route('shop.index', array_filter(['kategori' => request('kategori')])) }}" class="font-medium text-emerald-800 underline">hapus pencarian</a></span>
        @endif
    </div>

    @if ($products->count())
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach ($products as $product)
                @include('shop.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @else
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-5 py-12 text-center">
            <p class="font-semibold text-stone-800">Produk tidak ditemukan</p>
            <p class="mx-auto mt-1 max-w-sm text-sm text-stone-500">Belum ada produk aktif untuk filter ini. Coba kategori lain atau kata kunci berbeda.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Lihat Semua Produk</a>
        </div>
    @endif
@endsection
