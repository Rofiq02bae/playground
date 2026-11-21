@extends('shop.layouts.app')

@section('title', 'Checkout - Produk FunFarm')

@section('content')
    <h1 class="mb-5 text-xl font-bold text-stone-900 sm:text-2xl">Checkout</h1>

    @error('cart')<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('shop.checkout.store') }}" id="checkout-form">
        @csrf
        <div class="grid gap-5 lg:grid-cols-5">
            <div class="space-y-5 lg:col-span-3">
                <section class="rounded-2xl border border-stone-200 bg-white p-5">
                    <h2 class="mb-4 font-semibold text-stone-900">Penerima &amp; Alamat Tujuan</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Nama penerima</label>
                            <input type="text" name="recipient_name" value="{{ $prefill['recipient_name'] }}" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                            @error('recipient_name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">No. WhatsApp / HP penerima</label>
                            <input type="tel" name="recipient_phone" value="{{ $prefill['recipient_phone'] }}" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                            @error('recipient_phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Provinsi</label>
                            <select name="province_code" id="f-province" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                                <option value="">— Pilih provinsi —</option>
                                @foreach ($provinces as $prov)
                                    <option value="{{ $prov->kode }}" @selected($prefill['province_code'] === $prov->kode)>{{ $prov->nama }}</option>
                                @endforeach
                            </select>
                            @error('province_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Kabupaten/Kota</label>
                            <select name="regency_code" id="f-regency" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600"><option value="">— Pilih provinsi dulu —</option></select>
                            @error('regency_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Kecamatan</label>
                            <select name="district_code" id="f-district" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600"><option value="">— Pilih kabupaten dulu —</option></select>
                            @error('district_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Kelurahan/Desa</label>
                            <select name="village_code" id="f-village" required class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600"><option value="">— Pilih kecamatan dulu —</option></select>
                            @error('village_code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Dusun (opsional)</label>
                            <input type="text" name="hamlet" value="{{ $prefill['hamlet'] }}" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700">RT</label>
                                <input type="text" name="rt" value="{{ $prefill['rt'] }}" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700">RW</label>
                                <input type="text" name="rw" value="{{ $prefill['rw'] }}" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-stone-700">Kode Pos</label>
                                <input type="text" name="postal_code" id="f-postal" value="{{ $prefill['postal_code'] }}" required inputmode="numeric" maxlength="5" placeholder="55762" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">
                            </div>
                        </div>
                        @error('postal_code')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-stone-700">Catatan untuk penjual (opsional)</label>
                            <textarea name="notes" rows="2" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600">{{ $prefill['notes'] }}</textarea>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-stone-400">Alamat ini tersimpan di profilmu untuk checkout berikutnya.</p>
                </section>

                @if ($hasBarang)
                    <section class="rounded-2xl border border-stone-200 bg-white p-5">
                        <h2 class="mb-1 font-semibold text-stone-900">Pilihan Kurir</h2>
                        <p class="mb-3 text-xs text-stone-400">Ongkir dihitung Biteship dari gudang perusahaan (Bantul) ke kode pos tujuanmu. Jasa dalam keranjang tidak dihitung ongkir.</p>
                        <button type="button" id="btn-rates" class="rounded-xl border border-emerald-700 px-4 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50">Cek Pilihan Kurir &amp; Ongkir</button>
                        <p id="rates-status" class="mt-2 text-sm text-stone-500">Isi kode pos dulu, lalu cek kurir.</p>
                        <div id="rates-list" class="mt-3 space-y-2"></div>
                        @error('courier_key')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </section>
                @else
                    <section class="rounded-2xl border border-sky-200 bg-sky-50 p-5 text-sm text-sky-900">
                        Keranjangmu berisi <strong>jasa</strong> saja — tidak ada pengiriman barang. Jadwal pengerjaan layanan akan diatur perusahaan setelah pembayaran terkonfirmasi.
                    </section>
                @endif
            </div>

            <aside class="lg:col-span-2">
                <div class="rounded-2xl border border-stone-200 bg-white p-5 lg:sticky lg:top-32">
                    <h2 class="mb-3 font-semibold text-stone-900">Ringkasan Belanja</h2>
                    <ul class="space-y-2.5 text-sm">
                        @foreach ($items as $item)
                            <li class="flex justify-between gap-3">
                                <span class="text-stone-600">{{ $item['product']->name }} <span class="text-stone-400">× {{ number_format($item['quantity'], 0, ',', '.') }} {{ $item['product']->unit }}</span></span>
                                <span class="font-medium text-stone-900">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 space-y-1.5 border-t border-stone-100 pt-3 text-sm">
                        <div class="flex justify-between"><span class="text-stone-500">Subtotal</span><span class="font-medium">Rp{{ number_format($subtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-stone-500">Ongkos kirim</span><span class="font-medium" id="summary-shipping">{{ $hasBarang ? '— pilih kurir —' : 'Rp0' }}</span></div>
                        <div class="flex justify-between border-t border-stone-100 pt-2 text-base font-bold text-stone-900"><span>Total</span><span id="summary-total">Rp{{ number_format($subtotal, 0, ',', '.') }}</span></div>
                    </div>
                    <button type="submit" class="mt-4 w-full rounded-xl bg-emerald-700 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">Buat Pesanan &amp; Bayar</button>
                    <p class="mt-2 text-center text-xs text-stone-400">Pembayaran via Midtrans (VA bank, e-wallet, kartu, retail). Batas bayar 24 jam; stok dipotong sekarang dan kembali otomatis bila pesanan kedaluwarsa.</p>
                </div>
            </aside>
        </div>
    </form>

    <script>
    (function () {
        const regionUrl = @json(route('shop.regions.children'));
        const sel = {
            province: document.getElementById('f-province'),
            regency: document.getElementById('f-regency'),
            district: document.getElementById('f-district'),
            village: document.getElementById('f-village'),
        };
        const prefill = {
            regency: @json($prefill['regency_code']),
            district: @json($prefill['district_code']),
            village: @json($prefill['village_code']),
        };
        const placeholders = {
            regency: '— Pilih kabupaten/kota —', district: '— Pilih kecamatan —', village: '— Pilih kelurahan/desa —',
        };
        async function loadChildren(select, parent, selected) {
            select.innerHTML = '<option value="">Memuat...</option>';
            if (!parent) { select.innerHTML = '<option value="">' + (placeholders[select.id.replace('f-', '')] || '— Pilih —') + '</option>'; return; }
            const res = await fetch(regionUrl + '?parent=' + encodeURIComponent(parent), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            select.innerHTML = '<option value="">' + (placeholders[select.id.replace('f-', '')] || '— Pilih —') + '</option>';
            data.forEach(function (r) {
                const opt = document.createElement('option');
                opt.value = r.kode; opt.textContent = r.nama;
                if (selected && r.kode === selected) opt.selected = true;
                select.appendChild(opt);
            });
        }
        async function cascade() {
            await loadChildren(sel.regency, sel.province.value, prefill.regency);
            await loadChildren(sel.district, sel.regency.value, prefill.district);
            await loadChildren(sel.village, sel.district.value, prefill.village);
        }
        sel.province.addEventListener('change', async function () { prefill.regency = prefill.district = prefill.village = null; await loadChildren(sel.regency, sel.province.value, null); sel.district.innerHTML = '<option value="">— Pilih kabupaten dulu —</option>'; sel.village.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>'; });
        sel.regency.addEventListener('change', async function () { prefill.district = prefill.village = null; await loadChildren(sel.district, sel.regency.value, null); sel.village.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>'; });
        sel.district.addEventListener('change', async function () { prefill.village = null; await loadChildren(sel.village, sel.district.value, null); });
        if (sel.province.value) cascade();

        // ---- Ongkir Biteship ----
        const hasBarang = @json($hasBarang);
        if (!hasBarang) return;
        const btn = document.getElementById('btn-rates');
        const status = document.getElementById('rates-status');
        const list = document.getElementById('rates-list');
        const postal = document.getElementById('f-postal');
        const subtotal = {{ $subtotal }};
        const rupiah = (n) => 'Rp' + n.toLocaleString('id-ID');
        function setTotal(shipping) {
            document.getElementById('summary-shipping').textContent = rupiah(shipping);
            document.getElementById('summary-total').textContent = rupiah(subtotal + shipping);
        }
        btn.addEventListener('click', async function () {
            const code = postal.value.trim();
            if (!/^\d{5}$/.test(code)) { status.textContent = 'Kode pos harus 5 digit angka.'; status.className = 'mt-2 text-sm text-red-600'; return; }
            btn.disabled = true; status.className = 'mt-2 text-sm text-stone-500'; status.textContent = 'Menghubungi Biteship...';
            list.innerHTML = '';
            try {
                const res = await fetch(@json(route('shop.checkout.rates')), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
                    body: JSON.stringify({ postal_code: code }),
                });
                const data = await res.json();
                if (!res.ok || data.error) { status.textContent = data.error || 'Ongkir belum bisa dihitung.'; status.className = 'mt-2 text-sm text-red-600'; return; }
                if (!data.rates.length) { status.textContent = 'Tidak ada kurir yang melayani alamat ini.'; status.className = 'mt-2 text-sm text-red-600'; return; }
                status.textContent = 'Pilih salah satu kurir:';
                data.rates.forEach(function (r, i) {
                    const label = document.createElement('label');
                    label.className = 'flex cursor-pointer items-center gap-3 rounded-xl border border-stone-300 px-3.5 py-3 text-sm transition hover:border-emerald-500';
                    label.innerHTML = '<input type="radio" name="courier_key" value="' + r.key + '" class="accent-emerald-700" ' + (i === 0 ? 'checked' : '') + '>' +
                        '<span class="flex-1"><span class="font-semibold text-stone-900">' + r.company + '</span> <span class="text-stone-500">' + r.service + '</span>' +
                        (r.duration ? '<span class="block text-xs text-stone-400">Estimasi: ' + r.duration + '</span>' : '') + '</span>' +
                        '<span class="font-bold text-emerald-800">' + rupiah(r.price) + '</span>';
                    label.querySelector('input').addEventListener('change', function () { setTotal(r.price); });
                    list.appendChild(label);
                });
                setTotal(data.rates[0].price);
            } catch (e) {
                status.textContent = 'Gagal memuat ongkir. Coba lagi.'; status.className = 'mt-2 text-sm text-red-600';
            } finally { btn.disabled = false; }
        });
        postal.addEventListener('input', function () { list.innerHTML = ''; status.className = 'mt-2 text-sm text-stone-500'; status.textContent = 'Kode pos berubah — cek kurir lagi.'; document.getElementById('summary-shipping').textContent = '— pilih kurir —'; document.getElementById('summary-total').textContent = rupiah(subtotal); });
    })();
    </script>
@endsection
