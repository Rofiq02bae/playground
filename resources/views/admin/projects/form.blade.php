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
            <div class="sm:col-span-2">
                <span class="mb-1.5 block text-sm font-medium text-stone-700">Jenis sewa</span>
                <div class="flex gap-2">
                    <label class="flex flex-1 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 py-2.5 text-sm has-checked:border-emerald-600 has-checked:bg-emerald-50 has-checked:font-semibold">
                        <input type="radio" name="rental_type" value="bagi_hasil" class="sr-only rental-radio" @checked(old('rental_type', $project->rental_type ?? 'bagi_hasil') !== 'tahunan')> Sewa Bagi Hasil
                    </label>
                    <label class="flex flex-1 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 py-2.5 text-sm has-checked:border-emerald-600 has-checked:bg-emerald-50 has-checked:font-semibold">
                        <input type="radio" name="rental_type" value="tahunan" class="sr-only rental-radio" @checked(old('rental_type', $project->rental_type ?? 'bagi_hasil') === 'tahunan')> Sewa Tahunan
                    </label>
                </div>
                <p class="mt-1.5 text-xs text-stone-400">Bagi hasil: dilengkapi surat perjanjian (PDF). Tahunan: durasi 1–5 tahun dari tanggal mulai, tanpa surat perjanjian.</p>
                @error('rental_type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Pemilik sawah</label>
                <select name="owner_id" id="owner-select" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
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
                <input type="text" id="inp-location" name="location" value="{{ old('location', $project->location) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('location')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Luas (hektar)</label>
                <input type="number" id="inp-area" name="area_size" value="{{ old('area_size', $project->area_size) }}" step="0.01" min="0.01" required
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
                <input type="text" id="inp-crop" name="crop_type" value="{{ old('crop_type', $project->crop_type) }}" required placeholder="cth: Padi"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('crop_type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Varietas (opsional)</label>
                <input type="text" id="inp-variety" name="crop_variety" value="{{ old('crop_variety', $project->crop_variety) }}" placeholder="cth: Inpari 32"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Tanggal mulai</label>
                <input type="date" id="inp-start" name="start_date" value="{{ old('start_date', $project->start_date?->toDateString()) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('start_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div id="group-harvest" class="rental-bagi">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Estimasi panen (akhir masa sewa bagi hasil)</label>
                <input type="date" id="inp-harvest" name="estimated_harvest_date" value="{{ old('estimated_harvest_date', $project->estimated_harvest_date?->toDateString()) }}"
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
            <div id="group-share" class="rental-bagi">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Bagi hasil — bagian perusahaan (%)</label>
                <input type="number" id="inp-share" name="profit_share_company_percent" value="{{ old('profit_share_company_percent', $project->profit_share_company_percent) }}" min="0" max="100" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                <p class="mt-1 text-xs text-stone-400">Sisanya adalah bagian pemilik. Dipakai di laporan &amp; surat perjanjian.</p>
                @error('profit_share_company_percent')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div id="group-duration" class="rental-tahunan hidden">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Jangka waktu sewa tahunan</label>
                <select id="inp-duration" name="duration_years" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    @foreach ([1, 2, 3, 4, 5] as $years)
                        <option value="{{ $years }}" @selected((int) old('duration_years', $project->duration_years) === $years)>{{ $years }} tahun</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-stone-400">Tanggal akhir sewa dihitung otomatis dari tanggal mulai + jangka waktu ini.</p>
                @error('duration_years')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div id="group-price" class="rental-tahunan hidden">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Harga sewa per tahun (Rp, opsional)</label>
                <input type="number" id="inp-price" name="annual_rental_price" value="{{ old('annual_rental_price', $project->annual_rental_price) }}" min="0" step="1000"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                <p class="mt-1 text-xs text-stone-400">Harga sementara — akan divalidasi ulang oleh perusahaan.</p>
                @error('annual_rental_price')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @unless ($project->exists)
                <div id="group-agreement" class="rental-bagi sm:col-span-2">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                        <p class="text-sm font-medium text-stone-800">Surat Perjanjian Kerja Sama Garap Lahan</p>
                        <p class="mt-1 text-xs text-stone-500">Untuk sewa bagi hasil, cek kelengkapan data surat &amp; isi nama saksi. Surat (PDF) dibuat otomatis saat proyek disimpan bila kedua nama saksi terisi; PDF tersimpan dan bisa diunduh kapan pun dari halaman detail proyek.</p>
                        <button type="button" id="open-agreement-modal" class="mt-3 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-800">Cek Kelengkapan Surat Perjanjian</button>
                        <p id="witness-summary" class="mt-2 text-xs text-stone-500"></p>
                        <input type="hidden" name="witness1_name" id="witness1-hidden" value="{{ old('witness1_name') }}">
                        <input type="hidden" name="witness2_name" id="witness2-hidden" value="{{ old('witness2_name') }}">
                    </div>
                </div>
            @endunless
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
            <a href="{{ route('admin.projects.index') }}" class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</a>
            @if ($project->exists)
                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="ml-auto" onsubmit="return confirm('Hapus proyek ini beserta seluruh pencatatannya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-xl border border-red-200 px-5 py-2.5 text-sm font-medium text-red-700 transition hover:bg-red-50">Hapus Proyek</button>
                </form>
            @endif
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

    @unless ($project->exists)
        {{-- Popup cek kelengkapan surat perjanjian (khusus buat proyek baru, sewa bagi hasil) --}}
        <div id="agreement-modal" class="fixed inset-0 z-50 hidden bg-stone-950/70 p-3 sm:p-6">
            <div class="mx-auto flex max-h-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center gap-3 border-b border-stone-200 px-5 py-3.5">
                    <div class="min-w-0">
                        <h2 class="truncate text-sm font-semibold text-stone-900">Cek Kelengkapan Surat Perjanjian</h2>
                        <p class="truncate text-xs text-stone-500">Nomor surat berikutnya: <span class="font-medium text-stone-700">{{ $nextNumber ?? '' }}</span> (terbit saat proyek disimpan)</p>
                    </div>
                    <button type="button" data-close-agreement class="ml-auto shrink-0 rounded-lg px-2 py-2 text-lg leading-none text-stone-400 transition hover:text-stone-700" aria-label="Tutup">&times;</button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                    <ul id="agreement-checklist" class="divide-y divide-stone-100 text-sm"></ul>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama Saksi 1</label>
                            <input type="text" id="modal-witness1" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            <p class="mt-1 text-xs text-stone-400">Perangkat Desa / Ketua Kelompok Tani.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama Saksi 2</label>
                            <input type="text" id="modal-witness2" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            <p class="mt-1 text-xs text-stone-400">Keluarga / Tetangga Batas Lahan.</p>
                        </div>
                    </div>
                    <p id="agreement-status" class="mt-4 rounded-xl bg-stone-50 px-4 py-3 text-xs text-stone-600"></p>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-stone-200 px-5 py-3.5">
                    <button type="button" data-close-agreement class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</button>
                    <button type="button" id="save-witnesses" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Nama Saksi</button>
                </div>
            </div>
        </div>
    @endunless

    <script>
        (function () {
            // --- Toggle field menurut jenis sewa ---
            var bagiGroups = document.querySelectorAll('.rental-bagi');
            var tahunanGroups = document.querySelectorAll('.rental-tahunan');
            function setGroup(groups, hidden) {
                groups.forEach(function (el) {
                    el.classList.toggle('hidden', hidden);
                    el.querySelectorAll('input, select, textarea').forEach(function (i) { i.disabled = hidden; });
                });
            }
            function applyRental() {
                var checked = document.querySelector('.rental-radio:checked');
                var isTahunan = checked && checked.value === 'tahunan';
                setGroup(bagiGroups, isTahunan);
                setGroup(tahunanGroups, !isTahunan);
                var share = document.getElementById('inp-share');
                if (share) share.required = !isTahunan;
                var dur = document.getElementById('inp-duration');
                if (dur) dur.required = !!isTahunan;
            }
            document.querySelectorAll('.rental-radio').forEach(function (r) { r.addEventListener('change', applyRental); });
            applyRental();

            // --- Modal cek kelengkapan surat perjanjian ---
            var modal = document.getElementById('agreement-modal');
            if (!modal) return;
            var ownersData = @json($ownersData ?? []);
            var companyName = @json(config('perjanjian.company_name'));
            var companyAddress = @json(config('perjanjian.company_address'));
            var hidden1 = document.getElementById('witness1-hidden');
            var hidden2 = document.getElementById('witness2-hidden');
            var summary = document.getElementById('witness-summary');

            function val(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }
            function fmtDate(v) { if (!v) return ''; var p = v.split('-'); return p.length === 3 ? p[2] + '-' + p[1] + '-' + p[0] : v; }
            function fmtM2(ha) { var m2 = parseFloat(ha) * 10000; if (isNaN(m2)) return ''; return '± ' + m2.toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' m²'; }
            function row(label, value, state) {
                var icon = state === 'ok'
                    ? '<span class="font-bold text-emerald-600">&#10003;</span>'
                    : (state === 'opt'
                        ? '<span class="font-bold text-stone-300">&#8211;</span>'
                        : '<span class="font-bold text-red-500">&#10007;</span>');
                return '<li class="flex gap-3 py-2"><span class="w-5 shrink-0 text-center">' + icon + '</span><span class="min-w-0"><span class="block text-xs font-medium uppercase tracking-wide text-stone-400">' + label + '</span><span class="block break-words text-stone-800">' + (value || '<em class="text-red-500">belum terisi</em>') + '</span></span></li>';
            }
            function refreshSummary() {
                if (hidden1.value.trim() && hidden2.value.trim()) {
                    summary.textContent = 'Saksi: ' + hidden1.value.trim() + ' & ' + hidden2.value.trim() + ' — surat akan dibuat saat proyek disimpan.';
                } else {
                    summary.textContent = 'Nama saksi belum lengkap — surat belum dibuat. Bisa juga dibuat menyusul dari halaman detail proyek.';
                }
            }
            function openModal() {
                var ownerId = parseInt(val('owner-select'), 10);
                var owner = ownersData.find(function (o) { return o.id === ownerId; }) || {};
                var share = parseInt(val('inp-share'), 10);
                var loc = val('inp-location');
                var coords = val('field-coordinates');
                var crop = val('inp-crop');
                var variety = val('inp-variety');
                var rows = '';
                rows += row('Nama Pemilik Lahan', owner.name || '', owner.name ? 'ok' : 'missing');
                rows += row('NIK Pemilik (opsional — kosong = tidak tampil di surat)', owner.nik || '— tidak diisi —', owner.nik ? 'ok' : 'opt');
                rows += row('Pekerjaan Pemilik (opsional — kosong = tidak tampil di surat)', owner.occupation || '— tidak diisi —', owner.occupation ? 'ok' : 'opt');
                rows += row('Alamat Lengkap Pemilik', owner.address || '', owner.address ? 'ok' : 'missing');
                rows += row('Tempat Penandatanganan (wilayah alamat pemilik)', owner.place || '', owner.place ? 'ok' : 'missing');
                rows += row('Perusahaan (PIHAK KEDUA)', companyName + ' — ' + companyAddress, 'ok');
                rows += row('Lokasi Lahan', loc, loc ? 'ok' : 'missing');
                rows += row('Koordinat (opsional)', coords || '— tidak diisi —', coords ? 'ok' : 'opt');
                rows += row('Luas Lahan (dalam m²)', fmtM2(val('inp-area')), val('inp-area') ? 'ok' : 'missing');
                rows += row('Komoditas', crop ? crop + (variety ? ' (' + variety + ')' : '') : '', crop ? 'ok' : 'missing');
                rows += row('Tanggal Mulai', fmtDate(val('inp-start')), val('inp-start') ? 'ok' : 'missing');
                rows += row('Tanggal Akhir (estimasi panen)', fmtDate(val('inp-harvest')), val('inp-harvest') ? 'ok' : 'missing');
                rows += row('Bagi Hasil Pemilik / Perusahaan', isNaN(share) ? '' : (100 - share) + '% / ' + share + '%', isNaN(share) ? 'missing' : 'ok');
                document.getElementById('agreement-checklist').innerHTML = rows;
                document.getElementById('modal-witness1').value = hidden1.value;
                document.getElementById('modal-witness2').value = hidden2.value;
                updateStatus();
                modal.classList.remove('hidden');
            }
            function updateStatus() {
                var missing = document.querySelectorAll('#agreement-checklist li').length;
                var incomplete = document.getElementById('agreement-checklist').innerHTML.indexOf('belum terisi') !== -1;
                var w1 = document.getElementById('modal-witness1').value.trim();
                var w2 = document.getElementById('modal-witness2').value.trim();
                var el = document.getElementById('agreement-status');
                if (incomplete) {
                    el.textContent = 'Masih ada data wajib yang belum terisi (tanda ✗). Lengkapi dulu di form proyek / data pemilik — surat tetap bisa dibuat menyusul dari detail proyek.';
                } else if (!w1 || !w2) {
                    el.textContent = 'Data surat lengkap. Isi kedua nama saksi agar surat otomatis dibuat saat proyek disimpan.';
                } else {
                    el.textContent = 'Semua lengkap. Klik "Simpan Nama Saksi", lalu simpan proyek — PDF surat perjanjian langsung dibuat dan tersimpan.';
                }
                return missing;
            }
            document.getElementById('open-agreement-modal').addEventListener('click', openModal);
            modal.querySelectorAll('[data-close-agreement]').forEach(function (b) { b.addEventListener('click', function () { modal.classList.add('hidden'); }); });
            ['modal-witness1', 'modal-witness2'].forEach(function (id) { document.getElementById(id).addEventListener('input', updateStatus); });
            document.getElementById('save-witnesses').addEventListener('click', function () {
                hidden1.value = document.getElementById('modal-witness1').value.trim();
                hidden2.value = document.getElementById('modal-witness2').value.trim();
                refreshSummary();
                modal.classList.add('hidden');
            });
            refreshSummary();
        })();
    </script>

    @vite(['resources/js/field-map.js'])
@endsection
