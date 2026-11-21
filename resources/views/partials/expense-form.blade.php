{{-- Form tambah pencatatan proyek. Variabel: $project, $categories, $services, $action --}}
<form method="POST" action="{{ $action }}" class="rounded-2xl border border-stone-200 bg-white p-5">
    @csrf
    <h3 class="font-semibold text-stone-900">Tambah Pencatatan</h3>
    <p class="mt-0.5 text-xs text-stone-500">Untuk sewa drone / mesin tanam, harga satuan otomatis mengikuti tarif resmi &amp; status membership pemilik ({{ $project->owner->name }}: {{ $project->owner->membershipLabel() }}).</p>

    <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Kategori</label>
            <select name="category" required class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
            @error('date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama item / kegiatan</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="cth: Pupuk Urea, Semprot pestisida"
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Jenis (opsional)</label>
            <input type="text" name="item_type" value="{{ old('item_type') }}" placeholder="cth: NPK, herbisida"
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Jumlah</label>
            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" step="0.01" min="0.01" required
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
            @error('quantity')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Satuan</label>
            <input type="text" name="unit" value="{{ old('unit', 'unit') }}" placeholder="sak, botol, ha, HOK, kg"
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Harga satuan (Rp)</label>
            <input type="number" name="unit_price" value="{{ old('unit_price', 0) }}" min="0" step="1"
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
            <p class="mt-1 text-xs text-stone-400">Kosongkan/diabaikan untuk kategori sewa (tarif resmi yang dipakai).</p>
            @error('unit_price')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Keterangan (opsional)</label>
            <input type="text" name="notes" value="{{ old('notes') }}"
                   class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
        </div>
    </div>

    <button type="submit" class="mt-5 w-full rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800 active:bg-emerald-900 sm:w-auto">Simpan Pencatatan</button>
</form>
