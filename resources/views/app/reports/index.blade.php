@extends('app.layouts.app')

@section('title', 'Laporan Biaya Produksi - FunFarm WebApp')

@section('content')
    <div class="mb-4">
        <h1 class="font-semibold text-stone-900">Laporan Biaya Produksi</h1>
        <p class="text-xs text-stone-500">Biaya yang disetujui perusahaan, hasil panen, dan bagi hasilnya.</p>
    </div>

    <div class="space-y-3">
        @forelse ($summaries as $row)
            <a href="{{ route('app.reports.show', $row['project']) }}" class="block rounded-2xl border border-stone-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-medium text-stone-900">{{ $row['project']->name }}</p>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $row['project']->statusLabel() }}</span>
                </div>
                <dl class="mt-3 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl bg-stone-50 px-2 py-2">
                        <dt class="text-[11px] uppercase tracking-wide text-stone-400">Biaya</dt>
                        <dd class="text-sm font-semibold text-stone-800">Rp{{ number_format($row['approvedTotal'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-2 py-2">
                        <dt class="text-[11px] uppercase tracking-wide text-stone-400">Hasil Panen</dt>
                        <dd class="text-sm font-semibold text-stone-800">Rp{{ number_format($row['harvestRevenue'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="rounded-xl bg-emerald-50 px-2 py-2">
                        <dt class="text-[11px] uppercase tracking-wide text-emerald-600">Laba Bersih</dt>
                        <dd class="text-sm font-bold {{ $row['netProfit'] < 0 ? 'text-red-700' : 'text-emerald-900' }}">Rp{{ number_format($row['netProfit'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </a>
        @empty
            <p class="rounded-2xl border border-stone-200 bg-white px-4 py-6 text-center text-sm text-stone-400">Belum ada proyek untuk ditampilkan.</p>
        @endforelse
    </div>
@endsection
