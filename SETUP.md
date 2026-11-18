# FunFarm Platform — Fase 1: Blog (Sisi Viewer)

Aplikasi Laravel 13 + Blade dengan database SQLite.
Spec lengkap project ada di `README.md` (dokumen FunFarm Platform).
Fase 1 ini baru membangun **sisi viewer blog**; sisi updater/admin menyusul.

## Isi Fase 1 (viewer)

- Daftar artikel yang sudah diterbitkan (status `published`), tampil sebagai card grid
- Halaman detail artikel di `/artikel/{slug}`
- Filter berdasarkan kategori: Pertanian, Peternakan, Teknologi Pertanian, Informasi Umum
- Pencarian artikel (judul, ringkasan, isi)
- Artikel draft tidak tampil di viewer dan detailnya mengembalikan 404
- 5 artikel dummy dari seeder
- Grid artikel hanya dirender bila ada artikel; bila hasil kosong yang tampil
  adalah pesan *empty state* — tidak ada card/grid kosong

## Menjalankan

Syarat: PHP 8.3+ dan Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # bila file belum ada
php artisan migrate:fresh --seed
php artisan serve
```

Lalu buka http://127.0.0.1:8000

Database default adalah SQLite (`DB_CONNECTION=sqlite` di `.env`),
file-nya di `database/database.sqlite`.

## Struktur penting

| Bagian | Lokasi |
|---|---|
| Model | `app/Models/Article.php`, `app/Models/ArticleCategory.php` |
| Controller viewer | `app/Http/Controllers/ArticleController.php` |
| Routing | `routes/web.php` (`/` dan `/artikel/{slug}`) |
| Template Blade | `resources/views/layouts/app.blade.php`, `resources/views/articles/` |
| Migrasi | `database/migrations/*create_article_categories_table.php`, `*create_articles_table.php` |
| Seeder 5 artikel dummy | `database/seeders/ArticleSeeder.php` |

## Skema artikel

Tabel `articles`: `title`, `slug` (otomatis dari judul, unik), `excerpt`,
`content`, `thumbnail` (nullable), `status` (`draft`/`published`),
`published_at`, dan relasi `article_category_id` ke `article_categories`.
