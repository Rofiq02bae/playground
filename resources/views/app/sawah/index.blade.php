@extends('app.layouts.app')

@section('title', 'Sawah Saya - FunFarm WebApp')

@section('content')
    <h1 class="text-xl font-semibold text-stone-900">Sawah &amp; Proyek</h1>
    <p class="mb-5 mt-1 text-sm text-stone-500">
        @if (auth()->user()->isPemilik()) Sawah milik Anda dan catatan resminya.
        @elseif (auth()->user()->isPenggarap()) Sawah yang Anda garap. Tambah catatan dari halaman detail sawah.
        @else Semua proyek (akses admin). @endif
    </p>

    <div class="flex flex-col gap-3">
        @forelse ($projects as $project)
            <a href="{{ route('app.sawah.show', $project) }}" class="block rounded-2xl border border-stone-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 active:bg-stone-50">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-semibold leading-snug text-stone-900">{{ $project->name }}</p>
                    @include('app.partials.status-chip', ['project' => $project])
                </div>
                <p class="mt-1.5 text-xs leading-relaxed text-stone-500">{{ $project->field_name }} &middot; {{ $project->location }} &middot; {{ number_format($project->area_size, 2, ',', '.') }} ha</p>
                <p class="text-xs leading-relaxed text-stone-500">{{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }} &middot; Panen est. {{ $project->estimated_harvest_date?->format('d M Y') ?? '-' }}</p>
                <p class="mt-1 text-xs text-stone-500">Pemilik: {{ $project->owner->name }} &middot; Penggarap: {{ $project->cultivator?->name ?? '-' }}</p>
                <div class="mt-3 flex items-center justify-between border-t border-stone-100 pt-3">
                    <span class="text-xs text-stone-500">Total biaya disetujui</span>
                    <span class="flex items-center gap-1 text-sm font-bold text-stone-900">Rp{{ number_format($project->approved_total ?? 0, 0, ',', '.') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6" /></svg>
                    </span>
                </div>
                @if ($project->pending_count > 0)
                    <p class="mt-3 flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-2 text-xs font-semibold text-amber-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                        {{ $project->pending_count }} catatan menunggu persetujuan perusahaan
                    </p>
                @endif
            </a>
        @empty
            <div class="rounded-2xl border border-stone-200 bg-white px-6 py-10 text-center shadow-sm">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.6-7-11a7 7 0 1 1 14 0c0 5.4-7 11-7 11z" /><circle cx="12" cy="10" r="2.5" /></svg>
                </span>
                <p class="mt-3 font-semibold text-stone-900">Belum ada sawah terkait</p>
                <p class="mx-auto mt-1 max-w-xs text-sm leading-relaxed text-stone-500">Belum ada sawah/proyek yang terkait dengan akun Anda. Proyek dibuatkan oleh perusahaan.</p>
            </div>
        @endforelse
    </div>
@endsection
