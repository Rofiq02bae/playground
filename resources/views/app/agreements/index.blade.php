@extends('app.layouts.app')

@section('title', 'Surat Perjanjian - FunFarm WebApp')

@section('content')
    <div class="mb-4">
        <h1 class="font-semibold text-stone-900">Surat Perjanjian</h1>
        <p class="text-xs text-stone-500">Surat Perjanjian Kerja Sama Garap Lahan untuk proyek sewa bagi hasil Anda. PDF diterbitkan oleh perusahaan.</p>
    </div>

    <div class="space-y-3">
        @forelse ($projects as $project)
            <div class="rounded-2xl border border-stone-200 bg-white p-4">
                <div class="flex items-start justify-between gap-2">
                    <p class="line-clamp-2 font-medium leading-snug text-stone-900">{{ $project->name }}</p>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">{{ $project->statusLabel() }}</span>
                </div>
                <p class="mt-1 truncate text-xs text-stone-400">{{ $project->field_name }} &middot; {{ $project->crop_type }} &middot; Pemilik: {{ $project->owner->name }}</p>

                @if ($project->agreement)
                    <dl class="mt-3 space-y-1.5 border-t border-stone-100 pt-3 text-sm">
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-xs text-stone-400">Nomor Surat</dt>
                            <dd class="font-medium text-stone-800">{{ $project->agreement->number }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <dt class="text-xs text-stone-400">Tanggal Surat</dt>
                            <dd class="font-medium text-stone-800">{{ $project->agreement->agreement_date->format('d M Y') }}</dd>
                        </div>
                    </dl>
                    @if ($user->isAdmin() || ($user->isPemilik() && $project->owner_id === $user->id))
                        <a href="{{ route('app.agreements.download', $project) }}" class="mt-3 block rounded-xl bg-emerald-700 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-emerald-800">Unduh Surat Perjanjian (PDF)</a>
                    @else
                        <p class="mt-3 rounded-xl bg-stone-50 px-3 py-2 text-center text-xs text-stone-500">Surat sudah terbit. Pengunduhan khusus pemilik sawah &amp; perusahaan.</p>
                    @endif
                @else
                    <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-center text-xs text-amber-700">Surat perjanjian belum diterbitkan oleh perusahaan.</p>
                @endif
            </div>
        @empty
            <p class="rounded-2xl border border-stone-200 bg-white px-4 py-6 text-center text-sm text-stone-400">Belum ada proyek sewa bagi hasil untuk ditampilkan.</p>
        @endforelse
    </div>
@endsection
