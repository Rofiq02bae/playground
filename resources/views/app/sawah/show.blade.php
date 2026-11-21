@extends('app.layouts.app')

@section('title', $project->name . ' - FunFarm WebApp')

@section('content')
    <a href="{{ route('app.sawah.index') }}" class="-ml-2 inline-flex min-h-10 items-center gap-1 rounded-lg px-2 py-2 text-sm font-medium text-emerald-800 transition hover:bg-emerald-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6" /></svg>
        Semua sawah
    </a>

    <div class="mt-2 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <h1 class="font-semibold leading-snug text-stone-900">{{ $project->name }}</h1>
            @include('app.partials.status-chip', ['project' => $project])
        </div>
        <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Lahan</dt><dd class="mt-0.5 text-stone-800">{{ $project->field_name }}</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Lokasi</dt><dd class="mt-0.5 text-stone-800">{{ $project->location }}</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Luas</dt><dd class="mt-0.5 text-stone-800">{{ number_format($project->area_size, 2, ',', '.') }} ha</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Tanaman</dt><dd class="mt-0.5 text-stone-800">{{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }}</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Mulai tanam</dt><dd class="mt-0.5 text-stone-800">{{ $project->start_date->format('d M Y') }}</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Estimasi panen</dt><dd class="mt-0.5 text-stone-800">{{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }}</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Pemilik</dt><dd class="mt-0.5 text-stone-800">{{ $project->owner->name }} ({{ $project->owner->membershipLabel() }})</dd></div>
            <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Penggarap</dt><dd class="mt-0.5 text-stone-800">{{ $project->cultivator?->name ?? '-' }}</dd></div>
        </dl>
        <div class="mt-5 rounded-xl bg-emerald-700 px-4 py-3.5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-100">Total Biaya Disetujui Perusahaan</p>
            <p class="mt-0.5 text-2xl font-bold text-white">Rp{{ number_format($approvedTotal, 0, ',', '.') }}</p>
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
        <p class="mt-4 flex items-start gap-2.5 rounded-2xl border border-stone-200 bg-white px-4 py-3.5 text-sm leading-relaxed text-stone-500 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 11v5" /><path d="M12 8h.01" /></svg>
            Role pemilik hanya melihat data. Penambahan catatan dilakukan penggarap dan disetujui perusahaan.
        </p>
    @endif

    <h2 class="mb-3 mt-7 text-base font-semibold text-stone-900">Riwayat Pencatatan</h2>
    <div class="flex flex-col gap-3">
        @forelse ($expenses as $expense)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium leading-snug text-stone-900">{{ $expense->name }}</p>
                        <p class="mt-0.5 text-xs leading-relaxed text-stone-500">{{ $expense->categoryLabel() }}{{ $expense->item_type ? ' · '.$expense->item_type : '' }} &middot; {{ $expense->date->format('d M Y') }}</p>
                        <p class="text-xs leading-relaxed text-stone-500">{{ number_format($expense->quantity, 2, ',', '.') }} {{ $expense->unit }} &times; Rp{{ number_format($expense->unit_price, 0, ',', '.') }} &middot; oleh {{ $expense->creator->name }}</p>
                        @if ($expense->notes)<p class="mt-1 text-xs leading-relaxed text-stone-500">{{ $expense->notes }}</p>@endif
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="font-bold text-stone-900">Rp{{ number_format($expense->total_price, 0, ',', '.') }}</p>
                        @if ($expense->status === 'approved')<span class="mt-1 inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Disetujui</span>
                        @elseif ($expense->status === 'rejected')<span class="mt-1 inline-block rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Ditolak</span>
                        @else<span class="mt-1 inline-block rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Menunggu</span>@endif
                    </div>
                </div>
                @if ($expense->status === 'rejected' && $expense->rejection_note)
                    <p class="mt-2.5 rounded-lg bg-red-50 px-3 py-2 text-xs leading-relaxed text-red-700">Alasan ditolak: {{ $expense->rejection_note }}</p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-stone-200 bg-white px-6 py-8 text-center shadow-sm">
                <p class="font-semibold text-stone-900">Belum ada pencatatan</p>
                <p class="mx-auto mt-1 max-w-xs text-sm leading-relaxed text-stone-500">Catatan pupuk, obat, dan sewa akan muncul di sini setelah ditambahkan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $expenses->links() }}</div>
@endsection
