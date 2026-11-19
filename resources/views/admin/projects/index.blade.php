@extends('admin.layouts.app')

@section('title', 'Proyek Sawah - Admin FunFarm')

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">Proyek &amp; Profil Sawah</h1>
            <p class="text-sm text-stone-500">Pusat pencatatan tiap klien: sewa alat, pupuk, obat, dan biaya produksi.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="ml-auto rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">+ Proyek Baru</a>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari proyek, lahan, lokasi, pemilik..."
               class="w-full max-w-xs rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
        <select name="status" class="rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600">
            <option value="">Semua status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Proyek / Lahan</th>
                    <th class="px-4 py-3 font-semibold">Pemilik</th>
                    <th class="px-4 py-3 font-semibold">Penggarap</th>
                    <th class="px-4 py-3 font-semibold">Tanaman</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Total Biaya (Disetujui)</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3">
                            <span class="font-medium text-stone-900">{{ $project->name }}</span><br>
                            <span class="text-xs text-stone-400">{{ $project->field_name }} &middot; {{ $project->location }} &middot; {{ number_format($project->area_size, 2, ',', '.') }} ha</span>
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $project->owner->name }}<br><span class="text-xs {{ $project->owner->is_member ? 'font-semibold text-emerald-700' : 'text-stone-400' }}">{{ $project->owner->membershipLabel() }}</span></td>
                        <td class="px-4 py-3 text-stone-600">{{ $project->cultivator?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $project->crop_type }}{{ $project->crop_variety ? ' ('.$project->crop_variety.')' : '' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $project->statusLabel() }}</span>
                            @if ($project->pending_count > 0)<span class="ml-1 rounded-full bg-amber-100 px-2 py-1 text-xs font-bold text-amber-800">{{ $project->pending_count }} pending</span>@endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-stone-900">Rp{{ number_format($project->approved_total ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.projects.show', $project) }}" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">Pencatatan</a>
                                <a href="{{ route('admin.projects.edit', $project) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Ubah</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-stone-500">Belum ada proyek. Buat proyek pertama untuk mulai pencatatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $projects->links() }}</div>
@endsection
