@extends('app.layouts.app')

@section('title', 'Surat Perjanjian - FunFarm WebApp')

@section('content')
    <h1 class="text-xl font-semibold text-stone-900">Surat Perjanjian</h1>
    <p class="mb-5 mt-1 text-sm text-stone-500">Surat Perjanjian Kerja Sama Garap Lahan untuk proyek sewa bagi hasil Anda. PDF diterbitkan oleh perusahaan.</p>

    <div class="space-y-3">
        @forelse ($projects as $project)
            <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <p class="line-clamp-2 font-semibold leading-snug text-stone-900">{{ $project->name }}</p>
                    @include('app.partials.status-chip', ['project' => $project])
                </div>
                <p class="mt-1 truncate text-xs text-stone-500">{{ $project->field_name }} &middot; {{ $project->crop_type }} &middot; Pemilik: {{ $project->owner->name }}</p>

                @if ($project->agreement)
                    <dl class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 text-sm">
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-xs text-stone-500">Nomor Surat</dt>
                            <dd class="text-right font-medium text-stone-800">{{ $project->agreement->number }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-xs text-stone-500">Tanggal Surat</dt>
                            <dd class="font-medium text-stone-800">{{ $project->agreement->agreement_date->format('d M Y') }}</dd>
                        </div>
                    </dl>
                    @if ($user->isAdmin() || ($user->isPemilik() && $project->owner_id === $user->id))
                        <a href="{{ route('app.agreements.download', $project) }}" class="mt-3 flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-800 active:bg-emerald-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0 4-4m-4 4-4-4" /><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" /></svg>
                            Unduh Surat Perjanjian (PDF)
                        </a>
                    @else
                        <p class="mt-3 rounded-xl bg-stone-50 px-3 py-2.5 text-center text-xs leading-relaxed text-stone-500">Surat sudah terbit. Pengunduhan khusus pemilik sawah &amp; perusahaan.</p>
                    @endif
                @else
                    <p class="mt-3 flex items-center justify-center gap-1.5 rounded-xl bg-amber-50 px-3 py-2.5 text-center text-xs font-medium text-amber-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                        Surat perjanjian belum diterbitkan oleh perusahaan.
                    </p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-stone-200 bg-white px-6 py-10 text-center shadow-sm">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z" /><path d="M15 3v4h4" /><path d="M9 12h6M9 16h6" /></svg>
                </span>
                <p class="mt-3 font-semibold text-stone-900">Belum ada proyek bagi hasil</p>
                <p class="mx-auto mt-1 max-w-xs text-sm leading-relaxed text-stone-500">Belum ada proyek sewa bagi hasil untuk ditampilkan. Surat muncul di sini setelah perusahaan membuatkan proyek bagi hasil.</p>
            </div>
        @endforelse
    </div>
@endsection
