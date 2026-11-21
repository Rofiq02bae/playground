<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Fase 3.1 — tiga produk awal marketplace (harga seed, bisa diedit admin):
 * dua jasa mengikuti tarif normal master layanan Fase 2, satu barang contoh.
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
