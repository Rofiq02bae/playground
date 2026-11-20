@extends('admin.layouts.app')

@section('title', ($project->exists ? 'Ubah' : 'Buat') . ' Proyek - Admin FunFarm')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">{{ $project->exists ? 'Ubah Proyek & Profil Sawah' : 'Proyek & Profil Sawah Baru' }}</h1>
        <p class="text-sm text-stone-500">Profil sawah menjadi dasar pencatatan, kalender tanam/panen, laporan, dan surat perjanjian.</p>
    </div>

    <form method="POST" action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}" class="max-w-3xl rounded-2xl border border-stone-200 bg-white p-6">
        @csrf
        @if ($project->exists) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama proyek</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required placeholder="cth: Musim Tanam 1 - Sawah Budi"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Pemilik sawah</label>
                <select name="owner_id" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    <option value="">— pilih pemilik —</option>
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}" @selected((int) old('owner_id', $project->owner_id) === $owner->id)>{{ $owner->name }} ({{ $owner->membershipLabel() }})</option>
                    @endforeach
                </select>
                @error('owner_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Penggarap sawah</label>
                <select name="cultivator_id" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    <option value="">— belum ditentukan —</option>
                    @foreach ($cultivators as $cultivator)
                        <option value="{{ $cultivator->id }}" @selected((int) old('cultivator_id', $project->cultivator_id) === $cultivator->id)>{{ $cultivator->name }}</option>
                    @endforeach
                </select>
                @error('cultivator_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama / identitas lahan</label>
                <input type="text" name="field_name" value="{{ old('field_name', $project->field_name) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('field_name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Lokasi sawah</label>
                <input type="text" name="location" value="{{ old('location', $project->location) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('location')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Luas (m²)</label>
                <input type="number" name="area_size" value="{{ old('area_size', $project->area_size) }}" step="0.01" min="0.01" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('area_size')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Koordinat lokasi sawah (opsional)</label>
                <div class="flex gap-2">
                    <input type="text" id="field-coordinates" name="coordinates" value="{{ old('coordinates', $project->coordinates) }}" placeholder="Klik Pilih di Peta" readonly data-open-field-map
                           class="w-full cursor-pointer rounded-xl border border-stone-300 bg-stone-50 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    <button type="button" data-open-field-map class="shrink-0 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-800">Pilih di Peta</button>
                </div>
                <input type="hidden" id="field-boundary" name="boundary_geojson" value="{{ old('boundary_geojson', $project->boundary_geojson ? json_encode($project->boundary_geojson) : '') }}">
                <p class="mt-1.5 text-xs text-stone-400">Gambar poligon batas sawah atau taruh pin di peta satelit; koordinat (latitude, longitude) terisi otomatis dari titik tengahnya.</p>
                @error('coordinates')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('boundary_geojson')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Jenis tanaman</label>
                <input type="text" name="crop_type" value="{{ old('crop_type', $project->crop_type) }}" required placeholder="cth: Padi"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('crop_type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Varietas (opsional)</label>
                <input type="text" name="crop_variety" value="{{ old('crop_variety', $project->crop_variety) }}" placeholder="cth: Inpari 32"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Tanggal mulai</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->toDateString()) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('start_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Estimasi panen</label>
                <input type="date" name="estimated_harvest_date" value="{{ old('estimated_harvest_date', $project->estimated_harvest_date?->toDateString()) }}"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('estimated_harvest_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Status proyek</label>
                <select name="status" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $project->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Bagi hasil — bagian perusahaan (%)</label>
                <input type="number" name="profit_share_company_percent" value="{{ old('profit_share_company_percent', $project->profit_share_company_percent) }}" min="0" max="100" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                <p class="mt-1 text-xs text-stone-400">Sisanya adalah bagian pemilik. Dipakai di laporan &amp; surat perjanjian.</p>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
            <a href="{{ route('admin.projects.index') }}" class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</a>
            
        </div>
    </form>

    {{-- Popup pemilih lokasi sawah: peta baru muncul saat field koordinat / tombol ditekan --}}
    <div id="field-map-modal" class="fixed inset-0 z-50 hidden bg-stone-950/70 p-3 sm:p-6">
        <div class="mx-auto flex h-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center gap-3 border-b border-stone-200 px-4 py-3">
                <div class="min-w-0">
                    <h2 class="truncate text-sm font-semibold text-stone-900">Tentukan Lokasi Sawah</h2>
                    <p id="field-map-status" class="truncate text-xs text-stone-500">Gambar poligon batas sawah atau taruh pin di peta.</p>
                </div>
                <button type="button" id="field-map-gps" class="ml-auto shrink-0 rounded-lg border border-stone-300 px-3 py-2 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Posisi Saya (GPS)</button>
                <button type="button" data-close-field-map class="shrink-0 rounded-lg px-2 py-2 text-lg leading-none text-stone-400 transition hover:text-stone-700" aria-label="Tutup">&times;</button>
            </div>
            <div id="field-map-canvas" class="min-h-0 flex-1"></div>
            <div class="flex items-center justify-end gap-3 border-t border-stone-200 px-4 py-3">
                <button type="button" data-close-field-map class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</button>
                <button type="button" id="field-map-confirm" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Gunakan Lokasi Ini</button>
            </div>
        </div>
    </div>

    @vite(['resources/js/field-map.js'])
@endsection
