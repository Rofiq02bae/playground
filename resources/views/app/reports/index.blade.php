@extends('app.layouts.app')

@section('title', 'Laporan Biaya Produksi - FunFarm WebApp')

@section('content')
    <h1 class="text-xl font-semibold text-stone-900">Laporan Biaya Produksi</h1>
    <p class="mb-5 mt-1 text-sm text-stone-500">Biaya yang disetujui perusahaan, hasil panen, dan bagi hasilnya.</p>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($summaries as $row)
            <a href="{{ route('app.reports.show', $row['project']) }}" class="flex flex-col rounded-2xl border border-stone-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 active:bg-stone-50">
                <div class="flex items-start justify-between gap-2">
                    <p class="line-clamp-2 font-semibold leading-snug text-stone-900">{{ $row['project']->name }}</p>
                    @include('app.partials.status-chip', ['project' => $row['project']])
                </div>
                <p class="mt-1 truncate text-xs text-stone-500">{{ $row['project']->field_name }} &middot; {{ $row['project']->crop_type }}</p>

                <dl class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 text-sm">
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="text-xs text-stone-500">Biaya</dt>
                        <dd class="font-medium text-stone-800">Rp{{ number_format($row['approvedTotal'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="text-xs text-stone-500">Hasil Panen</dt>
                        <dd class="font-medium text-stone-800">Rp{{ number_format($row['harvestRevenue'], 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="text-xs font-medium text-emerald-700">Laba Bersih</dt>
                        <dd class="font-bold {{ $row['netProfit'] < 0 ? 'text-red-700' : 'text-emerald-900' }}">Rp{{ number_format($row['netProfit'], 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <p class="mt-3 flex items-center gap-1 text-xs font-semibold text-emerald-800">Lihat laporan
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6" /></svg>
                </p>
            </a>
        @empty
            <div class="rounded-2xl border border-stone-200 bg-white px-6 py-10 text-center shadow-sm sm:col-span-2 lg:col-span-3">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 20v-6M12 20V6M17 20v-9" /><path d="M3 20h18" /></svg>
                </span>
                <p class="mt-3 font-semibold text-stone-900">Belum ada laporan</p>
                <p class="mx-auto mt-1 max-w-xs text-sm leading-relaxed text-stone-500">Belum ada proyek untuk ditampilkan. Laporan muncul setelah proyek dibuatkan perusahaan.</p>
            </div>
        @endforelse
    </div>
@endsection
