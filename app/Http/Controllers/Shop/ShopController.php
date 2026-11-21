<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /** Katalog publik marketplace (Fase 3): hanya produk berstatus aktif. */
    public function index(Request $request)
    {
        $products = Product::query()
            ->aktif()
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('category', $request->string('kategori')))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'categories' => Product::CATEGORIES,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'aktif', 404);

        $related = Product::query()
            ->aktif()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->orderBy('name')
            ->take(4)
            ->get();

        return view('shop.show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
