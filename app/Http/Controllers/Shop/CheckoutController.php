<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Region;
use App\Support\Cart;
use App\Support\Midtrans;
use App\Support\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /** Admin dikecualikan dari membeli (keputusan user) — pemeriksaannya di setiap aksi. */
    private function guardBuyer(Request $request): void
    {
        abort_if($request->user()->isAdmin(), 403, 'Admin tidak dapat berbelanja di marketplace.');
    }

    public function index(Request $request)
    {
        $this->guardBuyer($request);

        if (Cart::isEmpty()) {
            return redirect()->route('shop.cart.index')->with('error', 'Keranjangmu masih kosong.');
        }

        $user = $request->user();

        return view('shop.checkout', [
            'items' => Cart::items(),
            'subtotal' => Cart::subtotal(),
            'hasBarang' => Cart::hasBarang(),
            'provinces' => Region::childrenOf(null)->orderBy('nama')->get(['kode', 'nama']),
            'prefill' => [
                'recipient_name' => old('recipient_name', $user->name),
                'recipient_phone' => old('recipient_phone', $user->phone),
                'province_code' => old('province_code', $user->province_code),
                'regency_code' => old('regency_code', $user->regency_code),
                'district_code' => old('district_code', $user->district_code),
                'village_code' => old('village_code', $user->village_code),
                'hamlet' => old('hamlet', $user->hamlet),
                'rt' => old('rt', $user->rt),
                'rw' => old('rw', $user->rw),
                'postal_code' => old('postal_code', $user->postal_code),
                'notes' => old('notes'),
            ],
        ]);
    }

    /** Pilihan ongkir dari Biteship untuk kode pos tujuan (dipanggil via fetch dari halaman checkout). */
    public function rates(Request $request)
    {
        $this->guardBuyer($request);

        $data = $request->validate([
            'postal_code' => ['required', 'digits:5'],
        ]);

        if (! Cart::hasBarang()) {
            return response()->json(['rates' => []]);
        }

        $barangItems = Cart::items()
            ->filter(fn ($item) => $item['product']->isBarang())
            ->map(fn ($item) => [
                'name' => $item['product']->name,
                'quantity' => $item['quantity'],
                'value' => $item['product']->price,
            ])
            ->values()
            ->all();

        try {
            $rates = Shipping::rates($data['postal_code'], $barangItems);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'rates' => collect($rates)->map(fn ($rate) => $rate + ['key' => Shipping::rateKey($rate)])->all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->guardBuyer($request);

        if (Cart::isEmpty()) {
            return redirect()->route('shop.cart.index')->with('error', 'Keranjangmu masih kosong.');
        }

        $hasBarang = Cart::hasBarang();

        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'province_code' => ['required', 'string', 'exists:regions,kode'],
            'regency_code' => ['required', 'string', 'exists:regions,kode'],
            'district_code' => ['required', 'string', 'exists:regions,kode'],
            'village_code' => ['required', 'string', 'exists:regions,kode'],
            'hamlet' => ['nullable', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'postal_code' => ['required', 'digits:5'],
            'courier_key' => [$hasBarang ? 'required' : 'nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $items = Cart::items();
        $subtotal = Cart::subtotal();

        // Ongkir diverifikasi ulang di server dari Biteship — tidak percaya angka dari browser.
        $shippingCost = 0;
        $courier = ['company' => null, 'service' => null];

        if ($hasBarang) {
            $barangItems = $items
                ->filter(fn ($item) => $item['product']->isBarang())
                ->map(fn ($item) => [
                    'name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'value' => $item['product']->price,
                ])
                ->values()
                ->all();

            try {
                $rates = Shipping::rates($data['postal_code'], $barangItems);
            } catch (\RuntimeException $e) {
                throw ValidationException::withMessages(['courier_key' => $e->getMessage()]);
            }

            $chosen = collect($rates)->first(fn ($rate) => Shipping::rateKey($rate) === $data['courier_key']);

            if (! $chosen) {
                throw ValidationException::withMessages(['courier_key' => 'Pilihan kurir tidak valid atau sudah berubah. Muat ulang pilihan kurir.']);
            }

            $shippingCost = $chosen['price'];
            $courier = ['company' => $chosen['company'], 'service' => $chosen['service']];
        }

        $user = $request->user();
        $addressText = $this->composeAddress($data);

        $order = DB::transaction(function () use ($request, $user, $data, $items, $subtotal, $shippingCost, $courier, $addressText) {
            // Potong stok barang (dikunci agar tidak balapan), jasa tidak memakai stok.
            foreach ($items as $item) {
                $product = $item['product'];

                if ($product->isBarang()) {
                    $fresh = Product::query()->lockForUpdate()->find($product->id);

                    if (! $fresh || $fresh->stock === null || $fresh->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'cart' => 'Stok '.$product->name.' tidak mencukupi (tersisa '.($fresh->stock ?? 0).' '.$product->unit.').',
                        ]);
                    }

                    $fresh->decrement('stock', $item['quantity']);
                }
            }

            $order = Order::create([
                'code' => 'TMP-'.uniqid(),
                'user_id' => $user->id,
                'status' => Order::STATUS_MENUNGGU,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'province_code' => $data['province_code'],
                'regency_code' => $data['regency_code'],
                'district_code' => $data['district_code'],
                'village_code' => $data['village_code'],
                'hamlet' => $data['hamlet'] ?? null,
                'rt' => $data['rt'] ?? null,
                'rw' => $data['rw'] ?? null,
                'address_text' => $addressText,
                'postal_code' => $data['postal_code'],
                'courier_company' => $courier['company'],
                'courier_service' => $courier['service'],
                'expires_at' => now()->addHours((int) config('midtrans.expiry_hours', 24)),
                'notes' => $data['notes'] ?? null,
            ]);

            $order->update(['code' => Order::generateCode($order->id)]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_type' => $item['product']->type,
                    'unit' => $item['product']->unit,
                    'price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Simpan alamat ke profil pembeli untuk checkout berikutnya.
            $user->update([
                'phone' => $user->phone ?: $data['recipient_phone'],
                'province_code' => $data['province_code'],
                'regency_code' => $data['regency_code'],
                'district_code' => $data['district_code'],
                'village_code' => $data['village_code'],
                'hamlet' => $data['hamlet'] ?? null,
                'rt' => $data['rt'] ?? null,
                'rw' => $data['rw'] ?? null,
                'postal_code' => $data['postal_code'],
                'address' => $addressText,
            ]);

            return $order;
        });

        Cart::clear();

        // Buat sesi pembayaran Snap (boleh gagal/gantung — bisa dicoba lagi dari halaman pesanan).
        Midtrans::createSnapToken($order);

        return redirect()->route('shop.orders.show', $order->code)
            ->with('success', 'Pesanan '.$order->code.' dibuat. Selesaikan pembayaran sebelum '.optional($order->expires_at)->format('d M Y H:i').'.');
    }

    /** Susun alamat lengkap dari dropdown wilayah + isian bebas. */
    private function composeAddress(array $data): string
    {
        $names = Region::query()
            ->whereIn('kode', [$data['province_code'], $data['regency_code'], $data['district_code'], $data['village_code']])
            ->pluck('nama', 'kode');

        $parts = array_filter([
            $data['hamlet'] ?? null,
            (! empty($data['rt']) || ! empty($data['rw'])) ? 'RT '.($data['rt'] ?? '-').'/RW '.($data['rw'] ?? '-') : null,
            $names[$data['village_code']] ?? null,
            $names[$data['district_code']] ?? null,
            $names[$data['regency_code']] ?? null,
            $names[$data['province_code']] ?? null,
        ]);

        return implode(', ', $parts).' '.$data['postal_code'];
    }
}
