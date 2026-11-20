{{-- Isi laporan biaya produksi & bagi hasil satu proyek. Butuh semua variabel dari App\Support\ProjectReport::forProject --}}
<div class="rounded-2xl border border-stone-200 bg-white p-5">
    <div class="border-b border-stone-100 pb-4">
        <h1 class="text-lg font-bold text-stone-900">Laporan Biaya Produksi</h1>
        <p class="text-sm text-stone-500">{{ $project->name }}</p>
        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1.5 text-sm sm:grid-cols-3">
            <div><dt class="text-xs text-stone-400">Lahan</dt><dd class="text-stone-700">{{ $project->field_name }} — {{ $project->location }}</dd></div>
            <div><dt class="text-xs text-stone-400">Luas</dt><dd class="text-stone-700">{{ number_format($project->area_size, 2, ',', '.') }} ha</dd></div>
            <div><dt class="text-xs text-stone-400">Tanaman</dt><dd class="text-stone-700">{{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }}</dd></div>
            <div><dt class="text-xs text-stone-400">Pemilik</dt><dd class="text-stone-700">{{ $project->owner->name }} ({{ $project->owner->membershipLabel() }})</dd></div>
            <div><dt class="text-xs text-stone-400">Penggarap</dt><dd class="text-stone-700">{{ $project->cultivator?->name ?? '-' }}</dd></div>
            <div><dt class="text-xs text-stone-400">Periode</dt><dd class="text-stone-700">{{ $project->start_date->format('d M Y') }} — {{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }}</dd></div>
        </dl>
    </div>

    <h2 class="mt-5 text-sm font-semibold uppercase tracking-wide text-stone-500">Rincian Biaya Produksi (Disetujui)</h2>
    <table class="mt-2 w-full text-left text-sm">
        <thead>
            <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-500">
                <th class="py-2 pr-3 font-semibold">Tanggal</th>
                <th class="py-2 pr-3 font-semibold">Kategori / Item</th>
                <th class="py-2 pr-3 font-semibold">Jumlah</th>
                <th class="py-2 text-right font-semibold">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <tr class="border-b border-stone-100">
                    <td class="py-2 pr-3 text-stone-600">{{ $expense->date->format('d M Y') }}</td>
                    <td class="py-2 pr-3 text-stone-700">{{ $expense->categoryLabel() }} — {{ $expense->name }}</td>
                    <td class="py-2 pr-3 text-stone-600">{{ number_format($expense->quantity, 2, ',', '.') }} {{ $expense->unit }} &times; Rp{{ number_format($expense->unit_price, 0, ',', '.') }}</td>
                    <td class="py-2 text-right font-medium text-stone-900">Rp{{ number_format($expense->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-3 text-center text-stone-400">Belum ada pencatatan yang disetujui.</td></tr>
            @endforelse
            <tr>
                <td colspan="3" class="py-2 pr-3 text-right font-semibold text-stone-700">Total Biaya Produksi</td>
                <td class="py-2 text-right font-bold text-stone-900">Rp{{ number_format($approvedTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @if ($pendingCount > 0)
        <p class="mt-1 text-xs text-stone-400">Catatan: {{ $pendingCount }} pencatatan (Rp{{ number_format($pendingTotal, 0, ',', '.') }}) masih menunggu persetujuan dan belum dihitung di laporan ini.</p>
    @endif

    <h2 class="mt-6 text-sm font-semibold uppercase tracking-wide text-stone-500">Hasil Panen</h2>
    <table class="mt-2 w-full text-left text-sm">
        <thead>
            <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-500">
                <th class="py-2 pr-3 font-semibold">Tanggal</th>
                <th class="py-2 pr-3 font-semibold">Hasil</th>
                <th class="py-2 pr-3 font-semibold">Harga / kg</th>
                <th class="py-2 text-right font-semibold">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($harvests as $harvest)
                <tr class="border-b border-stone-100">
                    <td class="py-2 pr-3 text-stone-600">{{ $harvest->harvest_date->format('d M Y') }}</td>
                    <td class="py-2 pr-3 text-stone-700">{{ number_format($harvest->quantity_kg, 2, ',', '.') }} kg{{ $harvest->notes ? ' — '.$harvest->notes : '' }}</td>
                    <td class="py-2 pr-3 text-stone-600">Rp{{ number_format($harvest->unit_price, 0, ',', '.') }}</td>
                    <td class="py-2 text-right font-medium text-stone-900">Rp{{ number_format($harvest->total_revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-3 text-center text-stone-400">Belum ada panen yang dicatat.</td></tr>
            @endforelse
            <tr>
                <td colspan="3" class="py-2 pr-3 text-right font-semibold text-stone-700">Total Hasil Panen ({{ number_format($harvestQuantity, 2, ',', '.') }} kg)</td>
                <td class="py-2 text-right font-bold text-stone-900">Rp{{ number_format($harvestRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-6 rounded-xl bg-emerald-50 p-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Ringkasan &amp; Bagi Hasil</h2>
        <dl class="mt-2 space-y-1.5 text-sm">
            <div class="flex justify-between"><dt class="text-stone-600">Total hasil panen</dt><dd class="font-medium text-stone-900">Rp{{ number_format($harvestRevenue, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-stone-600">Total biaya produksi</dt><dd class="font-medium text-stone-900">− Rp{{ number_format($approvedTotal, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between border-t border-emerald-100 pt-1.5"><dt class="font-semibold text-stone-800">Laba bersih</dt><dd class="font-bold {{ $netProfit < 0 ? 'text-red-700' : 'text-emerald-900' }}">Rp{{ number_format($netProfit, 0, ',', '.') }}</dd></div>
            @if ($netProfit > 0)
                <div class="flex justify-between"><dt class="text-stone-600">Bagian perusahaan ({{ $companyPercent }}%)</dt><dd class="font-semibold text-stone-900">Rp{{ number_format($companyShare, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-stone-600">Bagian pemilik ({{ 100 - $companyPercent }}%)</dt><dd class="font-semibold text-stone-900">Rp{{ number_format($ownerShare, 0, ',', '.') }}</dd></div>
            @else
                <p class="text-xs text-stone-500">Belum ada laba bersih untuk dibagi{{ $harvestRevenue === 0 ? ' — panen belum dicatat atau belum terjual' : '' }}.</p>
            @endif
        </dl>
    </div>

    <p class="mt-4 text-xs text-stone-400">Dihasilkan dari pencatatan FunFarm — hanya data berstatus disetujui yang dihitung. Dicetak {{ now()->format('d M Y H:i') }}.</p>
</div>
