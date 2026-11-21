# FunFarm Platform — Fase 1: Blog + Fase 2: Service (fondasi RBAC & webapp pencatatan)

Aplikasi Laravel 13 + Blade + Tailwind CSS (Vite) dengan database SQLite.
Spec lengkap project ada di `README.md` (dokumen FunFarm Platform).
Fase 1 mencakup **sisi viewer blog** dan **sisi updater/admin** dengan
block editor drag & drop untuk menyusun isi artikel.

## Rute viewer

| URL | Isi |
|---|---|
| `/` | Redirect ke `/blog` |
| `/blog` | Landing blog: artikel terbaru + testimonial |
| `/blog?show=all` | Semua artikel (pagination, 9 per halaman) |
| `/blog/search?q=...` | Pencarian artikel (judul, ringkasan, isi) |
| `/blog/category/{slug}` | Artikel per kategori |
| `/blog/{slug}` | Detail artikel + artikel terkait |

Hanya artikel berstatus `published` yang tampil; draft tidak muncul
di daftar dan detailnya mengembalikan 404.

Grid artikel hanya dirender bila ada artikel; bila hasil kosong yang
tampil adalah pesan *empty state* — tidak ada card/grid kosong. Definisi
grid dan class `blog-*` lainnya ada di `resources/css/app.css`.

## Menjalankan

Syarat: PHP 8.3+, Composer, Node.js (untuk build aset Vite — layout
memakai `@vite`, jadi langkah build wajib agar halaman tidak error
"Vite manifest not found").

```bash
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # bila file belum ada
php artisan migrate:fresh --seed
php artisan storage:link         # agar foto upload bisa diakses publik
php artisan serve
```

Lalu buka http://127.0.0.1:8000/blog

Database default adalah SQLite (`DB_CONNECTION=sqlite` di `.env`),
file-nya di `database/database.sqlite`. Seeder membuat 4 kategori dan
5 artikel dummy.

## Struktur penting

| Bagian | Lokasi |
|---|---|
| Controller viewer | `app/Http/Controllers/BlogController.php` |
| Model | `app/Models/Article.php`, `app/Models/ArticleCategory.php` |
| Routing | `routes/web.php` (grup `blog.`) |
| Template Blade | `resources/views/blog/` (layout, index, category, show, partials) |
| Komponen Blade | `resources/views/components/blog/` (`article-card`, `section-heading`, `review-card`) |
| CSS (class `blog-*`) | `resources/css/app.css` |
| JS (toggle menu mobile) | `resources/js/app.js` |
| Migrasi | `database/migrations/*create_article_categories_table.php`, `*create_articles_table.php` |
| Seeder 5 artikel dummy | `database/seeders/ArticleSeeder.php` |

## Catatan model Article

Selain relasi `category()` dan scope `published()`, model menyediakan
accessor yang dipakai view:

- `thumbnail_url` — URL thumbnail siap pakai, `null` bila tidak ada
- `meta_description_value` — ringkasan maks. 160 karakter untuk meta/OG
- `safe_content` — isi artikel sebagai HTML aman untuk `{!! !!}`
  (teks polos diubah jadi paragraf; HTML disaring dari tag/atribut berbahaya)
- `reading_time` — estimasi menit baca

## Skema artikel

Tabel `articles`: `title`, `slug` (otomatis dari judul, unik), `excerpt`,
`content`, `thumbnail` (nullable), `status` (`draft`/`published`),
`published_at`, dan relasi `article_category_id` ke `article_categories`.

## Admin (updater) — block editor

Login di **http://127.0.0.1:8000/admin/login**

Akun awal dari seeder (ganti password-nya setelah login pertama di
lingkungan nyata):

- Email: `admin@funfarm.id`
- Password: `admin123`

Fitur admin:

- CRUD artikel lengkap (judul, slug otomatis/manual, kategori, status
  draft/published, ringkasan, foto sampul dengan upload)
- **Block editor ala Gutenberg**: isi artikel ditulis langsung di canvas
  yang tampilannya mirip artikel asli. Block bisa ditambah (tombol + di
  tiap block atau tombol di atas), dihapus, diduplikat, dan digeser
  urutannya (drag & drop lewat handle, atau tombol naik/turun).
  Tipe block: **Judul** (H2/H3), **Paragraf**, **Foto** (upload file atau
  URL + keterangan + lebar normal/penuh), **List** (bullet/penomoran,
  satu item per baris), **Kutipan** (teks + sumber), **Kode**,
  **Video** (link YouTube/Vimeo, otomatis jadi embed), **Garis Pemisah**.
  Paragraf, judul, dan kutipan mendukung rich text: sorot teks untuk
  **bold**, *italic*, underline, dan link — HTML-nya disanitasi di server
  (hanya format inline aman yang disimpan).
