@extends('admin.layouts.app')

@section('title', 'Laporan ' . $project->name . ' - Admin FunFarm')

@section('content')
    <style>
        @media print {
            header, .no-print { display: none !important; }
            main { padding: 0 !important; }
            body { background: #fff !important; }
        }
    </style>

    <div class="no-print mb-4 flex items-center gap-2">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-emerald-800 hover:underline">&larr; Semua laporan</a>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.projects.show', $project) }}" class="rounded-xl border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Detail Proyek</a>
            <button type="button" onclick="window.print()" class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Cetak / Simpan PDF</button>
        </div>
    </div>

    @include('partials.report-body')
@endsection
