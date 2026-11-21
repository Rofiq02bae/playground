<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Aggregator pengiriman Biteship (Fase 3.2): cek ongkir + buat order/resi.
 * Semua panggilan dibungkus di sini agar penyedia bisa diganti tanpa
 * menyentuh controller (keputusan arsitektur di docs/fase-3-scope.md).
 */
class Shipping
{
    public static function configured(): bool
    {
        return filled(config('biteship.api_key'));
    }

    private static function http()
    {
        return Http::withToken((string) config('biteship.api_key'))
            ->acceptJson()
            ->timeout(25);
    }

    /** Pesan error asli dari respons Biteship, agar alasan penolakan tampil di halaman (bukan cuma di log). */
    private static function errorReason($response): ?string
    {
        $error = $response->json('error') ?? $response->json('message');
        if (is_array($error)) {
            $error = collect($error)->flatten()->filter()->implode(' ');
        }
        $error = is_string($error) ? trim($error) : null;

        return $error !== null && $error !== '' ? $error : null;
    }

    /** Kunci pilihan kurir yang dipilih pembeli: "company|service". */
    public static function rateKey(array $rate): string
    {
        return $rate['company'].'|'.$rate['service'];
    }

    /**
     * Daftar pilihan ongkir ke kode pos tujuan untuk item-item barang.
     * @param  array<int, array{name: string, quantity: int, value: int}>  $items
     * @return array<int, array{company: string, service: string, price: int, duration: ?string}>
     * @throws \RuntimeException bila Biteship tidak terkonfigurasi / gagal.
     */
    public static function rates(string $destinationPostalCode, array $items): array
    {
        if (! self::configured()) {
            throw new \RuntimeException('Layanan pengiriman belum dikonfigurasi (BITESHIP_API_KEY kosong).');
        }

        // SEMENTARA: satu paket seberat berat minimum Biteship (lihat config/biteship.php).
        $weight = (int) config('biteship.default_weight_gram', 1000);

        $payloadItems = collect($items)->map(fn ($item) => [
            'name' => $item['name'],
            'description' => $item['name'],
            'value' => (int) $item['value'],
            'quantity' => (int) $item['quantity'],
            'weight' => $weight,
        ])->all();

        try {
            $response = self::http()->post(config('biteship.base_url').'/v1/rates/couriers', [
                'origin_postal_code' => (string) config('biteship.origin.postal_code'),
                'destination_postal_code' => $destinationPostalCode,
                'couriers' => (string) config('biteship.couriers'),
                'items' => $payloadItems,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Biteship rates error: '.$e->getMessage());
            throw new \RuntimeException('Gagal menghubungi layanan pengiriman. Coba lagi sebentar lagi.');
        }

        if (! $response->successful()) {
            Log::warning('Biteship rates gagal', ['status' => $response->status(), 'body' => $response->body()]);
            $reason = self::errorReason($response);
            throw new \RuntimeException('Ongkir ke alamat ini belum bisa dihitung'.($reason ? ' ('.$reason.')' : '').'. Periksa kode pos tujuan.');
        }

        return collect($response->json('pricing', []))
            ->map(fn ($p) => [
                // company/service = KODE resmi (dipakai ulang sebagai courier_company/courier_type
                // saat buat order — Biteship menolak nama tampilan); *_label khusus untuk ditampilkan.
                'company' => (string) ($p['courier_code'] ?? $p['company'] ?? ''),
                'service' => (string) ($p['courier_service_code'] ?? $p['type'] ?? ($p['service'] ?? '')),
                'company_label' => (string) ($p['courier_name'] ?? $p['courier_code'] ?? $p['company'] ?? ''),
                'service_label' => (string) ($p['courier_service_name'] ?? $p['courier_service_code'] ?? $p['type'] ?? ($p['service'] ?? '')),
                'price' => (int) ($p['price'] ?? 0),
                'duration' => $p['duration'] ?? ($p['shipment_duration_range'] ?? null),
            ])
            ->filter(fn ($r) => $r['company'] !== '' && $r['price'] > 0)
            ->sortBy('price')
            ->values()
            ->all();
    }

    /**
     * Buat order pengiriman di Biteship untuk pesanan yang siap dikirim.
     * @return array{biteship_order_id: string, waybill: ?string}
     * @throws \RuntimeException bila gagal (admin bisa fallback resi manual).
     */
    public static function createOrder(Order $order): array
    {
        if (! self::configured()) {
            throw new \RuntimeException('Biteship belum dikonfigurasi (BITESHIP_API_KEY kosong).');
        }

        $origin = config('biteship.origin');

        // Cek kelengkapan data pengirim di awal — lebih baik pesan jelas di sini
        // daripada ditolak Biteship satu field demi satu field.
        $missing = [];
        foreach (['name' => 'BITESHIP_ORIGIN_NAME', 'phone' => 'BITESHIP_ORIGIN_PHONE', 'address' => 'BITESHIP_ORIGIN_ADDRESS', 'postal_code' => 'BITESHIP_ORIGIN_POSTAL_CODE'] as $key => $envKey) {
            if (blank($origin[$key] ?? null)) {
                $missing[] = $envKey;
            }
        }
        if ($missing) {
            throw new \RuntimeException('Data pengirim (gudang) belum lengkap — isi di .env: '.implode(', ', $missing).'.');
        }

        $weight = (int) config('biteship.default_weight_gram', 1000);

        $items = $order->items
            ->where('product_type', 'barang')
            ->map(fn ($item) => [
                'name' => $item->product_name,
                'description' => $item->product_name,
                'value' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'weight' => $weight,
            ])
            ->values()
            ->all();

        try {
            // Payload Biteship itu FLAT (courier_company/courier_type, shipper_contact_*) —
            // objek bersarang 'courier'/'shipper' diabaikan server dan memicu error
            // "missing 'courier_type' field".
            $response = self::http()->post(config('biteship.base_url').'/v1/orders', [
                'shipper_contact_name' => $origin['name'],
                'shipper_contact_phone' => $origin['phone'],
                'origin_contact_name' => $origin['name'],
                'origin_contact_phone' => $origin['phone'],
                'origin_address' => $origin['address'],
                'origin_postal_code' => (string) $origin['postal_code'],
                'destination_contact_name' => $order->recipient_name,
                'destination_contact_phone' => $order->recipient_phone,
                'destination_address' => $order->address_text,
                'destination_postal_code' => $order->postal_code,
                'courier_company' => $order->courier_company,
                'courier_type' => $order->courier_service,
                'delivery_type' => 'now',
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Biteship createOrder error: '.$e->getMessage(), ['order' => $order->code]);
            throw new \RuntimeException('Gagal membuat order di Biteship: '.$e->getMessage());
        }

        if (! $response->successful() || blank($response->json('id'))) {
            Log::warning('Biteship createOrder gagal', ['order' => $order->code, 'status' => $response->status(), 'body' => $response->body()]);
            $reason = self::errorReason($response);
            throw new \RuntimeException('Biteship menolak pembuatan order'.($reason ? ': '.$reason : '').' Gunakan input resi manual.');
        }

        return [
            'biteship_order_id' => (string) $response->json('id'),
            'waybill' => $response->json('courier.waybill_id'),
        ];
    }

    /** Label Bahasa Indonesia untuk status pelacakan Biteship. */
    public static function trackingStatusLabel(string $status): string
    {
        return match ($status) {
            'confirmed' => 'Pesanan dikonfirmasi kurir',
            'allocated' => 'Kurir ditugaskan',
            'picking_up' => 'Kurir menuju titik jemput',
            'picked' => 'Paket telah dijemput',
            'dropping_off' => 'Paket dalam perjalanan',
            'delivered' => 'Paket terkirim',
            'on_hold' => 'Paket tertahan',
            'returned' => 'Paket dikembalikan ke pengirim',
            'return_in_transit' => 'Paket sedang dikembalikan',
            'cancelled' => 'Pengiriman dibatalkan',
            'rejected' => 'Paket ditolak',
            'disposed' => 'Paket dimusnahkan',
            default => $status === '' ? 'Tidak diketahui' : ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Ambil JSON dari endpoint GET Biteship; RuntimeException berpesan jelas bila gagal.
     * @throws \RuntimeException
     */
    private static function fetchJson(string $path, Order $order): array
    {
        try {
            $response = self::http()->get(config('biteship.base_url').$path);
        } catch (\Throwable $e) {
            Log::warning('Biteship GET '.$path.' error: '.$e->getMessage(), ['order' => $order->code]);
            throw new \RuntimeException('Gagal menghubungi layanan pelacakan. Coba lagi sebentar lagi.');
        }

        if (! $response->successful()) {
            Log::warning('Biteship GET '.$path.' gagal', ['order' => $order->code, 'status' => $response->status(), 'body' => $response->body()]);
            $reason = self::errorReason($response);
            throw new \RuntimeException('Pelacakan belum tersedia'.($reason ? ': '.$reason : '.'));
        }

        return $response->json() ?? [];
    }

    /** Bentukkan data tracking/order Biteship menjadi struktur timeline yang seragam. */
    private static function normalizeTracking(array $data, Order $order): array
    {
        $history = collect($data['history'] ?? [])
            ->map(fn ($h) => [
                'note' => (string) ($h['note'] ?? ''),
                'status' => (string) ($h['status'] ?? ''),
                'status_label' => self::trackingStatusLabel((string) ($h['status'] ?? '')),
                'updated_at' => $h['updated_at'] ?? null,
            ])
            ->sortByDesc('updated_at')
            ->values()
            ->all();

        $status = (string) ($data['status'] ?? '');

        // Sumber data tanpa riwayat (mis. objek order): tampilkan status terakhirnya saja.
        if ($history === [] && $status !== '') {
            $history = [[
                'note' => '',
                'status' => $status,
                'status_label' => self::trackingStatusLabel($status),
                'updated_at' => $data['updated_at'] ?? null,
            ]];
        }

        return [
            'status' => $status,
            'status_label' => self::trackingStatusLabel($status),
            'waybill' => (string) ($data['waybill_id'] ?? $data['courier']['waybill_id'] ?? $order->waybill),
            'history' => $history,
        ];
    }

    /**
     * Lacak paket pesanan dari Biteship.
     *
     * Jalur utama untuk pesanan yang dibuat lewat API: ambil tracking_id dari
     * objek order (GET /v1/orders/{id}), lalu baca objek tracking resminya
     * (GET /v1/trackings/{tracking_id}). Endpoint publik berbasis nomor resi
     * mengandalkan data kurir sungguhan — order mode testing tidak terhubung
     * ke kurir asli, jadi di sana selalu "Data is not found"; endpoint publik
     * hanya dipakai untuk resi manual yang memang resi kurir asli.
     *
     * @return array{status: string, status_label: string, waybill: string, history: array<int, array{note: string, status: string, status_label: string, updated_at: ?string}>}
     * @throws \RuntimeException bila belum bisa dilacak / Biteship menolak.
     */
    public static function tracking(Order $order): array
    {
        if (! self::configured()) {
            throw new \RuntimeException('Layanan pengiriman belum dikonfigurasi (BITESHIP_API_KEY kosong).');
        }

        if (blank($order->waybill) && blank($order->biteship_order_id)) {
            throw new \RuntimeException('Pesanan ini belum memiliki nomor resi untuk dilacak.');
        }

        $lastError = null;

        if (filled($order->biteship_order_id)) {
            try {
                $orderData = self::fetchJson('/v1/orders/'.$order->biteship_order_id, $order);
                $trackingId = $orderData['courier']['tracking_id'] ?? null;

                if (filled($trackingId)) {
                    try {
                        return self::normalizeTracking(self::fetchJson('/v1/trackings/'.$trackingId, $order), $order);
                    } catch (\RuntimeException $e) {
                        $lastError = $e;
                    }
                }

                // Objek order sendiri membawa status terakhir — cukup untuk timeline.
                if (filled($orderData['status'] ?? null)) {
                    return self::normalizeTracking($orderData, $order);
                }
            } catch (\RuntimeException $e) {
                $lastError = $e;
            }
        }

        if (filled($order->waybill) && filled($order->courier_company)) {
            try {
                return self::normalizeTracking(
                    self::fetchJson('/v1/trackings/'.$order->waybill.'/couriers/'.strtolower((string) $order->courier_company), $order),
                    $order
                );
            } catch (\RuntimeException $e) {
                $lastError = $e;
            }
        }

        throw $lastError ?? new \RuntimeException('Pelacakan belum tersedia.');
    }
}