- Block disimpan sebagai JSON di kolom `articles.blocks`. Teks polosnya
  dibangkitkan otomatis ke kolom `content` untuk pencarian & meta.
- Styling block di viewer dikunci oleh CSS (`.article-content`), jadi
  semua artikel tetap tampil seragam ala halaman artikel PetaniKode.
- Artikel lama tanpa block (mis. dari seeder) tetap tampil — viewer
  memakai fallback render dari kolom `content`.

| Bagian admin | Lokasi |
|---|---|
| Controller | `app/Http/Controllers/Admin/ArticleController.php`, `AuthController.php` |
| View admin | `resources/views/admin/` |
| Block editor JS | `resources/js/admin-editor.js` (SortableJS) |
| Render block di viewer | `resources/views/blog/blocks/` |

## Fase 2 — Fondasi RBAC & WebApp Pencatatan

Satu database dan satu tabel `users` untuk blog + webapp, dibedakan
dengan kolom `role` (RBAC, middleware `role:`):

| Role | Akses |
|---|---|
| `admin` (perusahaan) | Semua akses: blog admin, webapp, kelola pengguna, approval data |
| `pemilik` (pemilik sawah) | Webapp — hanya melihat data |
| `penggarap` (penggarap sawah) | Webapp — melihat + menambah data (menunggu approval perusahaan) |

- Login satu pintu di `/admin/login`, bisa pakai **email atau username**.
  Setelah login: admin diarahkan ke panel admin, pemilik/penggarap ke
  webapp `/app` (tampilan mobile).
- Akun pemilik/penggarap **dibuatkan perusahaan** dari menu
  **Pengguna** di panel admin (`/admin/users`) — tidak ada pendaftaran
  mandiri. Saat membuat akun pemilik, admin mengecek identitas customer
  dulu lalu menetapkan statusnya lewat pilihan **Membership** /
  **Bukan Membership** (membership = potongan harga sewa; sumbernya dari
  langganan WiFi atau bayar sendiri, tidak dibedakan di sistem ini dan
  database WiFi tidak terhubung ke sini).
- Migrasi Fase 2 menambahkan ke `users`: `role`, `username`, `phone`,
  `address`, `is_member`, dan membuat `email` boleh kosong.

Akun demo dari seeder (untuk uji role; ganti/hapus di lingkungan nyata):

| Username | Password | Role | Membership |
|---|---|---|---|
| `admin` / `admin@funfarm.id` | `admin123` | Admin (perusahaan) | — |
| `budi` | `pemilik123` | Pemilik | Membership |
| `sari` | `pemilik123` | Pemilik | Bukan Membership |
| `joko` | `penggarap123` | Penggarap | — |

Setelah `git pull` versi ini, jalankan ulang:

```bash
composer install
npm install
npm run build
php artisan migrate --seed
```

Tahap berikutnya: kalender tanam/panen, laporan biaya produksi
(print), dan generate surat perjanjian (pemilik ↔ perusahaan).
Showcase sewa ditunda sampai landing page root dibuat; katalog produk
unggulan di-skip dulu.

## Fase 2 — Alamat pengguna: dropdown wilayah bertingkat

Form tambah/edit pengguna (`/admin/users`) memakai dropdown alamat
bertingkat: **Provinsi -> Kabupaten/Kota -> Kecamatan -> Kelurahan/Desa**,
lalu isian teks **Dusun** dan **RT/RW** di bawahnya. Alamat lengkap
tersimpan terkomposisi otomatis, mis:
"Dusun Krajan, RT 001/RW 002, Balecatur, Kec. Gamping, Kabupaten Sleman,
Daerah Istimewa Yogyakarta".

- Data wilayah: tabel `regions` (91.599 baris — 38 provinsi,
  514 kabupaten/kota, 7.285 kecamatan, 83.762 kelurahan/desa), kode
  hierarkis bertitik (`34`, `34.04`, `34.04.01`, `34.04.01.2001`).
