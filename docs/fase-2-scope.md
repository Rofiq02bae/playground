# FunFarm Fase 2 — Scope Terkunci

Disusun 2026-11-19 dari revisi user pagi ini di chat "Funfarm phase 2 service breakdown".
Tujuannya satu: semua keputusan yang sudah diambil user tercatat di satu tempat,
supaya waktu bangun tidak mengulang pertanyaan yang sama dan tidak membongkar ulang.

Status: **scope terkunci. RBAC dikonfirmasi user 2026-11-19 dan fondasinya
sudah dibangun hari itu juga** — dokumen ini jadi catatan pengunci keputusan,
bukan lagi pengajuan "gas".

**Kemajuan (diperbarui 2026-11-20): fondasi RBAC, modul pencatatan,
dropdown alamat wilayah, peta lokasi sawah (Leaflet + Geoman), dan
kalender tanam & panen SUDAH jadi dan ter-push. Kalender v1 murni tanggal
turunan (start_date, estimated_harvest_date, tanggal expense) sesuai
konfirmasi user 2026-11-20 — tanpa entri manual, tanpa tabel
planting_cycles/harvest_records (itu digabung ke modul laporan/panen).
Sisa Fase 2: laporan biaya produksi, surat perjanjian.**

## Yang dibangun sekarang — HANYA pencatatan

Dua tampilan, satu aplikasi Laravel di repo `playground`:

1. **WebApp mobile** — tampilan mobile-first buat dipakai di lapangan (Android),
   untuk pencatatan: profil sawah, penggunaan + harga pupuk & obat,
   masa tanam & masa panen (kalender), laporan biaya produksi.
2. **Desktop view** — untuk perusahaan: dashboard, approval, laporan,
   surat perjanjian.

## Yang di-SKIP / ditunda (keputusan user, jangan dibangun sekarang)

- **Showcase sewa alat** (drone semprot, mesin tanam, mesin penetas telur) —
  ditunda sampai landing page / root project dibuat. Saat ini `/` masih
  redirect ke `/blog`, jadi belum ada tempat showcase-nya.
- **Manajemen katalog produk unggulan** — skip dulu, keluar dari scope Fase 2.
- **Form booking / permintaan sewa publik** — tidak ada. Pemesanan ditangani
  perusahaan; pencatatan sewa masuk lewat webapp.

## 3 role user

| Role | Bisa |
| --- | --- |
| **Pemilik sawah** | View only: lihat sawahnya, semua catatan, laporan, status approval |
| **Penggarap sawah** | View + add data. Semua inputnya berstatus **Pending** dulu |
| **Perusahaan** | All permission: kelola semua data & akun, satu-satunya yang **Approve / Reject** data penggarap |

- Tiap profil sawah punya `pemilik_id` + `penggarap_id` — pemilik & penggarap
  hanya lihat sawah yang terkait dengan mereka.
- Akun pemilik & penggarap **dibuatkan perusahaan**, tidak ada daftar mandiri.
- Data **Pending belum dihitung** di laporan biaya & bagi hasil — baru setelah
  Approve jadi angka resmi. Reject disertai catatan, penggarap bisa perbaiki.

## Membership — biner, diinput admin, tanpa integrasi WiFi

- Harga member **tetap ada**: non-member = harga normal, member = harga potongan.
- Membership ada 2 jalur masuk (langganan WiFi perusahaan, atau bayar sendiri
  dapat kartu identitas fisik) — tapi **FunFarm tidak terhubung / tidak mengimpor
  database WiFi sama sekali**.
- Di form customer/pemilik, admin cukup memilih **`Membership`** atau
  **`Bukan Membership`**, setelah mengecek identitas customer secara manual
  di luar sistem. Yang tersimpan hanya hasil akhir itu; sumbernya tidak
  dibedakan di database.
- **Kartu member = kartu identitas fisik saja.** Tidak ada nomor kartu,
  tidak ada masa aktif/kedaluwarsa, kartunya tidak di-track sistem.
  Yang dicatat hanya data identitas pemilik + satu penanda member/bukan.
  Status member tidak kedaluwarsa otomatis — berubah hanya kalau admin mengubahnya.
- Diskon ditetapkan dari data identitas pemilik sawah yang terdaftar sebagai
  member. Tiap layanan punya harga normal & harga member, dan harga yang
  berlaku di-snapshot di catatan sewa mengikuti status member pemilik
  (input harga manual untuk sewa diabaikan) supaya laporan lama tidak
  berubah kalau tarif diganti.

## Surat perjanjian — 2 pihak saja

- **Pihak Pertama: Perusahaan. Pihak Kedua: Pemilik sawah.**
- Penggarap **bukan** pihak dan tidak ikut tanda tangan.
- Isi diambil dari data proyek: identitas pemilik, profil sawah
  (lokasi, luas, tanaman), masa tanam, ketentuan bagi hasil.
