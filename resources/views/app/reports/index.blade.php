@extends('app.layouts.app')

@section('title', 'Laporan Biaya Produksi - FunFarm WebApp')

@section('content')
    <div class="mb-4">
        <h1 class="font-semibold text-stone-900">Laporan Biaya Produksi</h1>
        <p class="text-xs text-stone-500">Biaya yang disetujui perusahaan, hasil panen, dan bagi hasilnya.</p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($summaries as $row)
            <a href="{{ route('app.reports.show', $row['project']) }}" class="flex flex-col rounded-2xl border border-stone-200 bg-white p-4 transition hover:border-emerald-300 hover:shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <p class="line-clamp-2 font-medium leading-snug text-stone-900">{{ $row['project']->name }}</p>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">{{ $row['project']->statusLabel() }}</span>
                </div>
                <p class="mt-1 truncate text-xs text-stone-400">{{ $row['project']->field_name }} &middot; {{ $row['project']->crop_type }}</p>

                <dl class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 text-sm">
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="text-xs text-stone-400">Biaya</dt>
                        <dd class="font-medium text-stone-800">Rp{{ number_format($row['approvedTotal'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="text-xs text-stone-400">Hasil Panen</dt>
                        <dd class="font-medium text-stone-800">Rp{{ number_format($row['harvestRevenue'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="text-xs font-medium text-emerald-700">Laba Bersih</dt>
                        <dd class="font-bold {{ $row['netProfit'] < 0 ? 'text-red-700' : 'text-emerald-900' }}">Rp{{ number_format($row['netProfit'], 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <p class="mt-3 text-xs font-medium text-emerald-800">Lihat laporan &rarr;</p>
            </a>
        @empty
            <p class="rounded-2xl border border-stone-200 bg-white px-4 py-6 text-center text-sm text-stone-400 sm:col-span-2 lg:col-span-3">Belum ada proyek untuk ditampilkan.</p>
        @endforelse
    </div>
@endsection
