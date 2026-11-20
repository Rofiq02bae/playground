@extends('app.layouts.app')

@section('title', 'Kalender Tanam & Panen - FunFarm WebApp')

@section('content')
    <div class="mb-4 flex items-center gap-2">
        <div>
            <h1 class="font-semibold text-stone-900">Kalender Tanam &amp; Panen</h1>
            <p class="text-xs text-stone-500">Sesuai sawah yang bisa kamu lihat.</p>
        </div>
        <div class="ml-auto flex items-center gap-1.5">
            <a href="{{ route('app.calendar.index', ['bulan' => $prev]) }}" class="rounded-lg border border-stone-300 px-2.5 py-1.5 text-sm font-medium text-stone-700">&larr;</a>
            <span class="min-w-24 text-center text-sm font-semibold text-stone-900">{{ $month->locale('id')->translatedFormat('F Y') }}</span>
            <a href="{{ route('app.calendar.index', ['bulan' => $next]) }}" class="rounded-lg border border-stone-300 px-2.5 py-1.5 text-sm font-medium text-stone-700">&rarr;</a>
        </div>
    </div>

    @include('partials.calendar-panen-segera', ['projectRoute' => 'app.sawah.show'])

    <div class="mb-4">
        @include('partials.calendar-grid', ['projectRoute' => 'app.sawah.show', 'compact' => true])
    </div>

    <div class="mb-4 flex flex-wrap gap-3 text-xs text-stone-500">
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Mulai tanam</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span> Estimasi panen</span>
        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span> Pencatatan</span>
    </div>

    @include('partials.calendar-agenda', ['projectRoute' => 'app.sawah.show'])
@endsection
