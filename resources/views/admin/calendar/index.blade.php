@extends('admin.layouts.app')

@section('title', 'Kalender Tanam & Panen - Admin FunFarm')

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">Kalender Tanam &amp; Panen</h1>
            <p class="text-sm text-stone-500">Dari tanggal proyek &amp; pencatatan — semua proyek. Event pending tampil redup.</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('admin.calendar.index', ['bulan' => $prev]) }}" class="rounded-xl border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">&larr;</a>
            <span class="min-w-36 text-center text-sm font-semibold text-stone-900">{{ $month->locale('id')->translatedFormat('F Y') }}</span>
            <a href="{{ route('admin.calendar.index', ['bulan' => $next]) }}" class="rounded-xl border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">&rarr;</a>
            <a href="{{ route('admin.calendar.index') }}" class="rounded-xl border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Bulan Ini</a>
        </div>
    </div>

    @include('partials.calendar-panen-segera', ['projectRoute' => 'admin.projects.show'])

    <div class="mb-4 flex flex-wrap gap-4 text-xs text-stone-500">
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Mulai tanam</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span> Estimasi panen</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span> Aktivitas pencatatan</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-sky-500 opacity-40"></span> Masih pending / proyek selesai (redup)</span>
    </div>

    <div class="mb-6">
        @include('partials.calendar-grid', ['projectRoute' => 'admin.projects.show'])
    </div>

    @include('partials.calendar-agenda', ['projectRoute' => 'admin.projects.show'])
@endsection
