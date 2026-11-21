<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Produk awal marketplace (harga seed, bisa diedit admin): dua jasa mengikuti
 * tarif normal master layanan Fase 2, sisanya barang contoh. Aman dijalankan
 * berulang — produk dicocokkan berdasarkan nama, tidak diduplikasi.
 */
class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Jasa Semprot Drone',
                'category' => Product::CATEGORY_PERTANIAN,
                'type' => Product::TYPE_JASA,
                'price' => 30,
                'stock' => null,
                'unit' => 'm²',
                'description' => 'Layanan penyemprotan pupuk/obat cair dengan drone untuk lahan pertanian. Harga Rp30 per meter persegi (setara Rp300.000 per hektar, tarif resmi perusahaan); masukkan luas lahanmu dalam m² saat memesan. Jadwal pengerjaan diatur setelah pemesanan dikonfirmasi.',
                'status' => 'aktif',
            ],
            [
                'name' => 'Jasa Mesin Tanam',
                'category' => Product::CATEGORY_PERTANIAN,
                'type' => Product::TYPE_JASA,
                'price' => 40,
                'stock' => null,
                'unit' => 'm²',
                'description' => 'Layanan penanaman dengan mesin tanam modern, hasil tanam rapi dan merata. Harga Rp40 per meter persegi (setara Rp400.000 per hektar, tarif resmi perusahaan); masukkan luas lahanmu dalam m² saat memesan. Jadwal pengerjaan diatur setelah pemesanan dikonfirmasi.',
                'status' => 'aktif',
            ],
            [
                'name' => 'Alat Penetas Telur',
                'category' => Product::CATEGORY_PETERNAKAN,
                'type' => Product::TYPE_BARANG,
                'price' => 1500000,
                'stock' => 5,
                'unit' => 'unit',
                'description' => 'Mesin penetas telur untuk peternakan unggas, suhu dan kelembapan terkontrol. Harga contoh awal — silakan sesuaikan lewat menu Produk di dashboard admin.',
                'status' => 'aktif',
            ],
            [
                'name' => 'Pupuk Kompos',
                'category' => Product::CATEGORY_PERTANIAN,
                'type' => Product::TYPE_BARANG,
                'price' => 25000,
                'stock' => 100,
                'unit' => 'karung',
                'description' => 'Pupuk kompos organik untuk menyuburkan tanah dan memperbaiki struktur lahan, kemasan karung 25 kg. Harga contoh awal — silakan sesuaikan lewat menu Produk di dashboard admin.',
                'status' => 'aktif',
            ],
            [
                'name' => 'Telur Ayam',
                'category' => Product::CATEGORY_PETERNAKAN,
                'type' => Product::TYPE_BARANG,
                'price' => 50000,
                'stock' => 50,
                'unit' => 'krat',
                'description' => 'Telur ayam segar hasil peternakan, dijual per krat isi 30 butir. Harga contoh awal — silakan sesuaikan lewat menu Produk di dashboard admin.',
                'status' => 'aktif',
            ],
            [
                'name' => 'Benih Padi',
                'category' => Product::CATEGORY_PERTANIAN,
                'type' => Product::TYPE_BARANG,
                'price' => 15000,
                'stock' => 200,
                'unit' => 'kg',
                'description' => 'Benih padi unggul bersertifikat, daya tumbuh tinggi dan siap semai. Harga contoh awal per kilogram — silakan sesuaikan lewat menu Produk di dashboard admin.',
                'status' => 'aktif',
            ],
        ];

        foreach ($products as $data) {
            $existing = Product::query()->where('name', $data['name'])->first();

            if ($existing) {
                $existing->update($data);
            } else {
                Product::create($data + ['slug' => Product::uniqueSlug($data['name'])]);
            }
        }
    }
}
