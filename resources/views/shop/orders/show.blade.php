@extends('shop.layouts.app')

@section('title', 'Pesanan '.$order->code.' - Produk FunFarm')

@section('content')
    <a href="{{ route('shop.orders.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 transition hover:text-emerald-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7" /></svg>
        Semua pesanan saya
    </a>

    <div class="mb-5 flex flex-wrap items-center gap-3">
        <h1 class="text-xl font-bold text-stone-900 sm:text-2xl">{{ $order->code }}</h1>
        @include('shop.partials.order-status-chip', ['status' => $order->status])
        <span class="text-sm text-stone-400">{{ $order->created_at->format('d M Y H:i') }} WIB</span>
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <div class="space-y-5 lg:col-span-3">
            <section class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="mb-3 font-semibold text-stone-900">Item Pesanan</h2>
                <ul class="divide-y divide-stone-100">
                    @foreach ($order->items as $item)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div>
                                <p class="font-medium text-stone-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-stone-400">{{ $item->product_type === 'jasa' ? 'Jasa' : 'Barang' }} · Rp{{ number_format($item->price, 0, ',', '.') }}/{{ $item->unit }} × {{ number_format($item->quantity, 0, ',', '.') }}</p>
                            </div>
                            <p class="font-semibold text-stone-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 text-sm">
                    <div class="flex justify-between"><span class="text-stone-500">Subtotal</span><span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-stone-500">Ongkos kirim{{ $order->courier_company ? ' ('.$order->courier_company.' '.$order->courier_service.')' : '' }}</span><span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between border-t border-stone-100 pt-2 text-base font-bold text-stone-900"><span>Total</span><span>Rp{{ number_format($order->total, 0, ',', '.') }}</span></div>
                </div>
            </section>

            <section class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="mb-2 font-semibold text-stone-900">Alamat Tujuan</h2>
                <p class="text-sm font-medium text-stone-800">{{ $order->recipient_name }} · {{ $order->recipient_phone }}</p>
                <p class="text-sm text-stone-600">{{ $order->address_text }}</p>
                @if ($order->notes)<p class="mt-2 text-sm text-stone-500">Catatan: {{ $order->notes }}</p>@endif
                @if ($order->waybill)
                    <div class="mt-3 rounded-xl bg-stone-50 px-4 py-3 text-sm">
                        <span class="text-stone-500">Kurir:</span> <span class="font-medium">{{ $order->courier_company }} {{ $order->courier_service }}</span><br>
                        <span class="text-stone-500">No. Resi:</span> <span class="font-bold tracking-wide">{{ $order->waybill }}</span>
                    </div>
                @endif
            </section>

            @if ($order->waybill)
                @include('partials.tracking-box', ['trackingUrl' => route('shop.orders.tracking', $order->code)])
            @endif
        </div>

        <aside class="lg:col-span-2">
            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="mb-3 font-semibold text-stone-900">Pembayaran</h2>
                @if ($order->status === 'menunggu_pembayaran')
                    <p class="text-sm text-stone-600">Selesaikan pembayaran sebelum <strong>{{ optional($order->expires_at)->format('d M Y H:i') }} WIB</strong>. Lewat dari itu pesanan otomatis batal dan stok kembali.</p>
                    @if ($order->payment_status)<p class="mt-1.5 text-xs text-stone-400">Status Midtrans terakhir: {{ $order->payment_status }}{{ $order->payment_type ? ' · '.$order->payment_type : '' }}</p>@endif
                    <div class="mt-4 space-y-2">
                        @if ($midtransConfigured && $order->snap_token)
                            <button type="button" id="btn-pay" class="w-full rounded-xl bg-emerald-700 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">Bayar Sekarang</button>
                        @elseif (! $midtransConfigured)
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Pembayaran online belum dikonfigurasi perusahaan. Pesananmu sudah tersimpan — silakan hubungi perusahaan untuk penyelesaian pembayaran.</div>
                        @endif
                        <form method="POST" action="{{ route('shop.orders.check', $order->code) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-stone-300 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Cek Status Pembayaran</button>
                        </form>
                        <form method="POST" action="{{ route('shop.orders.cancel', $order->code) }}" onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                            @csrf
                            <button type="submit" class="w-full rounded-xl py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">Batalkan Pesanan</button>
                        </form>
                    </div>
                @elseif ($order->status === 'dibatalkan')
                    <p class="text-sm text-stone-600">Pesanan ini sudah dibatalkan{{ $order->payment_status ? ' (Midtrans: '.$order->payment_status.')' : '' }}. Stok barang telah dikembalikan ke katalog.</p>
                @else
                    <p class="text-sm text-stone-600">Pembayaran diterima{{ $order->paid_at ? ' pada '.$order->paid_at->format('d M Y H:i').' WIB' : '' }}{{ $order->payment_type ? ' via '.$order->payment_type : '' }}.</p>
                    <p class="mt-2 text-sm text-stone-600">
                        @switch($order->status)
                            @case('dibayar') Pesananmu sedang disiapkan perusahaan. @break
                            @case('diproses') Pesananmu sedang diproses{{ $order->hasBarang() ? ' dan akan segera dikirim' : '' }}. @break
                            @case('dikirim') Paketmu sedang dalam perjalanan. Lacak dengan nomor resi di samping. @break
                            @case('selesai') Pesanan selesai. Terima kasih sudah berbelanja di FunFarm! @break
                        @endswitch
                    </p>
                @endif
            </div>
        </aside>
    </div>

    @if ($order->status === 'menunggu_pembayaran' && $midtransConfigured && $order->snap_token)
        <form id="sync-form" method="POST" action="{{ route('shop.orders.check', $order->code) }}" class="hidden">@csrf</form>
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
        <script>
            document.getElementById('btn-pay').addEventListener('click', function () {
                window.snap.pay(@json($order->snap_token), {
                    onSuccess: function () { document.getElementById('sync-form').submit(); },
                    onPending: function () { document.getElementById('sync-form').submit(); },
                    onError: function () { document.getElementById('sync-form').submit(); },
                    onClose: function () { /* pembeli menutup popup — status tetap bisa dicek manual */ }
                });
            });
        </script>
    @endif
@endsection
