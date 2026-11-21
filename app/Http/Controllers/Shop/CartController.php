<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('shop.cart', [
            'items' => Cart::items(),
            'subtotal' => Cart::subtotal(),
            'hasBarang' => Cart::hasBarang(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->status === 'aktif', 404);

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        Cart::add($product, $data['quantity'] ?? 1);

        return redirect()->route('shop.cart.index')
            ->with('success', $product->name.' ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quantity' => ['required', 'array'],
            'quantity.*' => ['integer', 'min:0', 'max:100000'],
        ]);

        foreach ($data['quantity'] as $productId => $qty) {
            Cart::update((int) $productId, (int) $qty);
        }

        return redirect()->route('shop.cart.index')->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Product $product)
    {
        Cart::remove($product->id);

        return redirect()->route('shop.cart.index')->with('success', 'Item dihapus dari keranjang.');
    }
}
