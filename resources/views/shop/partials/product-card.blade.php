{{-- Kartu produk di katalog. Butuh: $product --}}
<a href="{{ route('shop.show', $product->slug) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:border-emerald-300 hover:shadow-md">
    <div class="relative aspect-[4/3] bg-emerald-50">
        @if ($product->photo_url)
            <img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <span class="flex h-full w-full items-center justify-center text-emerald-700">
                @if ($product->category === 'peternakan')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21c-4 0-6.5-2.8-6.5-6.5 0-3 2-6.8 6.5-9.5 4.5 2.7 6.5 6.5 6.5 9.5C18.5 18.2 16 21 12 21z" /></svg>
                @elseif ($product->isJasa())
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3" /><path d="M12 2.5v3M12 18.5v3M2.5 12h3M18.5 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2" /></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21v-8" /><path d="M12 13c0-3.5-2.5-5.5-6.5-5.5 0 4 2 6.5 6.5 5.5zM12 10.5c0-3 2-4.8 5.5-4.8 0 3.4-1.8 5.3-5.5 4.8z" /></svg>
                @endif
            </span>
        @endif
        <span class="absolute left-2.5 top-2.5 rounded-full {{ $product->isJasa() ? 'bg-sky-100 text-sky-800' : 'bg-emerald-700 text-white' }} px-2.5 py-1 text-[11px] font-semibold">{{ $product->typeLabel() }}</span>
        @if ($product->isOutOfStock())
            <span class="absolute right-2.5 top-2.5 rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-semibold text-red-700">Stok Habis</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col gap-1 p-3.5">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-stone-400">{{ $product->categoryLabel() }}</p>
        <h3 class="font-semibold leading-snug text-stone-900 group-hover:text-emerald-800">{{ $product->name }}</h3>
        <p class="mt-auto pt-1.5 text-[15px] font-bold text-emerald-800">Rp{{ number_format($product->price, 0, ',', '.') }}<span class="text-xs font-normal text-stone-400"> /{{ $product->unit }}</span></p>
        <p class="text-xs text-stone-400">{{ $product->isJasa() ? 'Layanan — jadwal setelah pemesanan' : ($product->isOutOfStock() ? 'Stok habis' : 'Stok: '.number_format($product->stock ?? 0, 0, ',', '.').' '.$product->unit) }}</p>
    </div>
</a>
