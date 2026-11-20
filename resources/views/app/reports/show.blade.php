@extends('app.layouts.app')

@section('title', 'Laporan ' . $project->name . ' - FunFarm WebApp')

@section('content')
    <a href="{{ route('app.reports.index') }}" class="text-sm text-emerald-700">&larr; Semua laporan</a>

    <div class="mt-3">
        @include('partials.report-body')
    </div>
@endsection
