<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Keranjang belanja marketplace berbasis session (Fase 3.2).
 * Boleh diisi tanpa login; isinya bertahan melewati proses login
 * karena tersimpan di session yang sama.
 */
class Cart
{
    private const KEY = 'shop_cart';

    /** @return array<int, int> product_id => qty */
    public static function raw(): array
    {
        return session(self::KEY, []);
    }

    private static function put(array $items): void
    {
        session([self::KEY => $items]);
    }

    /** Item keranjang yang masih valid (produk aktif), lengkap dengan subtotalnya. */
    public static function items(): Collection
    {
        $raw = self::raw();

        if ($raw === []) {
            return collect();
        }

        $products = Product::query()->aktif()->whereIn('id', array_keys($raw))->get()->keyBy('id');

        return collect($raw)
            ->filter(fn ($qty, $id) => $products->has($id))
            ->map(function ($qty, $id) use ($products) {
                $product = $products[$id];

                return [
                    'product' => $product,
                    'quantity' => (int) $qty,
                    'subtotal' => $product->price * (int) $qty,
                ];
            })
            ->values();
    }

    public static function add(Product $product, int $quantity = 1): void
    {
        $items = self::raw();
        $items[$product->id] = ($items[$product->id] ?? 0) + max(1, $quantity);
        self::put($items);
    }

    public static function update(int $productId, int $quantity): void
    {
        $items = self::raw();

        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = $quantity;
        }

        self::put($items);
    }

    public static function remove(int $productId): void
    {
        $items = self::raw();
        unset($items[$productId]);
        self::put($items);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }

    /** Total jumlah item (untuk badge di header toko). */
    public static function count(): int
    {
        return (int) collect(self::raw())->sum();
    }

    public static function subtotal(): int
    {
        return (int) self::items()->sum('subtotal');
    }

    public static function hasBarang(): bool
    {
        return self::items()->contains(fn ($item) => $item['product']->isBarang());
    }

    public static function hasJasa(): bool
    {
        return self::items()->contains(fn ($item) => $item['product']->isJasa());
    }

    public static function isEmpty(): bool
    {
        return self::items()->isEmpty();
    }
}
