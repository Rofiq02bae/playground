<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /** Marketplace (Fase 3): katalog produk dikelola perusahaan dari dashboard admin. */
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('category', $request->string('kategori')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Product::CATEGORIES,
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'product' => new Product(['status' => 'aktif', 'type' => Product::TYPE_BARANG, 'unit' => 'pcs']),
            'categories' => Product::CATEGORIES,
            'types' => Product::TYPES,
            'statuses' => Product::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Product::uniqueSlug($data['name']);
        $data['photo_path'] = $this->storePhoto($request);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk ditambahkan ke marketplace.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Product::CATEGORIES,
            'types' => Product::TYPES,
            'statuses' => Product::STATUSES,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $data['slug'] = Product::uniqueSlug($data['name'], $product->id);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($product);
            $data['photo_path'] = $this->storePhoto($request);
        }

        if ($request->boolean('remove_photo')) {
            $this->deletePhoto($product);
            $data['photo_path'] = null;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->deletePhoto($product);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(Product::CATEGORIES))],
            'type' => ['required', Rule::in(array_keys(Product::TYPES))],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(Product::STATUSES))],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['photo']);

        // Jasa tidak memakai stok (selalu bisa dipesan); barang tanpa stok dianggap 0.
        if ($data['type'] === Product::TYPE_JASA) {
            $data['stock'] = null;
        } else {
            $data['stock'] = $data['stock'] ?? 0;
        }

        return $data;
    }

    private function storePhoto(Request $request): ?string
    {
        return $request->hasFile('photo')
            ? $request->file('photo')->store('products', 'public')
            : null;
    }

    private function deletePhoto(Product $product): void
    {
        if ($product->photo_path && ! str_starts_with($product->photo_path, 'http')) {
            Storage::disk('public')->delete($product->photo_path);
        }
    }
}
