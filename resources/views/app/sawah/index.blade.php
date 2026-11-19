@extends('app.layouts.app')

@section('title', 'Sawah Saya - FunFarm WebApp')

@section('content')
    <h1 class="mb-1 text-lg font-semibold text-stone-900">Sawah &amp; Proyek</h1>
    <p class="mb-4 text-sm text-stone-500">
        @if (auth()->user()->isPemilik()) Sawah milik Anda dan catatan resminya.
        @elseif (auth()->user()->isPenggarap()) Sawah yang Anda garap. Tambah catatan dari halaman detail sawah.
        @else Semua proyek (akses admin). @endif
    </p>

    <div class="flex flex-col gap-3">
        @forelse ($projects as $project)
            <a href="{{ route('app.sawah.show', $project) }}" class="block rounded-2xl border border-stone-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-stone-900">{{ $project->name }}</p>
                        <p class="mt-0.5 text-xs text-stone-500">{{ $project->field_name }} &middot; {{ $project->location }} &middot; {{ number_format($project->area_size, 2, ',', '.') }} ha</p>
                        <p class="text-xs text-stone-500">{{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }} &middot; Panen est. {{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }}</p>
                        <p class="mt-1 text-xs text-stone-400">Pemilik: {{ $project->owner->name }} &middot; Penggarap: {{ $project->cultivator?->name ?? '-' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $project->statusLabel() }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-3">
                    <span class="text-xs text-stone-500">Total biaya disetujui</span>
                    <span class="text-sm font-bold text-stone-900">Rp{{ number_format($project->approved_total ?? 0, 0, ',', '.') }}</span>
                </div>
                @if ($project->pending_count > 0)
                    <p class="mt-1 text-right text-xs font-semibold text-amber-700">{{ $project->pending_count }} catatan menunggu persetujuan</p>
                @endif
            </a>
        @empty
            <div class="rounded-2xl border border-stone-200 bg-white p-6 text-center text-sm text-stone-500">
                Belum ada sawah/proyek yang terkait dengan akun Anda. Proyek dibuatkan oleh perusahaan.
            </div>
        @endforelse
    </div>
@endsection
