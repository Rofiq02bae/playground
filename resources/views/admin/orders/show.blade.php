@extends('admin.layouts.app')

@section('title', 'Pesanan '.$order->code.' - Admin FunFarm')

@section('content')
    <a href="{{ route('admin.orders.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 transition hover:text-emerald-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7" /></svg>
        Semua pesanan
    </a>

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <h1 class="text-xl font-semibold text-stone-900">{{ $order->code }}</h1>
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
                <h2 class="mb-2 font-semibold text-stone-900">Pembeli &amp; Alamat</h2>
                <p class="text-sm text-stone-600">Akun: <span class="font-medium text-stone-800">{{ $order->user?->name }}</span> ({{ $order->user?->email }}) · role {{ $order->user?->roleLabel() }}</p>
                <p class="mt-1.5 text-sm font-medium text-stone-800">Penerima: {{ $order->recipient_name }} · {{ $order->recipient_phone }}</p>
                <p class="text-sm text-stone-600">{{ $order->address_text }}</p>
                @if ($order->notes)<p class="mt-2 text-sm text-stone-500">Catatan pembeli: {{ $order->notes }}</p>@endif
            </section>
        </div>

        <aside class="space-y-5 lg:col-span-2">
            <section class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="mb-3 font-semibold text-stone-900">Pembayaran</h2>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex justify-between"><dt class="text-stone-500">Status Midtrans</dt><dd class="font-medium">{{ $order->payment_status ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Metode</dt><dd class="font-medium">{{ $order->payment_type ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Dibayar pada</dt><dd class="font-medium">{{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-stone-500">Batas bayar</dt><dd class="font-medium">{{ $order->expires_at ? $order->expires_at->format('d M Y H:i') : '—' }}</dd></div>
                </dl>
            </section>

            <section class="rounded-2xl border border-stone-200 bg-white p-5">
                <h2 class="mb-3 font-semibold text-stone-900">Tindakan</h2>
                <div class="space-y-2">
                    @if ($order->status === 'dibayar')
                        <form method="POST" action="{{ route('admin.orders.process', $order) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-700 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Tandai Diproses</button>
                        </form>
                    @endif

                    @if ($order->status === 'diproses' && $order->hasBarang())
                        <form method="POST" action="{{ route('admin.orders.ship', $order) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-700 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Kirim via Biteship (resi otomatis)</button>
                        </form>
                        <form method="POST" action="{{ route('admin.orders.shipManual', $order) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="waybill" required placeholder="No. resi manual" class="min-w-0 flex-1 rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600">
                            <button type="submit" class="shrink-0 rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Kirim Manual</button>
                        </form>
                        @error('waybill')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    @endif

                    @if ($order->status === 'dikirim' || ($order->status === 'diproses' && ! $order->hasBarang()))
                        <form method="POST" action="{{ route('admin.orders.complete', $order) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-700 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Tandai Selesai</button>
                        </form>
                    @endif

                    @if (in_array($order->status, ['menunggu_pembayaran', 'dibayar', 'diproses'], true))
                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini? Stok barang dikembalikan ke katalog.')">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-red-200 py-2.5 text-sm font-medium text-red-700 transition hover:bg-red-50">Batalkan Pesanan</button>
                        </form>
                    @endif

                    @if (in_array($order->status, ['selesai', 'dibatalkan'], true))
                        <p class="text-sm text-stone-500">Pesanan sudah final ({{ $order->statusLabel() }}) — tidak ada tindakan lanjutan.</p>
                    @endif
                </div>

                @if ($order->waybill)
                    <div class="mt-4 rounded-xl bg-stone-50 px-4 py-3 text-sm">
                        <span class="text-stone-500">Kurir:</span> <span class="font-medium">{{ $order->courier_company }} {{ $order->courier_service }}</span><br>
                        <span class="text-stone-500">No. Resi:</span> <span class="font-bold tracking-wide">{{ $order->waybill }}</span>
                        @if ($order->biteship_order_id)<br><span class="text-stone-500">Order Biteship:</span> <span class="font-medium">{{ $order->biteship_order_id }}</span>@endif
                    </div>
                @endif
            </section>
        </aside>
    </div>
@endsection
