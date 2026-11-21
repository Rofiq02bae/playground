@extends('shop.layouts.app')

@section('title', 'Pesanan Saya - Produk FunFarm')

@section('content')
    <h1 class="mb-5 text-xl font-bold text-stone-900 sm:text-2xl">Pesanan Saya</h1>

    @if ($orders->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-5 py-12 text-center">
            <p class="font-semibold text-stone-800">Belum ada pesanan</p>
            <p class="mx-auto mt-1 max-w-sm text-sm text-stone-500">Pesanan yang kamu buat akan tampil di sini.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Mulai Belanja</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                <a href="{{ route('shop.orders.show', $order->code) }}" class="block rounded-2xl border border-stone-200 bg-white p-4 transition hover:border-emerald-300 hover:shadow-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-stone-900">{{ $order->code }}</span>
                        @include('shop.partials.order-status-chip', ['status' => $order->status])
                        <span class="ml-auto text-xs text-stone-400">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <p class="mt-1.5 text-sm text-stone-500">{{ $order->items->map(fn ($i) => $i->product_name.' × '.number_format($i->quantity, 0, ',', '.'))->join(', ') }}</p>
                    <p class="mt-1 text-sm">Total: <span class="font-bold text-emerald-800">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        @if ($order->status === 'menunggu_pembayaran')<span class="text-xs text-red-600">· bayar sebelum {{ optional($order->expires_at)->format('d M Y H:i') }}</span>@endif
                        @if ($order->waybill)<span class="text-xs text-stone-500">· Resi: {{ $order->waybill }}</span>@endif
                    </p>
                </a>
            @endforeach
        </div>
        <div class="mt-5">{{ $orders->links() }}</div>
    @endif
@endsection
