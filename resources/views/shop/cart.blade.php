@extends('shop.layouts.app')

@section('title', 'Keranjang Belanja - Produk FunFarm')

@section('content')
    <h1 class="mb-5 text-xl font-bold text-stone-900 sm:text-2xl">Keranjang Belanja</h1>

    @if ($items->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-5 py-12 text-center">
            <p class="font-semibold text-stone-800">Keranjangmu masih kosong</p>
            <p class="mx-auto mt-1 max-w-sm text-sm text-stone-500">Jelajahi katalog dan tambahkan hasil panen, jasa, atau alat yang kamu butuhkan.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Lihat Katalog</a>
        </div>
    @else
        <form method="POST" action="{{ route('shop.cart.update') }}">
            @csrf
            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                @foreach ($items as $item)
                    @php($p = $item['product'])
                    <div class="flex gap-3 border-b border-stone-100 p-4 last:border-0 sm:items-center">
                        @if ($p->photo_url)
                            <img src="{{ $p->photo_url }}" alt="" class="h-16 w-16 shrink-0 rounded-xl border border-stone-200 object-cover">
                        @else
                            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-emerald-50 font-semibold text-emerald-700">{{ strtoupper(substr($p->name, 0, 1)) }}</span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('shop.show', $p->slug) }}" class="font-semibold text-stone-900 hover:text-emerald-800">{{ $p->name }}</a>
                            <p class="text-xs text-stone-400">{{ $p->typeLabel() }} · Rp{{ number_format($p->price, 0, ',', '.') }}/{{ $p->unit }}@if ($p->isBarang()) · Stok {{ number_format($p->stock ?? 0, 0, ',', '.') }} @endif</p>
                            <div class="mt-2 flex items-center gap-2">
                                <label class="text-xs text-stone-500">Jumlah ({{ $p->unit }})</label>
                                <input type="number" name="quantity[{{ $p->id }}]" value="{{ $item['quantity'] }}" min="0" @if ($p->isBarang() && $p->stock) max="{{ $p->stock }}" @endif class="w-24 rounded-lg border border-stone-300 px-2.5 py-1.5 text-sm outline-none focus:border-emerald-600">
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-stone-900">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            <button type="submit" form="remove-{{ $p->id }}" class="mt-1 text-xs font-medium text-red-600 hover:underline">Hapus</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <button type="submit" class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Perbarui Jumlah</button>
                <p class="text-xs text-stone-400">Isi jumlah 0 lalu perbarui untuk mengeluarkan item.</p>
            </div>
        </form>

        @foreach ($items as $item)
            <form id="remove-{{ $item['product']->id }}" method="POST" action="{{ route('shop.cart.remove', $item['product']) }}" class="hidden">@csrf</form>
        @endforeach

        <div class="mt-5 rounded-2xl border border-stone-200 bg-white p-5">
            <div class="flex items-center justify-between text-sm">
                <span class="text-stone-500">Subtotal belanja</span>
                <span class="font-semibold text-stone-900">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <p class="mt-1.5 text-xs text-stone-400">{{ $hasBarang ? 'Ongkos kirim dihitung saat checkout dari pilihan kurir Biteship (hanya untuk barang; jasa tidak dikirim).' : 'Keranjangmu berisi jasa saja — tidak ada ongkos kirim.' }}</p>
            @auth
                @if (auth()->user()->isAdmin())
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Kamu masuk sebagai admin — admin tidak dapat berbelanja. Keluar dan masuk sebagai pembeli untuk checkout.</div>
                @else
                    <a href="{{ route('shop.checkout.index') }}" class="mt-4 block rounded-xl bg-emerald-700 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-800">Checkout Sekarang</a>
                @endif
            @else
                <a href="{{ route('shop.checkout.index') }}" class="mt-4 block rounded-xl bg-emerald-700 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-800">Checkout — Masuk Dulu</a>
                <p class="mt-2 text-center text-xs text-stone-400">Kamu akan diminta masuk/daftar; keranjangmu tidak akan hilang.</p>
            @endauth
        </div>
    @endif
@endsection
