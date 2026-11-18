# FunFarm Platform — Fase 1: Blog (Sisi Viewer)

Aplikasi Laravel 13 + Blade + Tailwind CSS (Vite) dengan database SQLite.
Spec lengkap project ada di `README.md` (dokumen FunFarm Platform).
Fase 1 ini baru membangun **sisi viewer blog**; sisi updater/admin menyusul.

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
