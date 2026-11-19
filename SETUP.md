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

Tahap berikutnya: profil sawah, pencatatan pupuk/obat & sewa dengan
approval, kalender tanam/panen, laporan biaya produksi, dan generate
surat perjanjian (pemilik ↔ perusahaan). Showcase sewa ditunda sampai
landing page root dibuat; katalog produk unggulan di-skip dulu.