- Keluar sebagai dokumen siap print/PDF dengan nomor surat otomatis.
  (Template teks di-draft asisten dulu, user koreksi redaksinya.)

## Laporan biaya produksi

- Hanya dari data yang sudah di-approve.
- Angka contoh dari README Bagian B (patokan verifikasi, bukan janji):
  pengeluaran drone 500rb + mesin tanam 700rb + pupuk 1,4jt + obat 300rb
  + tenaga kerja 500rb = 3,4jt; panen 15jt; bersih 11,6jt;
  bagi hasil 50:50 = 5,8jt per pihak.

## Keputusan arsitektur (DIKONFIRMASI user 2026-11-19)

**Satu database, satu tabel `users`, tambah kolom `role`** — tidak perlu
database lain. Alasannya: profil sawah (`pemilik_id`, `penggarap_id`),
laporan, approval, dan surat perjanjian semuanya berelasi langsung ke
tabel users; database terpisah memutus relasi itu.

**RBAC yang dikonfirmasi:** role **admin (= perusahaan)** dapat **SEMUA akses**,
termasuk blog dan webapp pencatatan. Role selain admin sesuai breakdown di atas
(pemilik view only; penggarap view + add data + approval perusahaan).

- Akun blog yang ada (`admin@funfarm.id`, login pakai email) naik jadi
  role **Perusahaan** — tetap bisa kelola artikel blog + semua fitur pencatatan.
- Satu pintu login untuk semua; habis login sistem mengarahkan sesuai role:
  perusahaan → dashboard desktop, pemilik & penggarap → webapp mobile.
- Penegakan lewat middleware per role.
- Penyesuaian praktis: pemilik & penggarap di lapangan belum tentu punya
  email — akun mereka dibuatkan perusahaan dengan **username / nomor HP
  sebagai login** + password awal, email boleh kosong. Akun perusahaan &
  blog tetap pakai email seperti sekarang.

## Keadaan kode sekarang (dicek 2026-11-19, diperbarui 11:31 WIB)

- Tabel `users` sudah punya `role`, `username`, `phone`, `is_member`,
  alamat wilayah (`province/regency/district/village_code` + `hamlet`/
  `rt/rw`), email boleh kosong; akun lama `admin@funfarm.id` jadi admin.
- Login satu pintu `/admin/login` menerima **email ATAU username**;
  redirect per role (admin → panel admin, pemilik/penggarap → `/app`).
- Middleware `role`: `/admin/*` khusus admin (pemilik/penggarap kena 403).
- Tabel Fase 2 sudah ada: `services` (tarif normal/member), `projects`
  (profil sawah: owner/cultivator, luas/lokasi/tanaman/tanggal/status,
  profit share), `project_expenses` (kategori + status
  pending/approved/rejected), `regions` (91.599 baris wilayah, kode
  hierarkis cahyadsn/wilayah).
- Rute admin: `/admin/projects`, `/admin/projects/{id}`,
  `/admin/approvals`, `/admin/services`, `/admin/users`,
  `/admin/regions/children`. Webapp: `/app`, `/app/sawah`,
  `/app/sawah/{id}`. Form tambah/edit pengguna memakai dropdown
  Provinsi → Kabupaten/Kota → Kecamatan → Kelurahan/Desa + teks Dusun
  dan RT/RW; alamat lengkap dikomposisi otomatis.
- Push terakhir: `a5fdd82` (RBAC, 20/20 cek), `24abb40` (pencatatan,
  32/32 cek), `3e7af88` (alamat wilayah, 17/17 cek).
- `/` masih redirect ke `/blog` (Fase 1).

## Urutan bangun

1. **Fondasi user & role** — ✅ SELESAI (`a5fdd82`).
2. Profil sawah & pencatatan pupuk/obat/sewa + approval — ✅ SELESAI
   (`24abb40`); dropdown alamat wilayah — ✅ SELESAI (`3e7af88`).
3. Kalender tanam/panen + laporan biaya — BELUM, menunggu pilihan user.
4. Surat perjanjian — BELUM, menunggu pilihan user.
5. Showcase & landing page — proyek terpisah, menyusul.

## Yang masih perlu jawaban user sebelum / saat bangun

1. ~~Login pemilik/penggarap pakai username atau nomor HP?~~ TERJAWAB:
   login pakai **email atau username** (kolom `phone` tetap tersimpan).
2. Template / redaksi resmi surat perjanjian — di-draft asisten dulu atau
   user kasih teks resmi?

## Verifikasi (seperti Fase 1)

Setiap langkah dicek via HTTP: login per role, pemilik tidak bisa buka form
input, penggarap tidak bisa approve / buka panel artikel, data Pending tidak
masuk laporan, harga member vs normal sesuai status pemilik. Hasil cek
dicatat jumlahnya, dan yang tidak bisa dicek visual diakui apa adanya.
