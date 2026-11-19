@extends('admin.layouts.app')

@section('title', $project->name . ' - Pencatatan')

@section('content')
    <div class="mb-6 flex flex-wrap items-start gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">{{ $project->name }}</h1>
            <p class="text-sm text-stone-500">{{ $project->field_name }} &middot; {{ $project->location }} &middot; {{ number_format($project->area_size, 2, ',', '.') }} ha &middot; {{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }}</p>
            <p class="mt-1 text-sm text-stone-500">Pemilik: <strong class="text-stone-700">{{ $project->owner->name }}</strong> ({{ $project->owner->membershipLabel() }}) &middot; Penggarap: <strong class="text-stone-700">{{ $project->cultivator?->name ?? '-' }}</strong> &middot; Status: {{ $project->statusLabel() }}</p>
            <p class="text-sm text-stone-500">Mulai {{ $project->start_date->format('d M Y') }} &middot; Estimasi panen {{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }} &middot; Bagi hasil perusahaan {{ $project->profit_share_company_percent }}%</p>
        </div>
        <a href="{{ route('admin.projects.edit', $project) }}" class="ml-auto rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Ubah Profil</a>
    </div>

    <div class="mb-6">
        @include('partials.field-map-display', ['project' => $project])
    </div>

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
