@extends('app.layouts.app')

@section('title', 'Laporan ' . $project->name . ' - FunFarm WebApp')

@section('content')
    <a href="{{ route('app.reports.index') }}" class="-ml-2 inline-flex min-h-10 items-center gap-1 rounded-lg px-2 py-2 text-sm font-medium text-emerald-800 transition hover:bg-emerald-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6" /></svg>
        Semua laporan
    </a>

    <div class="mt-2">
        @include('partials.report-body')
    </div>
@endsection
