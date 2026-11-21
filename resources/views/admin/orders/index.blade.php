@extends('admin.layouts.app')

@section('title', 'Pesanan Marketplace - Admin FunFarm')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">Pesanan Marketplace</h1>
        <p class="text-sm text-stone-500">Pesanan dari toko publik. Badge di menu menunjukkan pesanan yang sudah dibayar dan menunggu diproses.</p>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama penerima..." class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 sm:w-64">
        <select name="status" class="rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            <option value="">Semua status</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Pesanan</th>
                    <th class="px-4 py-3 font-semibold">Pembeli</th>
                    <th class="px-4 py-3 font-semibold">Item</th>
                    <th class="px-4 py-3 font-semibold">Total</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-stone-900">{{ $order->code }}</p>
                            <p class="text-xs text-stone-400">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-stone-800">{{ $order->recipient_name }}</p>
                            <p class="text-xs text-stone-400">{{ $order->user?->email }} · {{ $order->recipient_phone }}</p>
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $order->items->map(fn ($i) => $i->product_name.' × '.number_format($i->quantity, 0, ',', '.'))->join(', ') }}</td>
                        <td class="px-4 py-3 font-semibold text-stone-900">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">@include('shop.partials.order-status-chip', ['status' => $order->status])</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-500">Belum ada pesanan untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $orders->links() }}</div>
@endsection
