# FunFarm Fase 3 — Marketplace (Scope Terkunci)

Keputusan user 2026-11-21. Marketplace diakses publik dari tombol **"Produk"** di navbar blog;
admin mengelola dari dashboard admin yang sama (menu induk **Marketplace** dengan child Produk & Pesanan).

## Keputusan utama

1. **Arsitektur: aplikasi marketplace terpisah** (pola sama seperti WebApp) — area `/produk`
   dengan layout sendiri: header berisi pencarian, keranjang, tombol Login/Akun.
2. **Pembeli: publik boleh membeli.** Eksplor + keranjang TANPA login; **checkout wajib login**
   agar data identitas & alamat pengiriman pembeli lengkap. Perlu **registrasi mandiri** dengan
   role baru `pembeli`. Keranjang tersimpan di session dan tidak hilang setelah login.
3. **Pembayaran: Midtrans Snap** (sandbox dulu; Server Key & Client Key via jalur aman).
   Status resmi via webhook bila ada URL publik; selama lokal/sandbox: tombol "Cek Status Pembayaran".
   Alur status pesanan: menunggu_pembayaran → dibayar → diproses → dikirim → selesai;
   gagal/kedaluwarsa → dibatalkan (stok kembali).
4. **Menu admin**: satu menu induk "Marketplace" di sidebar dengan child **Produk** dan
   **Pesanan** (badge pesanan menunggu/menunggu pembayaran di child Pesanan).
5. **Kategori hanya 2: Pertanian & Peternakan.** Admin bisa menambah produk lagi lewat CRUD.
6. **Tipe produk: `barang` dan `jasa`.** Jasa tidak dihitung ongkir dan tidak dikirim.
7. **Produk awal (seed, harga bisa diedit admin)**:
   - Jasa Semprot Drone — Pertanian, jasa, Rp300.000/ha (mengikuti tarif normal master layanan Fase 2).
   - Jasa Mesin Tanam — Pertanian, jasa, Rp400.000/ha (tarif normal Fase 2).
   - Alat Penetas Telur — Peternakan, barang, harga seed contoh (diisi saat bangun, editable).
8. **Hasil panen → produk (opt-in, DIPERBARUI user 2026-11-21)**: tombol **"Jual Hasil Panen"**
   muncul hanya bila **proyek berstatus selesai** dan punya catatan panen. Sekali klik membuat
   produk kategori Pertanian bertipe barang: stok = total kg seluruh catatan panen proyek,
   harga awal = rata-rata tertimbang harga panen (bisa diedit admin). Produk terhubung ke proyeknya.
9. **Pengiriman: Biteship (FINAL, dipilih user 2026-11-21)** — satu integrasi penuh:
   rates + order + resi + tracking + webhook.
   Alamat checkout memakai ulang data wilayah Fase 2 (dropdown provinsi → desa).
   Arsitektur: logika pengiriman dibungkus satu class `App\Support\Shipping` agar penyedia bisa diganti.

## Keputusan Tahap 3.2 (user, 2026-11-21)

1. **Berat paket: sementara pakai berat minimum Biteship** (config `biteship.default_weight_gram`
   = 1000) untuk semua hitungan ongkir — BELUM ada field berat per produk.
   **PENGINGAT untuk user: ubah ke berat asli per produk setelah 3.2 jadi.**
2. **Satuan jasa = meter persegi (m²)**, bukan hektar desimal. Harga seed jasa dikonversi:
   drone Rp30/m² (= Rp300.000/ha), mesin tanam Rp40/m² (= Rp400.000/ha). Jumlah barang & jasa
   sama-sama bilangan bulat.
3. **Stok dipotong saat pesanan dibuat**, kembali otomatis bila batal/kedaluwarsa; batas bayar 24 jam.
4. **Jasa tidak dihitung ongkir dan tidak ada pengiriman** — pengiriman hanya untuk barang.
   Keranjang campuran boleh; ongkir hanya dari barang.
5. **Resi otomatis dari Biteship** saat admin mengirim; fallback input resi manual.
6. **Admin DIKECUALIKAN dari membeli** — checkout hanya untuk pembeli, pemilik, penggarap.
7. Alamat asal pengiriman default: alamat perusahaan (config/perjanjian.php) kode pos 55762,
   dapat dioverride lewat .env BITESHIP_ORIGIN_*.

## Tahapan bangun

- **3.1 Fondasi katalog** — migrasi `products` (+ foto upload), CRUD admin di bawah menu
  Marketplace, seed 3 produk awal, area publik `/produk` + detail, tombol Produk di navbar blog,
  registrasi/login pembeli (role `pembeli`) + layout marketplace.
- **3.2 Keranjang & checkout** — cart session, halaman keranjang, checkout wajib login
  (alamat dari profil + dropdown wilayah), pilihan kurir & ongkir dari aggregator,
  pesanan tersimpan, Midtrans Snap, halaman status pesanan pembeli, admin child Pesanan
  (konfirmasi/proses/kirim/selesai + resi).
- **3.3 Hasil panen jadi produk** — tombol Jual Hasil Panen di proyek selesai (admin),
  produk terhubung proyek, stok mengikuti total panen.
- **Di luar scope v1**: harga member di marketplace (nanti; akun pemilik member sudah ada),
  voucher/promo, rating/ulasan, multi-foto galeri, ongkir internasional.

## Kemajuan

- **3.1 SELESAI 2026-11-21** — tabel `products`, CRUD admin di bawah menu induk Marketplace
  (child Produk; child Pesanan menyusul di 3.2), seed 3 produk awal, area publik `/produk`
  + detail, tombol Produk di navbar blog, registrasi/login pembeli (role `pembeli`,
  webapp pencatatan kini menolak role pembeli). Terverifikasi 43/43 cek HTTP.
- **3.2 SELESAI 2026-11-21** — keranjang session, checkout wajib login (admin dikecualikan),
  ongkir Biteship diverifikasi ulang di server, Midtrans Snap + tombol Cek Status + webhook
  ber-signature, stok terpotong saat pesanan dibuat & kembali saat batal/kedaluwarsa,
  admin child Pesanan penuh (proses/kirim resi otomatis/manual/selesai/batal + badge).
  Terverifikasi 46/46 cek HTTP memakai stub lokal Midtrans & Biteship (kunci asli user
  belum diberikan — integrasi membaca base URL & kunci dari config/.env).
  SISA PENGINGAT: berat paket masih berat minimum 1000 gram (lihat Keputusan 3.2 poin 1).

## Catatan data

- Tabel baru: `products` (name, slug, category[pertanian|peternakan], type[barang|jasa],
  price, stock, unit, photo_path, description, status[draft|aktif|arsip], harvest_project_id nullable).
- Tabel baru: `orders` + `order_items` (snapshot nama/harga produk saat checkout),
  orders memuat: user_id, status, subtotal, shipping_cost, total, courier + service + resi,
  alamat pengiriman (snapshot), midtrans order id + status pembayaran.
- Role baru `pembeli` di users.role; login satu pintu tetap, redirect pembeli → marketplace.
