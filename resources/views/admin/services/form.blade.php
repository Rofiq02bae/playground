@extends('admin.layouts.app')

@section('title', ($service->exists ? 'Ubah' : 'Tambah') . ' Layanan - Admin FunFarm')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">{{ $service->exists ? 'Ubah Layanan & Harga' : 'Layanan Baru' }}</h1>
        <p class="text-sm text-stone-500">Harga member adalah harga setelah potongan membership. Perubahan harga hanya berlaku untuk pencatatan baru (yang lama sudah ter-snapshot).</p>
    </div>

    <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" class="max-w-2xl rounded-2xl border border-stone-200 bg-white p-6">
        @csrf
        @if ($service->exists) @method('PUT') @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama layanan / alat</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Cabang</label>
                <select name="branch" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                    <option value="pertanian" @selected(old('branch', $service->branch) === 'pertanian')>Pertanian</option>
                    <option value="peternakan" @selected(old('branch', $service->branch) === 'peternakan')>Peternakan</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Satuan tarif</label>
                <input type="text" name="unit" value="{{ old('unit', $service->unit) }}" required placeholder="ha, hari, unit"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('unit')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Harga normal (Rp / satuan)</label>
                <input type="number" name="price_normal" value="{{ old('price_normal', $service->price_normal) }}" min="0" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('price_normal')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Harga member (Rp / satuan)</label>
                <input type="number" name="price_member" value="{{ old('price_member', $service->price_member) }}" min="0" required
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('price_member')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-stone-700">Deskripsi (opsional)</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('description', $service->description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-700">
                <input type="checkbox" name="is_active" value="1" class="rounded border-stone-300" @checked(old('is_active', $service->is_active))> Layanan aktif
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
            <a href="{{ route('admin.services.index') }}" class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</a>
        </div>
    </form>
@endsection
