<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\Shipping;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Pesanan marketplace (Fase 3.2): dikelola perusahaan dari dashboard admin. */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['user', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('code', 'like', '%'.$request->string('q').'%')
                ->orWhere('recipient_name', 'like', '%'.$request->string('q').'%')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);

        return view('admin.orders.show', ['order' => $order]);
    }

    /** Lacak paket (JSON untuk timeline di halaman detail pesanan admin). */
    public function tracking(Order $order)
    {
        try {
            return response()->json(Shipping::tracking($order));
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function process(Order $order)
    {
        abort_unless($order->status === Order::STATUS_DIBAYAR, 422, 'Pesanan belum dibayar.');
        $order->update(['status' => Order::STATUS_DIPROSES]);

        return back()->with('success', 'Pesanan ditandai diproses.');
    }

    /** Kirim via Biteship: buat order pengiriman dan ambil resi otomatis. */
    public function ship(Order $order)
    {
        abort_unless($order->status === Order::STATUS_DIPROSES, 422, 'Pesanan belum diproses.');
        abort_unless($order->hasBarang(), 422, 'Pesanan jasa tidak dikirim.');

        try {
            $result = Shipping::createOrder($order);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $order->update([
            'status' => Order::STATUS_DIKIRIM,
            'biteship_order_id' => $result['biteship_order_id'],
            'waybill' => $result['waybill'],
        ]);

        return back()->with('success', 'Order Biteship dibuat. Resi: '.($result['waybill'] ?? '(menunggu dari kurir)'));
    }

    /** Fallback: admin memasukkan nomor resi manual. */
    public function shipManual(Request $request, Order $order)
    {
        abort_unless($order->status === Order::STATUS_DIPROSES, 422, 'Pesanan belum diproses.');
        abort_unless($order->hasBarang(), 422, 'Pesanan jasa tidak dikirim.');

        $data = $request->validate([
            'waybill' => ['required', 'string', 'max:100'],
        ]);

        $order->update([
            'status' => Order::STATUS_DIKIRIM,
            'waybill' => $data['waybill'],
        ]);

        return back()->with('success', 'Pesanan ditandai dikirim dengan resi manual.');
    }

    public function complete(Order $order)
    {
        $allowed = $order->hasBarang()
            ? $order->status === Order::STATUS_DIKIRIM
            : $order->status === Order::STATUS_DIPROSES;

        abort_unless($allowed, 422, 'Pesanan belum bisa diselesaikan dari status ini.');
        $order->update(['status' => Order::STATUS_SELESAI]);

        return back()->with('success', 'Pesanan selesai.');
    }

    public function cancel(Order $order)
    {
        abort_unless(
            in_array($order->status, [Order::STATUS_MENUNGGU, Order::STATUS_DIBAYAR, Order::STATUS_DIPROSES], true),
            422,
            'Pesanan yang sudah dikirim/selesai tidak bisa dibatalkan.'
        );

        $order->cancelAndRestoreStock();

        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }
}
