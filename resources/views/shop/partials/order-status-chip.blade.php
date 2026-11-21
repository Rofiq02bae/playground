{{-- Chip status pesanan. Butuh: $status --}}
@php
    $chip = match ($status) {
        'menunggu_pembayaran' => 'bg-amber-100 text-amber-800',
        'dibayar' => 'bg-sky-100 text-sky-800',
        'diproses' => 'bg-violet-100 text-violet-800',
        'dikirim' => 'bg-indigo-100 text-indigo-800',
        'selesai' => 'bg-emerald-100 text-emerald-800',
        'dibatalkan' => 'bg-red-100 text-red-700',
        default => 'bg-stone-200 text-stone-600',
    };
@endphp
<span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $chip }}">{{ \App\Models\Order::STATUSES[$status] ?? ucfirst($status) }}</span>