- Sumber data: cahyadsn/wilayah (Kepmendagri No 300.2.2-2138 Tahun 2025),
  dikonversi ke `database/seeders/data/wilayah.csv`, diimpor oleh
  `RegionSeeder` (otomatis jalan lewat `php artisan migrate:fresh --seed`
  atau `php artisan db:seed --class=RegionSeeder`).
- Dropdown diisi lewat endpoint JSON `GET /admin/regions/children?parent=<kode>`
  (khusus admin).

## Fase 2 — Lokasi sawah di peta (Leaflet + Geoman)

Di form proyek (`/admin/projects`), field **Koordinat lokasi sawah**
bersifat readonly: klik field / tombol **Pilih di Peta** untuk membuka
popup peta (full-screen di HP). Alurnya:

1. Popup berisi peta satelit **Esri World Imagery** (gratis, tanpa API
   key) + pilihan layer Peta Jalan (OSM), tombol **Posisi Saya (GPS)**.
2. Gambar **poligon** batas sawah atau taruh **pin** (Leaflet-Geoman),
   bisa diedit/digeser/dihapus.
3. Klik **Gunakan Lokasi Ini**: latitude & longitude (titik tengah
   poligon, atau koordinat pin) otomatis masuk ke field koordinat form.
   Sesuai keputusan desain: luas sawah TIDAK dihitung otomatis dari
   poligon — field luas tetap diisi manual.
4. Poligon tersimpan sebagai GeoJSON di kolom `boundary_geojson`
   (divalidasi server: harus poligon & koordinat di wilayah Indonesia),
   lalu tampil sebagai peta read-only di detail proyek admin dan
   detail sawah webapp.

Catatan teknis: aset peta adalah entry Vite terpisah
(`resources/js/field-map.js`, Leaflet 1.9 + Geoman) yang hanya dimuat
di halaman form/detail proyek — jangan lupa `npm install` lalu
`npm run build` setelah pull. Peta di-init saat popup pertama dibuka
+ `invalidateSize()` agar tile tidak rusak.

## Fase 2 — Kalender tanam & panen

| Rute | Isi |
|---|---|
| `/admin/kalender` | Kalender semua proyek (desktop perusahaan) |
| `/app/kalender` | Kalender webapp, scoped per role (pemilik: miliknya, penggarap: tugasnya, admin: semua) |

- Event murni **tanggal turunan** dari data yang ada: mulai tanam
  (`start_date`), estimasi panen (`estimated_harvest_date`), aktivitas
  pencatatan (tanggal expense). Tanpa library kalender JS — grid Blade
  + daftar agenda, navigasi `?bulan=YYYY-MM`.
- Expense **pending** tampil redup dengan badge "Menunggu approval"
  (tetap tidak dihitung di total resmi); expense ditolak tidak tampil;
  proyek dibatalkan tidak tampil, proyek selesai tampil redup.
- Strip **Panen Segera**: estimasi panen <= 7 hari ke depan untuk
  proyek berstatus berjalan / menunggu panen.
- Logika bersama di `App\Support\CalendarData`. Belum ada entri manual
  dan belum ada tanggal panen aktual — itu bagian modul laporan/panen.

## Fase 2 — Laporan biaya produksi & bagi hasil

| Rute | Isi |
|---|---|
| `/admin/laporan` | Rekap semua proyek: biaya, hasil panen, laba bersih, bagi hasil + total keseluruhan |
| `/admin/laporan/{project}` | Laporan per proyek siap cetak (tombol Cetak / Simpan PDF, layout print-friendly) |
| `/app/laporan` | Daftar laporan webapp — pemilik: proyeknya sendiri; penggarap: ditolak (403) |
| `/app/laporan/{project}` | Laporan per proyek di webapp (pemilik lain: 404) |

- **Data panen** dicatat admin di detail proyek (`/admin/projects/{id}`,
  bagian Hasil Panen): tanggal, hasil (kg), harga jual per kg — bisa
  **multi-panen/bertahap**, tersimpan di tabel `harvest_records`,
  `total_revenue = quantity_kg x unit_price`.
- **Aturan angka** (`App\Support\ProjectReport`): biaya = hanya expense
  **approved** (pending hanya info, tidak dihitung); laba bersih =
  total hasil panen − total biaya; bagi hasil dari laba bersih positif
  memakai `profit_share_company_percent` proyek — kalau belum ada laba,
  bagian perusahaan 0. Contoh README B.4 terverifikasi: biaya
  Rp3.400.000, panen 2.500 kg x Rp6.000 = Rp15.000.000, bersih
  Rp11.600.000, bagi hasil 50:50 = Rp5.800.000 per pihak (panen contoh
  ini ikut di-seed di proyek demo).

