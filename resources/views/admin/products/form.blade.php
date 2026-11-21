{{-- Form produk marketplace (dipakai create & edit). Butuh: $product, $categories, $types, $statuses, $action, $method --}}
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="rounded-2xl border border-stone-200 bg-white p-5">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama produk</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Kategori</label>
            <select name="category" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', $product->category) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Tipe</label>
            <select name="type" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $product->type) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-stone-400">Jasa tidak memakai stok dan tidak dihitung ongkir.</p>
            @error('type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Harga (Rp)</label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="1" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            @error('price')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Satuan</label>
            <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required placeholder="kg, ha, unit, pcs" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            @error('unit')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Stok (khusus barang)</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" step="1" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            <p class="mt-1 text-xs text-stone-400">Kosongkan untuk jasa. Barang tanpa stok dianggap habis.</p>
            @error('stock')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Status</label>
            <select name="status" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $product->status) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-stone-400">Hanya status Aktif yang tampil di toko.</p>
            @error('status')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Foto produk</label>
            @if ($product->photo_url)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $product->photo_url }}" alt="Foto {{ $product->name }}" class="h-16 w-16 rounded-xl border border-stone-200 object-cover">
                    <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" name="remove_photo" value="1" class="rounded border-stone-300"> Hapus foto saat ini</label>
                </div>
            @endif
            <input type="file" name="photo" accept="image/*" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
            <p class="mt-1 text-xs text-stone-400">JPG/PNG, maks 2 MB. Kosongkan bila tidak diganti.</p>
            @error('photo')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-stone-700">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">{{ old('description', $product->description) }}</textarea>
            @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="mt-5 flex gap-2">
        <button type="submit" class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Produk</button>
        <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Batal</a>
    </div>
</form>
