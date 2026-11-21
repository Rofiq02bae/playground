{{-- Lacak paket Biteship — dipakai halaman detail pesanan pembeli & admin. Butuh $trackingUrl (endpoint JSON). --}}
<section class="rounded-2xl border border-stone-200 bg-white p-5">
    <div class="mb-3 flex items-center justify-between gap-3">
        <h2 class="font-semibold text-stone-900">Lacak Paket</h2>
        <button type="button" id="tracking-btn" class="shrink-0 rounded-xl border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Lacak Sekarang</button>
    </div>
    <p id="tracking-status" class="text-sm text-stone-500">Lihat posisi paket dari kurir berdasarkan nomor resi.</p>
    <div id="tracking-result" class="mt-4 hidden">
        <p class="text-sm text-stone-600">Status terakhir: <span id="tracking-current" class="font-bold text-emerald-800"></span></p>
        <ol id="tracking-history" class="mt-4 space-y-4"></ol>
    </div>
    <script>
        (function () {
            var btn = document.getElementById('tracking-btn');
            var status = document.getElementById('tracking-status');
            var result = document.getElementById('tracking-result');
            var current = document.getElementById('tracking-current');
            var list = document.getElementById('tracking-history');
            if (!btn) return;
            btn.addEventListener('click', async function () {
                btn.disabled = true;
                status.textContent = 'Menghubungi kurir...';
                status.className = 'text-sm text-stone-500';
                try {
                    var res = await fetch(@json($trackingUrl), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    var data = await res.json();
                    if (!res.ok || data.error) {
                        status.textContent = data.error || 'Pelacakan belum tersedia.';
                        status.className = 'text-sm text-red-600';
                        result.classList.add('hidden');
                        return;
                    }
                    current.textContent = data.status_label;
                    list.innerHTML = '';
                    if (!data.history.length) {
                        var empty = document.createElement('li');
                        empty.className = 'text-sm text-stone-500';
                        empty.textContent = 'Belum ada riwayat perjalanan dari kurir.';
                        list.appendChild(empty);
                    }
                    data.history.forEach(function (h, i) {
                        var li = document.createElement('li');
                        li.className = 'flex gap-3';
                        var dot = document.createElement('span');
                        dot.className = 'mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full ' + (i === 0 ? 'bg-emerald-600' : 'bg-stone-300');
                        var body = document.createElement('div');
                        var note = document.createElement('p');
                        note.className = 'text-sm font-medium text-stone-800';
                        note.textContent = h.note || h.status_label;
                        var meta = document.createElement('p');
                        meta.className = 'text-xs text-stone-400';
                        var when = h.updated_at ? new Date(h.updated_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
                        meta.textContent = h.status_label + (when ? ' · ' + when : '');
                        body.appendChild(note);
                        body.appendChild(meta);
                        li.appendChild(dot);
                        li.appendChild(body);
                        list.appendChild(li);
                    });
                    result.classList.remove('hidden');
                    status.textContent = 'Data dari kurir untuk resi ' + data.waybill + '.';
                    btn.textContent = 'Perbarui';
                } catch (e) {
                    status.textContent = 'Gagal memuat pelacakan. Coba lagi.';
                    status.className = 'text-sm text-red-600';
                } finally { btn.disabled = false; }
            });
        })();
    </script>
</section>