## Fase 2 — Pencatatan (proyek, sewa, pupuk/obat, approval)

| Rute | Siapa | Isi |
|---|---|---|
| `/admin/projects` | Admin | Daftar proyek & profil sawah + total biaya disetujui |
| `/admin/projects/{id}` | Admin | Detail proyek: rekap per kategori, form pencatatan, riwayat + tombol setujui/tolak |
| `/admin/approvals` | Admin | Antrean semua pencatatan pending dari penggarap |
| `/admin/services` | Admin | Master layanan & tarif (harga normal / member) |
| `/app/sawah` | Semua role | Daftar sawah sesuai akses (pemilik: miliknya, penggarap: tugasnya, admin: semua) |
| `/app/sawah/{id}` | Sesuai akses | Detail sawah + riwayat; form tambah hanya untuk penggarap yang ditugaskan & admin |

Aturan pencatatan:

- Kategori: sewa drone, sewa mesin tanam, pupuk, obat/pestisida,
  tenaga kerja, benih, transportasi, lainnya. Semua menempel ke proyek.
- **Sewa alat**: harga satuan tidak diinput manual — diambil dari tarif
  resmi di `/admin/services` mengikuti status membership pemilik sawah
  (member = harga member, bukan = harga normal), lalu di-snapshot ke
  catatan. Perubahan tarif tidak mengubah catatan lama.
- **Approval**: catatan dari perusahaan langsung `approved`; dari
  penggarap berstatus `pending` dan **tidak dihitung** di total/laporan
  sampai disetujui. Yang ditolak tersimpan dengan alasan dan tidak
  dihitung.
- Total resmi proyek = jumlah `total_price` catatan berstatus approved.

Seeder Fase 2 (`Fase2Seeder`) membuat 3 layanan dummy (Drone Semprot
Rp300rb/Rp250rb per ha, Mesin Tanam Rp400rb/Rp350rb per ha, Mesin
Penetas Telur Rp60rb/Rp50rb per hari) dan satu proyek demo
"Musim Tanam 1 - Sawah Budi" (pemilik Budi — member, penggarap Joko)
dengan catatan approved senilai **Rp3.400.000** sesuai contoh
rekapitulasi README Bagian B.4, plus 1 catatan pending dari penggarap
untuk mencoba alur persetujuan.

## Fase 2 — Jenis sewa proyek & surat perjanjian

Form proyek (`/admin/projects/create`) punya radio **Jenis sewa**:

- **Sewa Bagi Hasil** — tombol "Cek Kelengkapan Surat Perjanjian"
  membuka modal berisi daftar cek data surat (dari data pemilik +
  isian form) dan isian nama **Saksi 1** (Perangkat Desa / Ketua
  Kelompok Tani) & **Saksi 2** (Keluarga / Tetangga Batas Lahan).
  Bila kedua saksi terisi, PDF surat dibuat otomatis saat proyek
  disimpan; bila belum, surat bisa dibuat menyusul dari bagian
  "Surat Perjanjian" di detail proyek. PDF tersimpan di
  `storage/app/private/agreements/` dan bisa **diunduh kapan pun**
  dari detail proyek (satu proyek = satu surat, nomor tidak berubah).
  - Data perusahaan (PIHAK KEDUA: PT SARANA AKSES ADIKARYA) dan
    **format nomor surat diatur di `config/perjanjian.php`** — ubah
    di file itu bila template perusahaan berubah.
  - PDF: kertas A4, font Times (12pt, justify), library dompdf.
  - NIK & pekerjaan pemilik **opsional** (field di form pengguna):
    barisnya tampil di surat hanya bila terisi.
  - Luas lahan dicetak dalam m² (konversi otomatis dari hektar),
    tempat penandatanganan dari wilayah alamat pemilik, jangka
    waktu surat dari tanggal mulai s/d estimasi panen, persen
    Pasal 3 dari field bagi hasil (pemilik = 100% − perusahaan).
- **Sewa Tahunan** — tanpa surat perjanjian. Tanggal mulai wajib,
  jangka waktu dipilih dari dropdown 1–5 tahun dan tanggal akhir
  sewa dihitung otomatis. Ada field **harga sewa per tahun** yang
  sifatnya **sementara** (perusahaan belum menetapkan harga pasti —
  perlu validasi ulang).

