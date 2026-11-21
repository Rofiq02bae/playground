@extends('app.layouts.app')

@section('title', 'Kalender Tanam & Panen - FunFarm WebApp')

@section('content')
    <h1 class="text-xl font-semibold text-stone-900">Kalender Tanam &amp; Panen</h1>
    <p class="mb-5 mt-1 text-sm text-stone-500">Jadwal sesuai sawah yang bisa kamu lihat.</p>

    <div class="mb-4 flex items-center justify-between gap-2 rounded-2xl border border-stone-200 bg-white px-3 py-2.5 shadow-sm">
        <a href="{{ route('app.calendar.index', ['bulan' => $prev]) }}" class="flex h-11 w-11 items-center justify-center rounded-xl border border-stone-300 text-stone-700 transition hover:bg-stone-50 active:bg-stone-100" aria-label="Bulan sebelumnya">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6" /></svg>
        </a>
        <span class="text-center text-base font-semibold text-stone-900">{{ $month->locale('id')->translatedFormat('F Y') }}</span>
        <a href="{{ route('app.calendar.index', ['bulan' => $next]) }}" class="flex h-11 w-11 items-center justify-center rounded-xl border border-stone-300 text-stone-700 transition hover:bg-stone-50 active:bg-stone-100" aria-label="Bulan berikutnya">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6" /></svg>
        </a>
    </div>

    @include('partials.calendar-panen-segera', ['projectRoute' => 'app.sawah.show'])

    <div class="mb-4">
        @include('partials.calendar-grid', ['projectRoute' => 'app.sawah.show', 'compact' => true])
    </div>

    <div class="mb-5 flex flex-wrap gap-x-4 gap-y-2 px-1 text-xs font-medium text-stone-600">
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Mulai tanam</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span> Estimasi panen</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span> Pencatatan</span>
    </div>

    @include('partials.calendar-agenda', ['projectRoute' => 'app.sawah.show'])
@endsection
