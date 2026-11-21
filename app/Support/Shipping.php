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
            Log::warning('Biteship rates gagal', ['body' => $response->body()]);
            throw new \RuntimeException('Ongkir ke alamat ini belum bisa dihitung. Periksa kode pos tujuan.');
        }

        return collect($response->json('pricing', []))
            ->map(fn ($p) => [
                'company' => (string) ($p['courier_name'] ?? $p['company'] ?? ''),
                'service' => (string) ($p['courier_service_name'] ?? $p['service'] ?? ''),
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
            $response = self::http()->post(config('biteship.base_url').'/v1/orders', [
                'shipper' => [
                    'name' => $origin['name'],
                    'phone' => $origin['phone'],
                ],
                'origin_contact_name' => $origin['name'],
                'origin_contact_phone' => $origin['phone'],
                'origin_address' => $origin['address'],
                'origin_postal_code' => (string) $origin['postal_code'],
                'destination_contact_name' => $order->recipient_name,
                'destination_contact_phone' => $order->recipient_phone,
                'destination_address' => $order->address_text,
                'destination_postal_code' => $order->postal_code,
                'courier' => [
                    'company' => $order->courier_company,
                    'type' => $order->courier_service,
                ],
                'delivery_type' => 'now',
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Biteship createOrder error: '.$e->getMessage(), ['order' => $order->code]);
            throw new \RuntimeException('Gagal membuat order di Biteship: '.$e->getMessage());
        }

        if (! $response->successful() || blank($response->json('id'))) {
            Log::warning('Biteship createOrder gagal', ['order' => $order->code, 'body' => $response->body()]);
            throw new \RuntimeException('Biteship menolak pembuatan order. Gunakan input resi manual.');
        }

        return [
            'biteship_order_id' => (string) $response->json('id'),
            'waybill' => $response->json('courier.waybill_id'),
        ];
    }
}
