@extends('admin.layouts.app')

@section('title', 'Laporan Biaya Produksi - Admin FunFarm')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">Laporan Biaya Produksi</h1>
        <p class="text-sm text-stone-500">Rekap semua proyek — hanya pencatatan yang disetujui yang dihitung. Klik proyek untuk laporan lengkap siap cetak.</p>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Proyek</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Biaya Produksi</th>
                    <th class="px-4 py-3 text-right font-semibold">Hasil Panen</th>
                    <th class="px-4 py-3 text-right font-semibold">Laba Bersih</th>
                    <th class="px-4 py-3 text-right font-semibold">Bagian Perusahaan</th>
                    <th class="px-4 py-3 text-right font-semibold">Bagian Pemilik</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summaries as $row)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3"><span class="font-medium text-stone-900">{{ $row['project']->name }}</span><br><span class="text-xs text-stone-400">{{ $row['project']->owner->name }} &middot; {{ $row['project']->field_name }}</span></td>
                        <td class="px-4 py-3 text-stone-600">{{ $row['project']->statusLabel() }}</td>
                        <td class="px-4 py-3 text-right text-stone-700">Rp{{ number_format($row['approvedTotal'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-stone-700">Rp{{ number_format($row['harvestRevenue'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium {{ $row['netProfit'] < 0 ? 'text-red-700' : 'text-stone-900' }}">Rp{{ number_format($row['netProfit'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-stone-700">Rp{{ number_format($row['companyShare'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-stone-700">Rp{{ number_format($row['ownerShare'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.reports.show', $row['project']) }}" class="font-medium text-emerald-800 hover:underline">Laporan</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-center text-stone-400">Belum ada proyek.</td></tr>
                @endforelse
            </tbody>
            @if ($summaries->isNotEmpty())
                <tfoot>
                    <tr class="bg-stone-50 font-semibold text-stone-900">
                        <td colspan="2" class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-right">Rp{{ number_format($grand['approvedTotal'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp{{ number_format($grand['harvestRevenue'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp{{ number_format($grand['netProfit'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp{{ number_format($grand['companyShare'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp{{ number_format($grand['ownerShare'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endsection
