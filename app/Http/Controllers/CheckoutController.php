<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        // Ambil data cart dari session (atau tempat lain sesuai implementasi kamu)
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // Hitung total harga
        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // Simpan ke tabel orders
        $order = Order::create([
            'user_id' => Auth::id(), // atau null jika guest
            'customer_name' => $request->customer_name,  // Simpan nama pelanggan di sini
            'total_price' => $totalPrice,
            'ordered_at' => now(),
        ]);

        // Simpan item-item order ke tabel order_items
        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        // Hapus keranjang setelah checkout
        session()->forget('cart');

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat.');
    }
}
