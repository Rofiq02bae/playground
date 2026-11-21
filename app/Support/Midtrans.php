<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi pembayaran Midtrans Snap (Fase 3.2) lewat HTTP API resmi,
 * tanpa SDK tambahan. Kunci dibaca dari config/midtrans.php (.env).
 */
class Midtrans
{
    public static function configured(): bool
    {
        return filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
    }

    private static function http()
    {
        return Http::withBasicAuth((string) config('midtrans.server_key'), '')
            ->acceptJson()
            ->timeout(20);
    }

    /**
     * Buat token Snap untuk sebuah pesanan. Mengembalikan token atau null
     * bila Midtrans tidak terkonfigurasi / menolak (pesanan tetap tersimpan
     * dan pembayaran bisa dicoba lagi dari halaman pesanan).
     */
    public static function createSnapToken(Order $order): ?string
    {
        if (! self::configured()) {
            return null;
        }

        try {
            $response = self::http()->post(config('midtrans.api_base').'/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id' => $order->code,
                    'gross_amount' => $order->total,
                ],
                'customer_details' => [
                    'first_name' => $order->recipient_name,
                    'email' => $order->user?->email,
                    'phone' => $order->recipient_phone,
                ],
                'expiry' => [
                    'unit' => 'hour',
                    'duration' => (int) config('midtrans.expiry_hours', 24),
                ],
            ]);

            $token = $response->json('token');

            if ($response->successful() && filled($token)) {
                $order->update(['snap_token' => $token]);

                return $token;
            }

            Log::warning('Midtrans createSnapToken gagal', ['order' => $order->code, 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::warning('Midtrans createSnapToken error: '.$e->getMessage(), ['order' => $order->code]);
        }

        return null;
    }

    /** Ambil status transaksi terbaru dari Midtrans untuk pesanan ini. */
    public static function fetchStatus(Order $order): ?array
    {
        if (! self::configured()) {
            return null;
        }

        try {
            $response = self::http()->get(config('midtrans.api_base').'/v2/'.$order->code.'/status');

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::warning('Midtrans fetchStatus error: '.$e->getMessage(), ['order' => $order->code]);

            return null;
        }
    }

    /** Sinkronkan status pesanan dari Midtrans (dipakai tombol Cek Status & webhook). */
    public static function sync(Order $order): Order
    {
        $payload = self::fetchStatus($order);

        if ($payload) {
            self::apply($order, $payload);
        }

        return $order->refresh();
    }

    /**
     * Terapkan payload status/notifikasi Midtrans ke pesanan.
     * - settlement/capture (fraud accept) -> dibayar
     * - expire/cancel/deny/failure -> dibatalkan + stok kembali (hanya dari menunggu_pembayaran)
     * - pending -> tetap menunggu
     */
    public static function apply(Order $order, array $payload): void
    {
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? 'accept');

        $order->payment_status = $transactionStatus ?: $order->payment_status;
        $order->payment_type = $payload['payment_type'] ?? $order->payment_type;
        $order->save();

        if (in_array($transactionStatus, ['settlement', 'capture'], true) && $fraudStatus !== 'challenge') {
            if ($order->status === Order::STATUS_MENUNGGU) {
                $order->update([
                    'status' => Order::STATUS_DIBAYAR,
                    'paid_at' => now(),
                ]);
            }

            return;
        }

        if (in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'], true)) {
            if ($order->status === Order::STATUS_MENUNGGU) {
                $order->cancelAndRestoreStock();
            }
        }
    }

    /** Verifikasi signature_key notifikasi webhook Midtrans. */
    public static function signatureValid(array $payload): bool
    {
        $serverKey = (string) config('midtrans.server_key');

        if ($serverKey === '' || empty($payload['signature_key'])) {
            return false;
        }

        $expected = hash('sha512',
            (string) ($payload['order_id'] ?? '')
            .(string) ($payload['status_code'] ?? '')
            .(string) ($payload['gross_amount'] ?? '')
            .$serverKey
        );

        return hash_equals($expected, (string) $payload['signature_key']);
    }
}
