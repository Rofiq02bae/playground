@extends('admin.layouts.app')

@section('title', $project->name . ' - Pencatatan')

@section('content')
    <div class="mb-6 flex flex-wrap items-start gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">{{ $project->name }}</h1>
            <p class="text-sm text-stone-500">{{ $project->field_name }} &middot; {{ $project->location }} &middot; {{ number_format($project->area_size, 2, ',', '.') }} ha &middot; {{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }}</p>
            <p class="mt-1 text-sm text-stone-500">Pemilik: <strong class="text-stone-700">{{ $project->owner->name }}</strong> ({{ $project->owner->membershipLabel() }}) &middot; Penggarap: <strong class="text-stone-700">{{ $project->cultivator?->name ?? '-' }}</strong> &middot; Status: {{ $project->statusLabel() }}</p>
            @if ($project->isBagiHasil())
                <p class="text-sm text-stone-500">{{ $project->rentalTypeLabel() }} &middot; Mulai {{ $project->start_date->format('d M Y') }} &middot; Estimasi panen {{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }} &middot; Bagi hasil perusahaan {{ $project->profit_share_company_percent }}%</p>
            @else
                <p class="text-sm text-stone-500">{{ $project->rentalTypeLabel() }} {{ $project->duration_years }} tahun &middot; Mulai {{ $project->start_date->format('d M Y') }} &middot; Akhir sewa {{ $project->lease_end_date?->format('d M Y') ?? '-' }}@if ($project->annual_rental_price) &middot; Harga Rp{{ number_format($project->annual_rental_price, 0, ',', '.') }}/tahun @endif</p>
            @endif
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.reports.show', $project) }}" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-800">Lihat Laporan</a>
            <a href="{{ route('admin.projects.edit', $project) }}" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Ubah Profil</a>
        </div>
    </div>

    <div class="mb-6">
        @include('partials.field-map-display', ['project' => $project])
    </div>

    @if ($project->isBagiHasil())
        <div class="mb-6 rounded-2xl border border-stone-200 bg-white p-5">
            <div class="flex flex-wrap items-start gap-3">
                <div>
                    <h2 class="font-semibold text-stone-900">Surat Perjanjian Kerja Sama Garap Lahan</h2>
                    <p class="text-xs text-stone-500">Antara pemilik sawah dan {{ config('perjanjian.company_name') }} (PIHAK KEDUA).</p>
                </div>
                @if ($project->agreement)
                    <a href="{{ route('admin.projects.agreement.download', $project) }}" class="ml-auto rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-800">Unduh PDF Surat</a>
                @endif
            </div>
            @if ($project->agreement)
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div><dt class="text-xs uppercase tracking-wide text-stone-400">Nomor Surat</dt><dd class="mt-0.5 font-medium text-stone-800">{{ $project->agreement->number }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wide text-stone-400">Tanggal Surat</dt><dd class="mt-0.5 font-medium text-stone-800">{{ $project->agreement->agreement_date->format('d M Y') }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wide text-stone-400">Saksi 1</dt><dd class="mt-0.5 font-medium text-stone-800">{{ $project->agreement->witness1_name }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wide text-stone-400">Saksi 2</dt><dd class="mt-0.5 font-medium text-stone-800">{{ $project->agreement->witness2_name }}</dd></div>
                </dl>
                <p class="mt-3 text-xs text-stone-400">PDF tersimpan di sistem dan bisa diunduh ulang kapan pun dari tombol di atas.</p>
            @else
                @if ($letterData && ! $letterData['complete'])
                    <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <p class="font-medium">Data surat belum lengkap:</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($letterData['checklist'] as $item)
                                @unless ($item['filled'])<li>{{ $item['label'] }}</li>@endunless
                            @endforeach
                        </ul>
                        <p class="mt-1 text-xs">Lengkapi lewat Ubah Profil proyek / data pemilik. Surat tetap bisa dibuat, tetapi bagian yang kosong akan tampil kosong di PDF.</p>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.projects.agreement.store', $project) }}" class="mt-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama Saksi 1</label>
                            <input type="text" name="witness1_name" required placeholder="Perangkat Desa / Ketua Kelompok Tani"
                                   class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama Saksi 2</label>
                            <input type="text" name="witness2_name" required placeholder="Keluarga / Tetangga Batas Lahan"
                                   class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Generate Surat Perjanjian (PDF)</button>
                    <p class="mt-2 text-xs text-stone-400">Nomor surat berikutnya: {{ \App\Support\AgreementGenerator::nextNumber() }} — nomor terbit sekali dan tersimpan bersama PDF-nya.</p>
                </form>
            @endif
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Total Biaya Berjalan</p>
            <p class="mt-1 text-2xl font-bold text-emerald-900">Rp{{ number_format($approvedTotal, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-700">Hanya data approved yang dihitung</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 sm:col-span-2">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-500">Rekap per Kategori (Disetujui)</p>
            @forelse ($byCategory as $cat => $total)
                <div class="flex justify-between border-b border-stone-100 py-1.5 text-sm last:border-0">
                    <span class="text-stone-600">{{ \App\Models\ProjectExpense::CATEGORIES[$cat] ?? $cat }}</span>
                    <span class="font-medium text-stone-900">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
            @empty
                <p class="text-sm text-stone-400">Belum ada pencatatan yang disetujui.</p>
            @endforelse
        </div>
    </div>

    <div class="mb-6">
        @include('partials.expense-form', ['action' => route('admin.projects.expenses.store', $project)])
    </div>

    <div class="mb-6 rounded-2xl border border-stone-200 bg-white p-5">
        <h2 class="font-semibold text-stone-900">Hasil Panen</h2>
        <p class="mb-4 text-sm text-stone-500">Catat setiap panen (bisa bertahap). Total hasil dipakai di laporan untuk laba bersih &amp; bagi hasil.</p>

        @if ($harvests->isNotEmpty())
            <div class="mb-4 overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-500">
                            <th class="py-2 pr-3 font-semibold">Tanggal</th>
                            <th class="py-2 pr-3 font-semibold">Hasil</th>
                            <th class="py-2 pr-3 font-semibold">Harga / kg</th>
                            <th class="py-2 pr-3 text-right font-semibold">Total</th>
                            <th class="py-2 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($harvests as $harvest)
                            <tr class="border-b border-stone-100 last:border-0">
                                <td class="py-2 pr-3 text-stone-600">{{ $harvest->harvest_date->format('d M Y') }}</td>
                                <td class="py-2 pr-3 text-stone-700">{{ number_format($harvest->quantity_kg, 2, ',', '.') }} kg</td>
                                <td class="py-2 pr-3 text-stone-700">Rp{{ number_format($harvest->unit_price, 0, ',', '.') }}</td>
                                <td class="py-2 pr-3 text-right font-medium text-stone-900">Rp{{ number_format($harvest->total_revenue, 0, ',', '.') }}</td>
                                <td class="py-2 text-right">
                                    <form method="POST" action="{{ route('admin.harvests.destroy', $harvest) }}" onsubmit="return confirm('Hapus catatan panen ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="py-2 pr-3 text-right text-sm font-medium text-stone-500">Total hasil panen</td>
                            <td class="py-2 pr-3 text-right font-bold text-emerald-800">Rp{{ number_format($harvests->sum('total_revenue'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.projects.harvests.store', $project) }}" class="grid gap-3 sm:grid-cols-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Tanggal panen</label>
                <input type="date" name="harvest_date" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Hasil (kg)</label>
                <input type="number" name="quantity_kg" step="0.01" min="0.01" required placeholder="2500" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Harga jual / kg (Rp)</label>
                <input type="number" name="unit_price" step="1" min="0" required placeholder="6000" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Catat Panen</button>
            </div>
            <div class="sm:col-span-4">
                <input type="text" name="notes" placeholder="Catatan (opsional), cth: gabah kering panen" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            </div>
        </form>
    </div>

    <h2 class="mb-3 font-semibold text-stone-900">Riwayat Pencatatan</h2>
    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Tanggal</th>
                    <th class="px-4 py-3 font-semibold">Kategori / Item</th>
                    <th class="px-4 py-3 font-semibold">Jumlah</th>
                    <th class="px-4 py-3 text-right font-semibold">Total</th>
                    <th class="px-4 py-3 font-semibold">Diinput</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3 text-stone-600">{{ $expense->date->format('d M Y') }}</td>
                        <td class="px-4 py-3"><span class="font-medium text-stone-900">{{ $expense->name }}</span><br><span class="text-xs text-stone-400">{{ $expense->categoryLabel() }}{{ $expense->item_type ? ' · '.$expense->item_type : '' }}{{ $expense->notes ? ' · '.$expense->notes : '' }}</span></td>
                        <td class="px-4 py-3 text-stone-600">{{ number_format($expense->quantity, 2, ',', '.') }} {{ $expense->unit }} &times; Rp{{ number_format($expense->unit_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium text-stone-900">Rp{{ number_format($expense->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $expense->creator->name }}</td>
                        <td class="px-4 py-3">
                            @if ($expense->status === 'approved')<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Disetujui</span>
                            @elseif ($expense->status === 'rejected')<span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Ditolak</span>@if ($expense->rejection_note)<br><span class="text-xs text-red-500">{{ $expense->rejection_note }}</span>@endif
                            @else<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu Persetujuan</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @if ($expense->status === 'pending')
                                    <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.expenses.reject', $expense) }}" onsubmit="return confirm('Tolak pencatatan ini?')">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">Tolak</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" onsubmit="return confirm('Hapus pencatatan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-stone-500">Belum ada pencatatan untuk proyek ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $expenses->links() }}</div>
@endsection
