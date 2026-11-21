@extends('admin.layouts.app')

@section('title', 'Produk Marketplace - Admin FunFarm')

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">Produk Marketplace</h1>
            <p class="text-sm text-stone-500">Katalog yang tampil di toko publik (tombol Produk di blog). Kategori: Pertanian &amp; Peternakan.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="ml-auto rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">+ Tambah Produk</a>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..." class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 sm:w-64">
        <select name="kategori" class="rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            <option value="">Semua kategori</option>
            @foreach ($categories as $key => $label)
                <option value="{{ $key }}" @selected(request('kategori') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Produk</th>
                    <th class="px-4 py-3 font-semibold">Kategori / Tipe</th>
                    <th class="px-4 py-3 font-semibold">Harga</th>
                    <th class="px-4 py-3 font-semibold">Stok</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($product->photo_url)
                                    <img src="{{ $product->photo_url }}" alt="" class="h-11 w-11 shrink-0 rounded-lg border border-stone-200 object-cover">
                                @else
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50 font-semibold text-emerald-700">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <p class="font-medium text-stone-900">{{ $product->name }}</p>
                                    <p class="text-xs text-stone-400">/produk/{{ $product->slug }}{{ $product->harvest_project_id ? ' · dari panen proyek #'.$product->harvest_project_id : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $product->categoryLabel() }}<br><span class="text-xs text-stone-400">{{ $product->typeLabel() }}</span></td>
                        <td class="px-4 py-3 font-medium text-stone-900">Rp{{ number_format($product->price, 0, ',', '.') }}<span class="text-xs font-normal text-stone-400"> /{{ $product->unit }}</span></td>
                        <td class="px-4 py-3 text-stone-600">{{ $product->isJasa() ? '— (jasa)' : number_format($product->stock ?? 0, 0, ',', '.').' '.$product->unit }}</td>
                        <td class="px-4 py-3">
                            @if ($product->status === 'aktif')<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                            @elseif ($product->status === 'draft')<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Draft</span>
                            @else<span class="rounded-full bg-stone-200 px-2.5 py-1 text-xs font-semibold text-stone-600">Arsip</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Ubah</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini dari marketplace?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-500">Belum ada produk. Tambahkan produk pertama lewat tombol di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $products->links() }}</div>
@endsection
