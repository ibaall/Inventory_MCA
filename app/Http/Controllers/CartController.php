<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class CartController extends Controller
{
    // Menampilkan isi keranjang
    public function index()
    {
        $cart = session()->get('cart', []);
        $customers = \App\Models\Customer::orderBy('name')->get();
        return view('cart.index', compact('cart', 'customers'));
    }

    // Menambahkan produk ke keranjang
    public function addToCart(Request $request, $productId)
{
    $product  = Product::findOrFail($productId);
    $quantity = $request->input('quantity', 1);

    // Jika ada variant_id, gunakan stok varian
    if ($request->filled('variant_id')) {
        $variant = \App\Models\ProductVariant::findOrFail($request->variant_id);

        if ($variant->stock < $quantity) {
            return redirect()->back()->with('error', 'Stok varian tidak mencukupi!');
        }

        $cart      = session()->get('cart', []);
        $cartKey   = $productId . '_' . $variant->id; // key unik per varian

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                "name"        => $product->name . ' - ' . $variant->nama_varian,
                "kode_barang" => $product->kode_barang,
                "vendor"      => $product->vendor,
                "quantity"    => $quantity,
                "satuan"      => $product->satuan ?? 'Pcs',  // ← tambah ini
                "price"       => $variant->price ?? $product->price,
                "image"       => $product->image,
                "category"    => $product->category,
                "variant_id"  => $variant->id,
                "nama_varian" => $variant->nama_varian,
            ];
        }

        session()->put('cart', $cart);

        // Kurangi stok varian & produk
        $variant->decrement('stock', $quantity);
        $product->decrement('stock', $quantity);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    // Tidak ada varian: proses normal
    if ($product->stock < $quantity) {
        return redirect()->back()->with('error', 'Stok tidak mencukupi!');
    }

    $cart = session()->get('cart', []);

    if (isset($cart[$productId])) {
        $cart[$productId]['quantity'] += $quantity;
    } else {
        $cart[$productId] = [
            "name"        => $product->name,
            "kode_barang" => $product->kode_barang,
            "vendor"      => $product->vendor,
            "quantity"    => $quantity,
            "price"       => $product->price,
            "image"       => $product->image,
            "category"    => $product->category,
            "variant_id"  => null,
            "nama_varian" => null,
        ];
    }

    session()->put('cart', $cart);
    $product->decrement('stock', $quantity);

    return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
}

    // Menghapus produk dari keranjang & mengembalikan stok
    public function removeFromCart($cartKey)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$cartKey])) {
        $item     = $cart[$cartKey];
        $quantity = $item['quantity'];

        // Kembalikan stok varian jika ada
        if (!empty($item['variant_id'])) {
            \App\Models\ProductVariant::where('id', $item['variant_id'])
                ->increment('stock', $quantity);
        }

        // Kembalikan stok produk
        // Ambil product_id dari cartKey (format: productId_variantId atau productId)
        $productId = explode('_', $cartKey)[0];
        Product::where('id', $productId)->increment('stock', $quantity);

        unset($cart[$cartKey]);
        session()->put('cart', $cart);
    }

    return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang!');
}

    // Mengosongkan seluruh keranjang belanja
    public function clearCart()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $productId => $item) {
            Product::where('id', $productId)->increment('stock', $item['quantity']);
        }

        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dikosongkan!');
    }

    // Menghapus beberapa item sekaligus dari keranjang (bulk remove)
    public function bulkRemove(Request $request)
    {
        $cartKeys = $request->input('cart_keys', '');
        $keys = array_filter(explode(',', $cartKeys));

        if (empty($keys)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada item yang dipilih.');
        }

        $cart = session()->get('cart', []);
        $removedCount = 0;

        foreach ($keys as $cartKey) {
            if (isset($cart[$cartKey])) {
                $item = $cart[$cartKey];
                $quantity = $item['quantity'];

                // Kembalikan stok varian jika ada
                if (!empty($item['variant_id'])) {
                    \App\Models\ProductVariant::where('id', $item['variant_id'])
                        ->increment('stock', $quantity);
                }

                // Kembalikan stok produk
                $productId = (int) explode('_', $cartKey)[0];
                Product::where('id', $productId)->increment('stock', $quantity);

                unset($cart[$cartKey]);
                $removedCount++;
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', $removedCount . ' item berhasil dihapus dari keranjang!');
    }

    // Method baru: set discount
    public function setDiscount(Request $request, $cartKey)
    {
        $request->validate([
            'discount_percent' => 'required|integer|in:0,5,10,15',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['discount_percent'] = $request->discount_percent;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    // Method baru: Checkout dan simpan pesanan, dengan metode pembayaran
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required|string|max:255',
            'alamat'            => 'nullable|string|max:1000',
            'status_pembayaran' => 'required|in:lunas,belum dibayar',
            'metode_pembayaran' => 'required|in:qris,tunai,transfer,ewallet',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // Determine which items to checkout
        $selectedItemsInput = $request->input('selected_items', '');
        $selectedKeys = array_filter(explode(',', $selectedItemsInput));

        // If no items selected, checkout all items
        if (empty($selectedKeys)) {
            $selectedKeys = array_keys($cart);
        }

        // Filter cart to only selected items
        $checkoutItems = [];
        foreach ($selectedKeys as $key) {
            if (isset($cart[$key])) {
                $checkoutItems[$key] = $cart[$key];
            }
        }

        if (empty($checkoutItems)) {
            return redirect()->route('cart.index')->with('error', 'Item yang dipilih tidak ditemukan.');
        }

        // Calculate total from selected items only, including discount
        $totalPrice = 0;
        foreach ($checkoutItems as $item) {
            $discountPct = $item['discount_percent'] ?? 0;
            $originalPrice = $item['price'];
            $discountedPrice = $discountPct > 0 ? round($originalPrice * (1 - $discountPct / 100)) : $originalPrice;
            $totalPrice += $discountedPrice * $item['quantity'];
        }

        // Check PPN toggle
        $usePpn = $request->has('use_ppn') ? 1 : 0;

        $order = Order::create([
            'user_id'           => Auth::id(),
            'customer_name'     => $request->customer_name,
            'alamat'            => $request->alamat,
            'total_price'       => $totalPrice,
            'ordered_at'        => now(),
            'status_pembayaran' => $request->status_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
            'use_ppn'           => $usePpn,
        ]);

        foreach ($checkoutItems as $cartKey => $item) {
            // ✅ Ambil product_id murni dari cartKey
            // Format cartKey: "63" (tanpa varian) atau "63_3" (dengan varian)
            $productId = (int) explode('_', $cartKey)[0];
            $discountPct = $item['discount_percent'] ?? 0;
            $originalPrice = $item['price'];
            $discountedPrice = $discountPct > 0 ? round($originalPrice * (1 - $discountPct / 100)) : $originalPrice;

            $order->items()->create([
                'product_id'       => $productId,               // ✅ integer bukan "63_3"
                'quantity'         => $item['quantity'],
                'price'            => $discountedPrice,
                'original_price'   => $originalPrice,
                'discount_percent' => $discountPct,
                'subtotal'         => $discountedPrice * $item['quantity'],
            ]);
        }

        // Only remove checked-out items from cart, keep the rest
        foreach ($selectedKeys as $key) {
            unset($cart[$key]);
        }

        if (empty($cart)) {
            session()->forget('cart');
        } else {
            session()->put('cart', $cart);
        }

        // Invalidate cached dropdown data
        Cache::forget('laporan_customers');
        Cache::forget('laporan_available_years');
        Cache::forget('financial_customers');

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat.');
    }

    // Method tambahan: Export laporan keuangan
    public function exportLaporan(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');

        return Excel::download(new LaporanExport($bulan, $tahun), 'laporan_keuangan.xlsx');
    }
}
