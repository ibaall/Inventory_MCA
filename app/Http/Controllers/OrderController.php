<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong!');
        }

        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Simpan order ke database
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $totalPrice,
            'ordered_at' => now(),
        ]);

        // Simpan setiap item ke dalam order_items
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('orders.show', $order->id)->with('success', 'Checkout berhasil! Pesanan telah disimpan.');
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    // Method untuk menampilkan form edit
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('orders.edit', compact('order'));
    }

    // Method untuk proses update data pesanan
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status_pembayaran' => 'required|in:lunas,belum dibayar',
            'metode_pembayaran' => 'required|string|max:255',
        ]);

        $order->update([
            'status_pembayaran' => $request->status_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil diperbarui.');
    }

    // Tambahan untuk laporan keuangan
    public function laporanKeuangan()
    {
        $laporan = DB::table('orders')
            ->select(
                DB::raw('MONTH(ordered_at) as bulan'),
                DB::raw('YEAR(ordered_at) as tahun'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_price) as total_pendapatan')
            )
            ->groupBy(DB::raw('YEAR(ordered_at)'), DB::raw('MONTH(ordered_at)'))
            ->orderBy(DB::raw('YEAR(ordered_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(ordered_at)'), 'desc')
            ->get();

        return view('laporan.index', compact('laporan'));
    }


public function exportExcel(Request $request)
{
    $bulan = $request->query('bulan');
    $tahun = $request->query('tahun');

    return Excel::download(new LaporanKeuanganExport($bulan, $tahun), 'laporan_keuangan_' . $bulan . '_' . $tahun . '.xlsx');
}
public function exportInvoicePdf($id)
{
    $order = Order::with(['items.product', 'user'])->findOrFail($id);

    $bulan       = \Carbon\Carbon::parse($order->ordered_at)->format('n');
    $tahun       = \Carbon\Carbon::parse($order->ordered_at)->format('Y');
    $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    $nomorInvoice = str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/INV/MCA/' . $bulanRomawi . '/' . $tahun;

    $dpp    = $order->total_price;
    $ppn    = round($dpp * 0.11);
    $jumlah = $dpp + $ppn;

    $pdf = Pdf::loadView('orders.invoice-pdf', compact('order', 'nomorInvoice', 'dpp', 'ppn', 'jumlah'))
               ->setPaper('a4', 'portrait');

    // ✅ Ganti "/" dengan "-" di nama file agar tidak error
    $namaFile = 'Invoice-' . str_replace('/', '-', $nomorInvoice) . '.pdf';

    return $pdf->download($namaFile);
}
public function printInvoice($id)
{
    $order = Order::with(['items.product', 'user'])->findOrFail($id);

    $bulan        = \Carbon\Carbon::parse($order->ordered_at)->format('n');
    $tahun        = \Carbon\Carbon::parse($order->ordered_at)->format('Y');
    $bulanRomawi  = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    $nomorInvoice = str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/INV/MCA/' . $bulanRomawi . '/' . $tahun;

    $dpp    = $order->total_price;
    $ppn    = round($dpp * 0.11);
    $jumlah = $dpp + $ppn;

    return view('orders.invoice-print', compact('order', 'nomorInvoice', 'dpp', 'ppn', 'jumlah'));
}

/**
 * Export multiple orders into a single combined PDF (struk gabungan).
 * All items from selected orders are merged into one table.
 * Layout identical to single invoice.
 */
public function exportBulkInvoicePdf(Request $request)
{
    $orderIds = $request->query('order_ids', []);

    // Support comma-separated string: ?order_ids=1,2,3
    if (is_string($orderIds)) {
        $orderIds = array_filter(explode(',', $orderIds));
    }

    if (empty($orderIds)) {
        abort(400, 'Pilih minimal 1 pesanan!');
    }

    $orders = Order::with(['items.product', 'user'])
        ->whereIn('id', $orderIds)
        ->orderBy('id')
        ->get();

    if ($orders->isEmpty()) {
        abort(404, 'Pesanan tidak ditemukan.');
    }

    // Gunakan order pertama untuk info pelanggan, tanggal, user
    $order = $orders->first();

    // Gabungkan semua item dari semua order ke dalam satu list
    $allItems = collect();
    foreach ($orders as $o) {
        foreach ($o->items as $item) {
            $allItems->push($item);
        }
    }

    // Generate nomor invoice berdasarkan order pertama
    $bulan       = \Carbon\Carbon::parse($order->ordered_at)->format('n');
    $tahun       = \Carbon\Carbon::parse($order->ordered_at)->format('Y');
    $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    $nomorInvoice = str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/INV/MCA/' . $bulanRomawi . '/' . $tahun;

    // Hitung total gabungan
    $dpp    = $orders->sum('total_price');
    $ppn    = round($dpp * 0.11);
    $jumlah = $dpp + $ppn;

    $pdf = Pdf::loadView('orders.bulk-invoice-pdf', compact(
        'order', 'allItems', 'nomorInvoice', 'dpp', 'ppn', 'jumlah'
    ))->setPaper('a4', 'portrait');

    $namaFile = 'Invoice-' . str_replace('/', '-', $nomorInvoice) . '.pdf';

    return $pdf->download($namaFile);
}

/**
 * Show combined detail page for multiple selected orders.
 */
public function bulkDetail(Request $request)
{
    $orderIds = $request->query('order_ids', []);
    if (is_string($orderIds)) {
        $orderIds = array_filter(explode(',', $orderIds));
    }
    if (empty($orderIds)) {
        abort(400, 'Pilih minimal 1 pesanan!');
    }

    $orders = Order::with(['items.product', 'user'])
        ->whereIn('id', $orderIds)
        ->orderBy('id')
        ->get();

    if ($orders->isEmpty()) {
        abort(404, 'Pesanan tidak ditemukan.');
    }

    // Gabungkan semua item
    $allItems = collect();
    foreach ($orders as $o) {
        foreach ($o->items as $item) {
            $allItems->push($item);
        }
    }

    // Total gabungan
    $dpp    = $orders->sum('total_price');
    $ppn    = round($dpp * 0.11);
    $jumlah = $dpp + $ppn;

    return view('orders.bulk-detail', compact('orders', 'allItems', 'dpp', 'ppn', 'jumlah'));
}

/**
 * Print preview for combined invoice of multiple orders.
 */
public function bulkPrint(Request $request)
{
    $orderIds = $request->query('order_ids', []);
    if (is_string($orderIds)) {
        $orderIds = array_filter(explode(',', $orderIds));
    }
    if (empty($orderIds)) {
        abort(400, 'Pilih minimal 1 pesanan!');
    }

    $orders = Order::with(['items.product', 'user'])
        ->whereIn('id', $orderIds)
        ->orderBy('id')
        ->get();

    if ($orders->isEmpty()) {
        abort(404, 'Pesanan tidak ditemukan.');
    }

    $order = $orders->first();

    $allItems = collect();
    foreach ($orders as $o) {
        foreach ($o->items as $item) {
            $allItems->push($item);
        }
    }

    $bulan       = \Carbon\Carbon::parse($order->ordered_at)->format('n');
    $tahun       = \Carbon\Carbon::parse($order->ordered_at)->format('Y');
    $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    $nomorInvoice = str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/INV/MCA/' . $bulanRomawi . '/' . $tahun;

    $dpp    = $orders->sum('total_price');
    $ppn    = round($dpp * 0.11);
    $jumlah = $dpp + $ppn;

    return view('orders.bulk-invoice-print', compact('order', 'allItems', 'nomorInvoice', 'dpp', 'ppn', 'jumlah'));
}

}
