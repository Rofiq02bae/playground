<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Biteship (Fase 3.2 — aggregator pengiriman marketplace)
    |--------------------------------------------------------------------------
    | Isi API key dari dashboard Biteship di .env: BITESHIP_API_KEY.
    | Base URL dapat dioverride lewat .env (dipakai juga untuk pengujian lokal).
    */

    'api_key' => env('BITESHIP_API_KEY'),
    'base_url' => env('BITESHIP_BASE_URL', 'https://api.biteship.com'),

    // Kurir yang ditawarkan ke pembeli saat checkout.
    'couriers' => env('BITESHIP_COURIERS', 'jne,jnt,sicepat,anteraja'),

    /*
     * SEMENTARA (keputusan user 2026-11-21): berat paket memakai berat
     * minimum Biteship untuk semua hitungan ongkir, karena belum ada data
     * berat asli per produk.
     * TODO: ganti dengan field berat per produk setelah perusahaan
     * menimbang produk-produknya — user minta diingatkan soal ini.
     */
    'default_weight_gram' => (int) env('BITESHIP_DEFAULT_WEIGHT_GRAM', 1000),

    // Alamat asal pengiriman (gudang perusahaan). Default mengikuti alamat
    // perusahaan di config/perjanjian.php; override lewat .env bila beda.
    'origin' => [
        'name' => env('BITESHIP_ORIGIN_NAME', 'PT Sarana Akses Adikarya'),
        'phone' => env('BITESHIP_ORIGIN_PHONE', ''),
        'address' => env('BITESHIP_ORIGIN_ADDRESS', 'Jalan Pandansimo, Bakungan, Klurahan, Trimurti, Kec. Srandakan, Kabupaten Bantul, Daerah Istimewa Yogyakarta'),
        'postal_code' => env('BITESHIP_ORIGIN_POSTAL_CODE', '55762'),
    ],
];
