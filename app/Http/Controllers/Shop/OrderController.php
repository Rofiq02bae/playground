<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Midtrans;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Daftar pesanan milik user yang login. */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $order->load('items');

        // Pesanan menunggu dibayar tetapi belum punya token Snap: coba buat sekarang.
        if ($order->status === Order::STATUS_MENUNGGU && blank($order->snap_token)) {
            Midtrans::createSnapToken($order);
            $order->refresh();
        }

        return view('shop.orders.show', [
            'order' => $order,
            'midtransConfigured' => Midtrans::configured(),
            'snapUrl' => config('midtrans.snap_url'),
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    /** Sinkronkan status pembayaran dari Midtrans (penentu status selama belum ada webhook publik). */
    public function checkStatus(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if (! Midtrans::configured()) {
            return back()->with('error', 'Pembayaran belum dikonfigurasi perusahaan.');
        }

        $order = Midtrans::sync($order);

        return back()->with('success', 'Status pesanan: '.$order->statusLabel().'.');
    }

    /** Pembeli membatalkan pesanannya sendiri selama belum dibayar. */
    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->status === Order::STATUS_MENUNGGU, 422, 'Pesanan ini tidak bisa dibatalkan lagi.');

        $order->cancelAndRestoreStock();

        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }
}
