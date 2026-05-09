<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
   // Tambahkan konstanta satuan agar mudah dikelola
private array $satuanList = ['Pcs', 'Pax', 'Rol', 'Set', 'Box', 'Lusin', 'Botol', 'Lembar', 'Unit', 'Buah'];

public function create()
{
    $satuanList = $this->satuanList;
    return view('products.create', compact('satuanList'));
}

public function store(Request $request)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'kode_barang' => 'nullable|string|max:100|unique:products,kode_barang',
        'vendor'      => 'nullable|string|max:255',
        'stock'       => 'nullable|integer|min:0',
        'satuan'      => 'required|string|max:50',
        'price'       => 'required|numeric|min:0',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'category'    => 'nullable|string|max:100',
        'variants.*.nama_varian' => 'nullable|string|max:100',
        'variants.*.stock'       => 'nullable|integer|min:0',
        'variants.*.price'       => 'nullable|numeric|min:0',
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
    }

    $totalStock = $request->stock ?? 0;
    if ($request->filled('variants')) {
        $totalStock = collect($request->variants)->sum('stock');
    }

    $product = Product::create([
        'name'        => $request->name,
        'kode_barang' => $request->kode_barang,
        'vendor'      => $request->vendor,
        'stock'       => $totalStock,
        'satuan'      => $request->satuan,
        'price'       => $request->price,
        'image'       => $imagePath,
        'category'    => $request->category,
    ]);

    if ($request->filled('variants')) {
        foreach ($request->variants as $variant) {
            if (!empty($variant['nama_varian'])) {
                $product->variants()->create([
                    'nama_varian' => $variant['nama_varian'],
                    'stock'       => $variant['stock'] ?? 0,
                    'price'       => $variant['price'] ?? null,
                ]);
            }
        }
    }

    return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
}

public function index(Request $request)
{
    $query = Product::query();

    if ($request->filled('search'))       $query->where('name', 'like', '%' . $request->search . '%');
    if ($request->filled('kode_barang'))  $query->where('kode_barang', 'like', '%' . $request->kode_barang . '%');
    if ($request->filled('vendor'))       $query->where('vendor', $request->vendor);
    if ($request->filled('category'))     $query->where('category', $request->category);
    if ($request->filled('stock_status')) {
        if ($request->stock_status === 'available') $query->where('stock', '>', 5);
        elseif ($request->stock_status === 'low')   $query->where('stock', '>', 0)->where('stock', '<', 6);
        elseif ($request->stock_status === 'empty') $query->where('stock', 0);
    }

    $products    = $query->with('variants')->orderBy('name')->get();
    $categories  = Product::whereNotNull('category')->distinct()->pluck('category');
    $vendors     = Product::whereNotNull('vendor')->distinct()->pluck('vendor');
    $satuanList  = $this->satuanList;

    return view('products.index', compact('products', 'categories', 'vendors', 'satuanList'));
}

public function edit($id)
{
    $product    = Product::with('variants')->findOrFail($id);
    $satuanList = $this->satuanList;
    return view('products.edit', compact('product', 'satuanList'));
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $request->validate([
        'name'        => 'required|string|max:255',
        'kode_barang' => 'nullable|string|max:100|unique:products,kode_barang,' . $id,
        'vendor'      => 'nullable|string|max:255',
        'stock'       => 'nullable|integer|min:0',
        'satuan'      => 'required|string|max:50',
        'price'       => 'required|numeric|min:0',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'category'    => 'nullable|string|max:100',
        'variants.*.nama_varian' => 'nullable|string|max:100',
        'variants.*.stock'       => 'nullable|integer|min:0',
        'variants.*.price'       => 'nullable|numeric|min:0',
    ]);

    $imagePath = $product->image;
    if ($request->hasFile('image')) {
        if ($imagePath) Storage::disk('public')->delete($imagePath);
        $imagePath = $request->file('image')->store('products', 'public');
    }

    $totalStock = $request->stock ?? 0;
    if ($request->filled('variants')) {
        $product->variants()->delete();
        $totalStock = 0;
        foreach ($request->variants as $variant) {
            if (!empty($variant['nama_varian'])) {
                $product->variants()->create([
                    'nama_varian' => $variant['nama_varian'],
                    'stock'       => $variant['stock'] ?? 0,
                    'price'       => $variant['price'] ?? null,
                ]);
                $totalStock += $variant['stock'] ?? 0;
            }
        }
    }

    $product->update([
        'name'        => $request->name,
        'kode_barang' => $request->kode_barang,
        'vendor'      => $request->vendor,
        'stock'       => $totalStock,
        'satuan'      => $request->satuan,
        'price'       => $request->price,
        'image'       => $imagePath,
        'category'    => $request->category,
    ]);

    return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
}

public function destroy($id)
{
    $product = Product::findOrFail($id);

    // Hapus varian terkait
    $product->variants()->delete();

    // Hapus gambar jika ada
    if ($product->image) {
        Storage::disk('public')->delete($product->image);
    }

    $product->delete();

    return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
}

/**
 * Store a new variant for a product (AJAX)
 */
public function storeVariant(Request $request, $productId)
{
    $product = Product::findOrFail($productId);

    $request->validate([
        'nama_varian' => 'required|string|max:100',
        'stock'       => 'required|integer|min:0',
        'price'       => 'nullable|numeric|min:0',
    ]);

    $variant = $product->variants()->create([
        'nama_varian' => $request->nama_varian,
        'stock'       => $request->stock,
        'price'       => $request->price,
    ]);

    // Recalculate parent product total stock
    $product->update(['stock' => $product->variants()->sum('stock')]);

    return response()->json([
        'success' => true,
        'message' => 'Varian berhasil ditambahkan!',
        'variant' => $variant,
        'total_stock' => $product->fresh()->stock,
    ]);
}

/**
 * Update an existing variant (AJAX)
 */
public function updateVariant(Request $request, $variantId)
{
    $variant = ProductVariant::findOrFail($variantId);

    $request->validate([
        'nama_varian' => 'required|string|max:100',
        'stock'       => 'required|integer|min:0',
        'price'       => 'nullable|numeric|min:0',
    ]);

    $variant->update([
        'nama_varian' => $request->nama_varian,
        'stock'       => $request->stock,
        'price'       => $request->price,
    ]);

    // Recalculate parent product total stock
    $product = $variant->product;
    $product->update(['stock' => $product->variants()->sum('stock')]);

    return response()->json([
        'success' => true,
        'message' => 'Varian berhasil diperbarui!',
        'variant' => $variant->fresh(),
        'total_stock' => $product->fresh()->stock,
    ]);
}

/**
 * Delete a variant (AJAX)
 */
public function destroyVariant($variantId)
{
    $variant = ProductVariant::findOrFail($variantId);
    $product = $variant->product;

    $variant->delete();

    // Recalculate parent product total stock
    $product->update(['stock' => $product->variants()->sum('stock')]);

    return response()->json([
        'success' => true,
        'message' => 'Varian berhasil dihapus!',
        'total_stock' => $product->fresh()->stock,
    ]);
}
}
