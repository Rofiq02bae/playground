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

            <div>
                <label for="address" class="mb-1.5 block text-sm font-medium text-stone-700">Alamat (sesuai identitas)</label>
                <textarea id="address" name="address" rows="2"
                          class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('address', $user->address) }}</textarea>
                @error('address')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

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
