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
                'price' => 300000,
                'stock' => null,
                'unit' => 'ha',
                'description' => 'Layanan penyemprotan pupuk/obat cair dengan drone untuk lahan pertanian. Harga per hektar mengikuti tarif resmi perusahaan; jadwal pengerjaan diatur setelah pemesanan dikonfirmasi.',
                'status' => 'aktif',
            ],
            [
                'name' => 'Jasa Mesin Tanam',
                'category' => Product::CATEGORY_PERTANIAN,
                'type' => Product::TYPE_JASA,
                'price' => 400000,
                'stock' => null,
                'unit' => 'ha',
                'description' => 'Layanan penanaman dengan mesin tanam modern, hasil tanam rapi dan merata. Harga per hektar mengikuti tarif resmi perusahaan; jadwal pengerjaan diatur setelah pemesanan dikonfirmasi.',
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
