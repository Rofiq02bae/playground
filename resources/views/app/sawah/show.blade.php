@extends('app.layouts.app')

@section('title', $project->name . ' - FunFarm WebApp')

@section('content')
    <a href="{{ route('app.sawah.index') }}" class="text-sm text-emerald-700">&larr; Semua sawah</a>

    <div class="mt-3 rounded-2xl border border-stone-200 bg-white p-5">
        <div class="flex items-start justify-between gap-3">
            <h1 class="font-semibold text-stone-900">{{ $project->name }}</h1>
            <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $project->statusLabel() }}</span>
        </div>
        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <div><dt class="text-xs text-stone-400">Lahan</dt><dd class="text-stone-700">{{ $project->field_name }}</dd></div>
            <div><dt class="text-xs text-stone-400">Lokasi</dt><dd class="text-stone-700">{{ $project->location }}</dd></div>
            <div><dt class="text-xs text-stone-400">Luas</dt><dd class="text-stone-700">{{ number_format($project->area_size, 2, ',', '.') }} ha</dd></div>
            <div><dt class="text-xs text-stone-400">Tanaman</dt><dd class="text-stone-700">{{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }}</dd></div>
            <div><dt class="text-xs text-stone-400">Mulai tanam</dt><dd class="text-stone-700">{{ $project->start_date->format('d M Y') }}</dd></div>
            <div><dt class="text-xs text-stone-400">Estimasi panen</dt><dd class="text-stone-700">{{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }}</dd></div>
            <div><dt class="text-xs text-stone-400">Pemilik</dt><dd class="text-stone-700">{{ $project->owner->name }} ({{ $project->owner->membershipLabel() }})</dd></div>
            <div><dt class="text-xs text-stone-400">Penggarap</dt><dd class="text-stone-700">{{ $project->cultivator?->name ?? '-' }}</dd></div>
        </dl>
        <div class="mt-4 rounded-xl bg-emerald-50 px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Total Biaya Disetujui Perusahaan</p>
            <p class="text-xl font-bold text-emerald-900">Rp{{ number_format($approvedTotal, 0, ',', '.') }}</p>
        </div>
        <div class="mt-4">
            @include('partials.field-map-display', ['project' => $project])
        </div>
    </div>

    @if ($canAdd)
        <div class="mt-4">
            @include('partials.expense-form', ['action' => route('app.sawah.expenses.store', $project)])
        </div>
    @elseif (auth()->user()->isPemilik())
        <p class="mt-4 rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-500">Role pemilik hanya melihat data. Penambahan catatan dilakukan penggarap dan disetujui perusahaan.</p>
    @endif

    <h2 class="mb-3 mt-6 font-semibold text-stone-900">Riwayat Pencatatan</h2>
    <div class="flex flex-col gap-3">
        @forelse ($expenses as $expense)
            <div class="rounded-2xl border border-stone-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium text-stone-900">{{ $expense->name }}</p>
                        <p class="text-xs text-stone-500">{{ $expense->categoryLabel() }}{{ $expense->item_type ? ' · '.$expense->item_type : '' }} &middot; {{ $expense->date->format('d M Y') }}</p>
                        <p class="text-xs text-stone-500">{{ number_format($expense->quantity, 2, ',', '.') }} {{ $expense->unit }} &times; Rp{{ number_format($expense->unit_price, 0, ',', '.') }} &middot; oleh {{ $expense->creator->name }}</p>
                        @if ($expense->notes)<p class="text-xs text-stone-400">{{ $expense->notes }}</p>@endif
                        @if ($expense->status === 'rejected' && $expense->rejection_note)<p class="text-xs text-red-500">Alasan ditolak: {{ $expense->rejection_note }}</p>@endif
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="font-bold text-stone-900">Rp{{ number_format($expense->total_price, 0, ',', '.') }}</p>
                        @if ($expense->status === 'approved')<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-800">Disetujui</span>
                        @elseif ($expense->status === 'rejected')<span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Ditolak</span>
                        @else<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Menunggu</span>@endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-stone-200 bg-white p-6 text-center text-sm text-stone-500">Belum ada pencatatan.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $expenses->links() }}</div>
@endsection
