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
    // ===== HELPER: Bulan Romawi =====
    private function bulanRomawi($bulan)
    {
        return ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    }

    // ===== HELPER: Generate & assign invoice number =====
    private function assignInvoiceNumber(Order $order)
    {
        if ($order->invoice_number) {
            return $order->invoice_number;
        }

        $tanggal = \Carbon\Carbon::parse($order->ordered_at);
        $bulan   = $tanggal->format('n');
        $tahun   = $tanggal->format('Y');
        $romawi  = $this->bulanRomawi($bulan);

        // Hitung urutan: berapa invoice sudah terbit di bulan+tahun ini
        $count = Order::whereNotNull('invoice_number')
            ->whereMonth('ordered_at', $bulan)
            ->whereYear('ordered_at', $tahun)
            ->count();

        $seq = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        $nomorInvoice = "{$seq}/INV/MCA/{$romawi}/{$tahun}";

        $order->update(['invoice_number' => $nomorInvoice]);

        return $nomorInvoice;
    }

    // ===== HELPER: Generate & assign surat jalan number =====
    private function assignSuratJalanNumber(Order $order)
    {
        if ($order->surat_jalan_number) {
            return $order->surat_jalan_number;
        }

        $tanggal = \Carbon\Carbon::parse($order->ordered_at);
        $bulan   = $tanggal->format('n');
        $tahun   = $tanggal->format('Y');
        $romawi  = $this->bulanRomawi($bulan);

        // Hitung urutan: berapa surat jalan sudah terbit di bulan+tahun ini
        $count = Order::whereNotNull('surat_jalan_number')
            ->whereMonth('ordered_at', $bulan)
            ->whereYear('ordered_at', $tahun)
            ->count();

        $seq = str_pad($count + 1, 2, '0', STR_PAD_LEFT);

        // Dengan PPN: N-XX/SJ/MCA/V/2026, Tanpa PPN: XX/SJ/MCA/V/2026
        $usePpn = $order->use_ppn ?? true;
        if ($usePpn) {
            $nomorSJ = "N-{$seq}/SJ/MCA/{$romawi}/{$tahun}";
        } else {
            $nomorSJ = "{$seq}/SJ/MCA/{$romawi}/{$tahun}";
        }

        $order->update(['surat_jalan_number' => $nomorSJ]);

        return $nomorSJ;
    }

    // ===== HELPER: Prepare invoice data =====
    private function prepareInvoiceData(Order $order)
    {
        $nomorInvoice = $this->assignInvoiceNumber($order);
        $nomorSJ      = $order->surat_jalan_number ?? '-';

        $dpp    = $order->total_price;
        $usePpn = $order->use_ppn ?? true;
        $ppn    = $usePpn ? round($dpp * 0.11) : 0;
        $jumlah = $dpp + $ppn;

        $tanggal = \Carbon\Carbon::parse($order->ordered_at);

        return compact('nomorInvoice', 'nomorSJ', 'dpp', 'ppn', 'jumlah', 'usePpn', 'tanggal');
    }

    // ===== CRUD =====
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong!');
        }

        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $totalPrice,
            'ordered_at' => now(),
        ]);

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

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('orders.edit', compact('order'));
    }

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

    // ===== LAPORAN =====
    public function laporanKeuangan()
    {
        // Ringkasan penjualan per bulan
        $laporanPenjualan = DB::table('orders')
            ->select(
                DB::raw('MONTH(ordered_at) as bulan'),
                DB::raw('YEAR(ordered_at) as tahun'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy(DB::raw('YEAR(ordered_at)'), DB::raw('MONTH(ordered_at)'))
            ->orderBy(DB::raw('YEAR(ordered_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(ordered_at)'), 'desc')
            ->get();

        // Ringkasan pembelian (PO) per bulan
        $laporanPembelian = DB::table('purchase_orders')
            ->select(
                DB::raw('MONTH(ordered_at) as bulan'),
                DB::raw('YEAR(ordered_at) as tahun'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy(DB::raw('YEAR(ordered_at)'), DB::raw('MONTH(ordered_at)'))
            ->orderBy(DB::raw('YEAR(ordered_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(ordered_at)'), 'desc')
            ->get();

        // Transaksi penjualan terbaru (detail)
        $recentOrders = Order::with('user')->latest('ordered_at')->take(20)->get();

        // Transaksi PO terbaru (detail)
        $recentPOs = \App\Models\PurchaseOrder::with('user')->latest('ordered_at')->take(20)->get();

        // Grand totals
        $totalPenjualan = DB::table('orders')->sum('total_price');
        $totalPembelian = DB::table('purchase_orders')->sum('total_price');

        // Backward compat: keep $laporan for old export
        $laporan = $laporanPenjualan;

        return view('laporan.index', compact(
            'laporanPenjualan', 'laporanPembelian',
            'recentOrders', 'recentPOs',
            'totalPenjualan', 'totalPembelian',
            'laporan'
        ));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');

        return Excel::download(new LaporanKeuanganExport($bulan, $tahun), 'laporan_keuangan_' . $bulan . '_' . $tahun . '.xlsx');
    }

    // ===== INVOICE PDF =====
    public function exportInvoicePdf($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        $data  = $this->prepareInvoiceData($order);

        $pdf = Pdf::loadView('orders.invoice-pdf', array_merge(['order' => $order], $data))
                   ->setPaper('a4', 'portrait');

        $namaFile = 'Invoice-' . str_replace('/', '-', $data['nomorInvoice']) . '.pdf';
        return $pdf->download($namaFile);
    }

    // ===== INVOICE PRINT =====
    public function printInvoice($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        $data  = $this->prepareInvoiceData($order);

        return view('orders.invoice-print', array_merge(['order' => $order], $data));
    }

    // ===== SURAT JALAN PDF =====
    public function exportSuratJalanPdf($id)
    {
        $order   = Order::with(['items.product', 'user'])->findOrFail($id);
        $nomorSJ = $this->assignSuratJalanNumber($order);
        $tanggal = \Carbon\Carbon::parse($order->ordered_at);

        $pdf = Pdf::loadView('orders.surat-jalan-pdf', compact('order', 'nomorSJ', 'tanggal'))
                   ->setPaper('a4', 'portrait');

        $namaFile = 'SuratJalan-' . str_replace('/', '-', $nomorSJ) . '.pdf';
        return $pdf->download($namaFile);
    }

    // ===== SURAT JALAN PRINT =====
    public function printSuratJalan($id)
    {
        $order   = Order::with(['items.product', 'user'])->findOrFail($id);
        $nomorSJ = $this->assignSuratJalanNumber($order);
        $tanggal = \Carbon\Carbon::parse($order->ordered_at);

        return view('orders.surat-jalan-print', compact('order', 'nomorSJ', 'tanggal'));
    }

    // ===== BULK INVOICE PDF =====
    public function exportBulkInvoicePdf(Request $request)
    {
        $orderIds = $request->query('order_ids', []);
        if (is_string($orderIds)) {
            $orderIds = array_filter(explode(',', $orderIds));
        }
        if (empty($orderIds)) {
            abort(400, 'Pilih minimal 1 pesanan!');
        }

        $orders = Order::with(['items.product', 'user'])
            ->whereIn('id', $orderIds)->orderBy('id')->get();

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

        $nomorInvoice = $this->assignInvoiceNumber($order);
        $nomorSJ      = $order->surat_jalan_number ?? '-';

        $dpp    = $orders->sum('total_price');
        $usePpn = $order->use_ppn ?? true;
        $ppn    = $usePpn ? round($dpp * 0.11) : 0;
        $jumlah = $dpp + $ppn;
        $tanggal = \Carbon\Carbon::parse($order->ordered_at);

        $pdf = Pdf::loadView('orders.bulk-invoice-pdf', compact(
            'order', 'allItems', 'nomorInvoice', 'nomorSJ', 'dpp', 'ppn', 'jumlah', 'usePpn', 'tanggal'
        ))->setPaper('a4', 'portrait');

        $namaFile = 'Invoice-' . str_replace('/', '-', $nomorInvoice) . '.pdf';
        return $pdf->download($namaFile);
    }

    // ===== BULK DETAIL =====
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
            ->whereIn('id', $orderIds)->orderBy('id')->get();

        if ($orders->isEmpty()) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        $allItems = collect();
        foreach ($orders as $o) {
            foreach ($o->items as $item) {
                $allItems->push($item);
            }
        }

        $dpp    = $orders->sum('total_price');
        $usePpn = $orders->first()->use_ppn ?? true;
        $ppn    = $usePpn ? round($dpp * 0.11) : 0;
        $jumlah = $dpp + $ppn;

        return view('orders.bulk-detail', compact('orders', 'allItems', 'dpp', 'ppn', 'jumlah', 'usePpn'));
    }

    // ===== BULK PRINT =====
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
            ->whereIn('id', $orderIds)->orderBy('id')->get();

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

        $nomorInvoice = $this->assignInvoiceNumber($order);
        $nomorSJ      = $order->surat_jalan_number ?? '-';

        $dpp    = $orders->sum('total_price');
        $usePpn = $order->use_ppn ?? true;
        $ppn    = $usePpn ? round($dpp * 0.11) : 0;
        $jumlah = $dpp + $ppn;
        $tanggal = \Carbon\Carbon::parse($order->ordered_at);

        return view('orders.bulk-invoice-print', compact('order', 'allItems', 'nomorInvoice', 'nomorSJ', 'dpp', 'ppn', 'jumlah', 'usePpn', 'tanggal'));
    }

}
