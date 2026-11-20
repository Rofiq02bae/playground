@extends('admin.layouts.app')

@section('title', ($user->exists ? 'Ubah' : 'Tambah') . ' Pengguna - Admin FunFarm')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">{{ $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h1>
        <p class="text-sm text-stone-500">Untuk pemilik: cek identitas customer dulu (pelanggan WiFi / pemegang kartu), lalu tetapkan status membership-nya.</p>
    </div>

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-2xl rounded-2xl border border-stone-200 bg-white p-6">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="flex flex-col gap-4">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-stone-700">Nama lengkap (sesuai identitas)</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-sm font-medium text-stone-700">Role</label>
                <select id="role" name="role" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>
                            {{ $role === 'admin' ? 'Admin (Perusahaan) — semua akses' : ($role === 'pemilik' ? 'Pemilik Sawah — hanya lihat data' : 'Penggarap Sawah — lihat + tambah data (perlu approval)') }}
                        </option>
                    @endforeach
                </select>
                @error('role')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="username" class="mb-1.5 block text-sm font-medium text-stone-700">Username (untuk login)</label>
                    <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    @error('username')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-stone-700">Email (opsional)</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-medium text-stone-700">Nomor HP / WhatsApp</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <span class="mb-1.5 block text-sm font-medium text-stone-700">Status Membership (untuk pemilik)</span>
                    <div class="flex gap-2">
                        <label class="flex flex-1 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 py-2.5 text-sm has-checked:border-emerald-600 has-checked:bg-emerald-50 has-checked:font-semibold">
                            <input type="radio" name="is_member" value="1" class="sr-only" @checked(old('is_member', $user->is_member))> Membership
                        </label>
                        <label class="flex flex-1 cursor-pointer items-center justify-center rounded-xl border border-stone-300 px-3 py-2.5 text-sm has-checked:border-emerald-600 has-checked:bg-emerald-50 has-checked:font-semibold">
                            <input type="radio" name="is_member" value="0" class="sr-only" @checked(! old('is_member', $user->is_member))> Bukan Membership
                        </label>
                    </div>
                    <p class="mt-1.5 text-xs text-stone-400">Membership = dapat potongan harga sewa. Bukan = harga normal.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nik" class="mb-1.5 block text-sm font-medium text-stone-700">NIK / Nomor KTP (opsional)</label>
                    <input id="nik" type="text" name="nik" value="{{ old('nik', $user->nik) }}"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    <p class="mt-1.5 text-xs text-stone-400">Untuk surat perjanjian. Bila diisi akan tampil di surat; bila kosong, baris NIK tidak ditampilkan.</p>
                    @error('nik')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="occupation" class="mb-1.5 block text-sm font-medium text-stone-700">Pekerjaan (opsional)</label>
                    <input id="occupation" type="text" name="occupation" value="{{ old('occupation', $user->occupation) }}"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    <p class="mt-1.5 text-xs text-stone-400">Untuk surat perjanjian, diperlakukan sama seperti NIK.</p>
                    @error('occupation')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <fieldset class="rounded-xl border border-stone-200 p-4">
                <legend class="px-1 text-sm font-medium text-stone-700">Alamat (sesuai identitas)</legend>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="province_code" class="mb-1.5 block text-sm font-medium text-stone-700">Provinsi</label>
                        <select id="province_code" name="province_code" data-selected="{{ old('province_code', $user->province_code) }}"
                                class="region-select w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                            <option value="">— pilih provinsi —</option>
                        </select>
                        @error('province_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="regency_code" class="mb-1.5 block text-sm font-medium text-stone-700">Kabupaten / Kota</label>
                        <select id="regency_code" name="regency_code" data-selected="{{ old('regency_code', $user->regency_code) }}" disabled
                                class="region-select w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 disabled:bg-stone-50">
                            <option value="">— pilih provinsi dulu —</option>
                        </select>
                        @error('regency_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="district_code" class="mb-1.5 block text-sm font-medium text-stone-700">Kecamatan</label>
                        <select id="district_code" name="district_code" data-selected="{{ old('district_code', $user->district_code) }}" disabled
                                class="region-select w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 disabled:bg-stone-50">
                            <option value="">— pilih kabupaten/kota dulu —</option>
                        </select>
                        @error('district_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="village_code" class="mb-1.5 block text-sm font-medium text-stone-700">Kelurahan / Desa</label>
                        <select id="village_code" name="village_code" data-selected="{{ old('village_code', $user->village_code) }}" disabled
                                class="region-select w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 disabled:bg-stone-50">
                            <option value="">— pilih kecamatan dulu —</option>
                        </select>
                        @error('village_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="hamlet" class="mb-1.5 block text-sm font-medium text-stone-700">Dusun</label>
                        <input id="hamlet" type="text" name="hamlet" value="{{ old('hamlet', $user->hamlet) }}" placeholder="cth: Krajan"
                               class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('hamlet')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="rt" class="mb-1.5 block text-sm font-medium text-stone-700">RT</label>
                            <input id="rt" type="text" name="rt" value="{{ old('rt', $user->rt) }}" placeholder="001"
                                   class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            @error('rt')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="rw" class="mb-1.5 block text-sm font-medium text-stone-700">RW</label>
                            <input id="rw" type="text" name="rw" value="{{ old('rw', $user->rw) }}" placeholder="002"
                                   class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            @error('rw')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-xs text-stone-400">Alamat lengkap tersimpan otomatis dari pilihan wilayah + dusun/RT/RW, mis: "Dusun Krajan, RT 001/RW 002, Balecatur, Kec. Gamping, Kabupaten Sleman, DI Yogyakarta".</p>
            </fieldset>

            <script>
            (function () {
                const endpoint = @json(route('admin.regions.children'));
                const chain = ['province_code', 'regency_code', 'district_code', 'village_code'];
                const placeholders = {
                    province_code: '— pilih provinsi —',
                    regency_code: '— pilih kabupaten/kota —',
                    district_code: '— pilih kecamatan —',
                    village_code: '— pilih kelurahan/desa —'
                };

                function resetFrom(index) {
                    for (let i = index; i < chain.length; i++) {
                        const el = document.getElementById(chain[i]);
                        el.innerHTML = '<option value="">' + placeholders[chain[i]] + '</option>';
                        el.disabled = true;
                    }
                }

                async function load(index, parent, selected) {
                    const el = document.getElementById(chain[index]);
                    const url = parent ? endpoint + '?parent=' + encodeURIComponent(parent) : endpoint;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const items = await res.json();
                    el.innerHTML = '<option value="">' + placeholders[chain[index]] + '</option>';
                    items.forEach(function (item) {
                        const opt = document.createElement('option');
                        opt.value = item.kode;
                        opt.textContent = item.nama;
                        el.appendChild(opt);
                    });
                    el.disabled = false;
                    if (selected) {
                        el.value = selected;
                        if (el.value === selected && index + 1 < chain.length) {
                            await load(index + 1, selected, document.getElementById(chain[index + 1]).dataset.selected || '');
                        }
                    }
                }

                chain.forEach(function (id, index) {
                    document.getElementById(id).addEventListener('change', function () {
                        if (index + 1 >= chain.length) return;
                        resetFrom(index + 1);
                        if (this.value) load(index + 1, this.value, '');
                    });
                });

                load(0, '', document.getElementById('province_code').dataset.selected || '');
            })();
            </script>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-stone-700">Password {{ $user->exists ? '(kosongkan bila tidak diganti)' : '' }}</label>
                    <input id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }} autocomplete="new-password"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-stone-700">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</a>
        </div>
    </form>
@endsection
