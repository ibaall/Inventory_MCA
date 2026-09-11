<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class MasterDataController extends Controller
{
    /**
     * Halaman utama Master Data (Supplier & Customer dalam satu halaman)
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $products  = Product::with('variants')->orderBy('name')->get();
        $satuanList = ['Pcs', 'Pax', 'Rol', 'Set', 'Box', 'Lusin', 'Botol', 'Lembar', 'Unit', 'Buah'];

        return view('master-data.index', compact('suppliers', 'customers', 'products', 'satuanList'));
    }

    // ===== SUPPLIER =====

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:suppliers,name',
            'alamat'    => 'nullable|string|max:1000',
            'telepon'   => 'nullable|string|max:50',
            'rekening'  => 'nullable|string|max:255',
        ]);

        Supplier::create($request->only('name', 'alamat', 'telepon', 'rekening'));

        return redirect()->route('master-data.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'alamat'    => 'nullable|string|max:1000',
            'telepon'   => 'nullable|string|max:50',
            'rekening'  => 'nullable|string|max:255',
        ]);

        $supplier->update($request->only('name', 'alamat', 'telepon', 'rekening'));

        return redirect()->route('master-data.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroySupplier(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('master-data.index')->with('success', 'Supplier berhasil dihapus.');
    }

    // ===== CUSTOMER =====

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:customers,name',
            'alamat'  => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
        ]);

        Customer::create($request->only('name', 'alamat', 'telepon'));

        return redirect()->route('master-data.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'alamat'  => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
        ]);

        $customer->update($request->only('name', 'alamat', 'telepon'));

        return redirect()->route('master-data.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroyCustomer(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('master-data.index')->with('success', 'Customer berhasil dihapus.');
    }

    // ===== API: JSON untuk dropdown AJAX =====

    public function getSuppliers()
    {
        return response()->json(Supplier::orderBy('name')->get());
    }

    public function getCustomers()
    {
        return response()->json(Customer::orderBy('name')->get());
    }

    // ===== PRODUCT =====

    public function storeProduct(Request $request)
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
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name'        => $request->name,
            'kode_barang' => $request->kode_barang,
            'vendor'      => $request->vendor,
            'stock'       => $request->stock,
            'satuan'      => $request->satuan,
            'price'       => $request->price,
            'image'       => $imagePath,
            'category'    => $request->category,
        ]);

        Cache::forget('product_categories');
        Cache::forget('product_vendors');

        return redirect()->route('master-data.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'kode_barang' => 'nullable|string|max:100|unique:products,kode_barang,' . $product->id,
            'vendor'      => 'nullable|string|max:255',
            'stock'       => 'nullable|integer|min:0',
            'satuan'      => 'required|string|max:50',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category'    => 'nullable|string|max:100',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name'        => $request->name,
            'kode_barang' => $request->kode_barang,
            'vendor'      => $request->vendor,
            'stock'       => $request->has('stock') ? (int) $request->stock : null,
            'satuan'      => $request->satuan,
            'price'       => $request->price,
            'image'       => $imagePath,
            'category'    => $request->category,
        ]);

        Cache::forget('product_categories');
        Cache::forget('product_vendors');

        return redirect()->route('master-data.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroyProduct(Product $product)
    {
        $product->variants()->delete();

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        Cache::forget('product_categories');
        Cache::forget('product_vendors');

        return redirect()->route('master-data.index')->with('success', 'Produk berhasil dihapus.');
    }
}
