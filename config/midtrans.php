<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans (Fase 3.2 — pembayaran marketplace)
    |--------------------------------------------------------------------------
    | Isi kunci dari dashboard Midtrans (Settings > Access Keys) di .env:
    |   MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY
    | Sandbox: MIDTRANS_IS_PRODUCTION=false (default). Base URL dapat
    | dioverride lewat .env (dipakai juga untuk pengujian lokal).
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),

    'api_base' => env('MIDTRANS_API_BASE', 'https://api.sandbox.midtrans.com'),
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'),

    // Batas waktu pembayaran setelah pesanan dibuat (jam).
    'expiry_hours' => 24,
];