Setelah `git pull` untuk perubahan ini, jalankan:

```bash
composer install   # ada dependency baru: barryvdh/laravel-dompdf
npm install
npm run build
php artisan migrate
```

## Fase 3 — Marketplace 3.1: katalog, menu admin, auth pembeli

Marketplace adalah aplikasi terpisah di area `/produk` (tombol **Produk** di
navbar blog), dikelola dari dashboard admin yang sama lewat menu induk
**Marketplace → Produk**. Scope lengkap: `docs/fase-3-scope.md`.

- **Katalog publik** — `/produk` (filter kategori Pertanian/Peternakan +
  pencarian) dan `/produk/{slug}`. Hanya produk berstatus **aktif** yang
  tampil; draft/arsip disembunyikan (detail draft = 404).
- **Produk** — kategori hanya `pertanian`/`peternakan`; tipe `barang`
  (memakai stok) atau `jasa` (tanpa stok, tanpa ongkir). Foto di-upload ke
  disk `public` (folder `products/`) — pastikan `php artisan storage:link`
  sudah pernah dijalankan. Produk hasil panen (tahap 3.3) akan terhubung
  lewat kolom `harvest_project_id`.
- **Pembeli** — publik boleh eksplor tanpa login. Registrasi mandiri di
  `/produk/daftar` membuat user role `pembeli`; login bisa dari
  `/produk/masuk` atau pintu tunggal `/admin/login` (pembeli diarahkan
  pulang ke `/produk`). Role pembeli **tidak** bisa masuk webapp pencatatan
  (`/app` khusus admin/pemilik/penggarap). Keranjang & checkout Midtrans
  menyusul di tahap 3.2.
- **Seed** — `MarketplaceSeeder`: Jasa Semprot Drone Rp300.000/ha, Jasa
  Mesin Tanam Rp400.000/ha (tarif normal Fase 2), Alat Penetas Telur
  Rp1.500.000/unit stok 5 (harga contoh — ubah lewat menu Produk).

Setelah `git pull` untuk perubahan ini, jalankan:

```bash
npm run build
php artisan migrate --seed
```

## Fase 3 — Marketplace 3.2: keranjang, checkout, Midtrans, Biteship

- **Keranjang** — publik tanpa login (session, bertahan setelah login).
  Checkout **wajib login**; **admin dikecualikan** dari membeli (403).
- **Checkout** — alamat memakai dropdown wilayah + kode pos (tersimpan ke
  profil). Ongkir dari **Biteship** dihitung ulang di server (tidak percaya
  angka browser). Jasa tidak dihitung ongkir & tidak dikirim; keranjang
  campuran boleh. Satuan jasa = **m²** (seed drone Rp30/m², mesin tanam
  Rp40/m²).
- **Stok** dipotong saat pesanan dibuat; kembali otomatis bila pesanan
  dibatalkan pembeli/admin atau pembayaran kedaluwarsa. Batas bayar 24 jam.
- **Pembayaran Midtrans Snap** — status resmi disinkronkan lewat tombol
  "Cek Status" di halaman pesanan; webhook `POST /midtrans/notification`
  (verifikasi signature) aktif begitu ada URL publik dan didaftarkan di
  dashboard Midtrans.
- **Admin** — menu Marketplace → **Pesanan** (badge = jumlah status
  "dibayar"): proses → kirim (resi otomatis dari order Biteship, fallback
  resi manual) → selesai; pesanan jasa langsung diproses → selesai.
- **Kunci API** — isi di `.env` (lihat `.env.example`): `MIDTRANS_SERVER_KEY`,
  `MIDTRANS_CLIENT_KEY`, `BITESHIP_API_KEY`, dan `BITESHIP_ORIGIN_PHONE`
  (no. HP gudang untuk pickup kurir). Tanpa kunci Midtrans, pesanan tetap
  bisa dibuat tetapi tombol Bayar tidak muncul; tanpa kunci Biteship,
  checkout yang berisi barang tidak bisa menghitung ongkir.
- **BERAT PAKET (sementara)** — ongkir memakai berat minimum Biteship
  1000 gram untuk semua paket (`BITESHIP_DEFAULT_WEIGHT_GRAM`).
  **Perlu diubah ke berat asli per produk** setelah perusahaan menimbang
  produknya — pengingat tercatat untuk user.

Setelah `git pull` untuk perubahan ini, jalankan:

```bash
npm run build
php artisan migrate
```
