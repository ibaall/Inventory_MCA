<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->get();
        return view('invoice.index', compact('invoices'));
    }

    public function create()
    {
        $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'total_price' => collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']),
            ]);

            foreach ($cartItems as $id => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);

                // Kurangi stok produk
                $product = Product::find($id);
                if ($product) {
                    $product->stock -= $item['quantity'];
                    $product->save();
                }
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        return view('invoice.show', compact('invoice'));
    }
}
