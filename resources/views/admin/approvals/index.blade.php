@extends('admin.layouts.app')

@section('title', 'Persetujuan Pencatatan - Admin FunFarm')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">Persetujuan Pencatatan</h1>
        <p class="text-sm text-stone-500">Data yang diinput penggarap menunggu di sini. Yang disetujui masuk laporan resmi; yang ditolak tidak dihitung.</p>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Tanggal</th>
                    <th class="px-4 py-3 font-semibold">Proyek / Pemilik</th>
                    <th class="px-4 py-3 font-semibold">Kategori / Item</th>
                    <th class="px-4 py-3 text-right font-semibold">Total</th>
                    <th class="px-4 py-3 font-semibold">Diinput</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pending as $expense)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3 text-stone-600">{{ $expense->date->format('d M Y') }}</td>
                        <td class="px-4 py-3"><a href="{{ route('admin.projects.show', $expense->project) }}" class="font-medium text-emerald-800 hover:underline">{{ $expense->project->name }}</a><br><span class="text-xs text-stone-400">{{ $expense->project->owner->name }}</span></td>
                        <td class="px-4 py-3"><span class="font-medium text-stone-900">{{ $expense->name }}</span><br><span class="text-xs text-stone-400">{{ $expense->categoryLabel() }} · {{ number_format($expense->quantity, 2, ',', '.') }} {{ $expense->unit }} &times; Rp{{ number_format($expense->unit_price, 0, ',', '.') }}</span></td>
                        <td class="px-4 py-3 text-right font-medium text-stone-900">Rp{{ number_format($expense->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $expense->creator->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('admin.expenses.reject', $expense) }}" class="flex gap-2" onsubmit="return confirm('Tolak pencatatan ini?')">
                                    @csrf
                                    <input type="text" name="rejection_note" placeholder="Alasan (opsional)" class="w-36 rounded-lg border border-stone-300 px-2 py-1.5 text-xs outline-none focus:border-red-400">
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-500">Tidak ada pencatatan yang menunggu persetujuan. Bersih!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $pending->links() }}</div>
@endsection
