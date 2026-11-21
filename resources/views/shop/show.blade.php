@extends('shop.layouts.app')

@section('title', $product->name.' - Produk FunFarm')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 150) ?: $product->name.' di toko FunFarm.')

@section('content')
    <a href="{{ route('shop.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 transition hover:text-emerald-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7" /></svg>
        Kembali ke katalog
    </a>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-3xl border border-stone-200 bg-white">
            @if ($product->photo_url)
                <img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }}" class="aspect-[4/3] w-full object-cover">
            @else
                <div class="flex aspect-[4/3] w-full items-center justify-center bg-emerald-50 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21v-8" /><path d="M12 13c0-3.5-2.5-5.5-6.5-5.5 0 4 2 6.5 6.5 5.5zM12 10.5c0-3 2-4.8 5.5-4.8 0 3.4-1.8 5.3-5.5 4.8z" /></svg>
                </div>
            @endif
        </div>

        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-stone-200 px-3 py-1 text-xs font-semibold text-stone-600">{{ $product->categoryLabel() }}</span>
                <span class="rounded-full {{ $product->isJasa() ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800' }} px-3 py-1 text-xs font-semibold">{{ $product->typeLabel() }}</span>
                @if ($product->harvest_project_id)
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Hasil Panen FunFarm</span>
                @endif
            </div>

            <h1 class="mt-3 text-2xl font-bold leading-tight text-stone-900 sm:text-3xl">{{ $product->name }}</h1>

            <p class="mt-3 text-3xl font-bold text-emerald-800">Rp{{ number_format($product->price, 0, ',', '.') }}<span class="text-base font-normal text-stone-400"> /{{ $product->unit }}</span></p>

            <p class="mt-2 text-sm {{ $product->isOutOfStock() ? 'font-semibold text-red-600' : 'text-stone-500' }}">
                @if ($product->isJasa())
                    Layanan tersedia — jadwal pengerjaan diatur setelah pemesanan dikonfirmasi.
                @elseif ($product->isOutOfStock())
                    Stok sedang habis.
                @else
                    Stok tersedia: {{ number_format($product->stock ?? 0, 0, ',', '.') }} {{ $product->unit }}
                @endif
            </p>

            @if ($product->description)
                <div class="mt-5 border-t border-stone-200 pt-5">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-stone-500">Deskripsi</h2>
                    <p class="whitespace-pre-line leading-relaxed text-stone-700">{{ $product->description }}</p>
                </div>
            @endif

            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900">
                Keranjang &amp; pembayaran online (Midtrans) dibuka pada tahap berikutnya. Untuk sekarang katalog ini bisa dieksplorasi publik — checkout akan meminta login agar data pengirimanmu lengkap.
            </div>
        </div>
    </div>

    @if ($related->count())
        <h2 class="mb-3 mt-10 text-lg font-semibold text-stone-900">Produk {{ $product->categoryLabel() }} lainnya</h2>
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach ($related as $rel)
                @include('shop.partials.product-card', ['product' => $rel])
            @endforeach
        </div>
    @endif
@endsection
