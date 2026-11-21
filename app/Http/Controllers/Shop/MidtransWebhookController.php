<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Midtrans;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    /**
     * Notifikasi pembayaran Midtrans (webhook). Aktif begitu aplikasi punya
     * alamat publik dan URL ini didaftarkan di dashboard Midtrans.
     * Selalu balas 200 untuk payload yang valid agar Midtrans berhenti mengulang.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        if (! Midtrans::signatureValid($payload)) {
            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $order = Order::query()->where('code', $payload['order_id'] ?? '')->first();

        if ($order) {
            Midtrans::apply($order, $payload);
        }

        return response()->json(['message' => 'OK']);
    }
}
