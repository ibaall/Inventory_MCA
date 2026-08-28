<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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

        // Invalidate cached dropdown data
        Cache::forget('laporan_customers');
        Cache::forget('laporan_available_years');

        return redirect()->route('orders.show', $order->id)->with('success', 'Checkout berhasil! Pesanan telah disimpan.');
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(25)->withQueryString();
        return view('orders.index', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        // Invalidate cached dropdown data
        Cache::forget('laporan_customers');
        Cache::forget('laporan_available_years');

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $customers = \App\Models\Customer::orderBy('name')->get();
        return view('orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status_pembayaran'   => 'required|in:lunas,belum dibayar',
            'metode_pembayaran'   => 'required|string|max:255',
            'nama_pasien'         => 'nullable|string|max:255',
            'operator'            => 'nullable|string|max:255',
            'tanggal_operasi'     => 'nullable|date',
            'alamat'              => 'nullable|string|max:1000',
            'surat_jalan_number'  => 'nullable|string|max:255',
            'tanggal_jatuh_tempo' => 'nullable|date',
        ]);

        $order->update([
            'status_pembayaran'   => $request->status_pembayaran,
            'metode_pembayaran'   => $request->metode_pembayaran,
            'nama_pasien'         => $request->nama_pasien,
            'operator'            => $request->operator,
            'tanggal_operasi'     => $request->tanggal_operasi,
            'alamat'              => $request->alamat,
            'surat_jalan_number'  => $request->surat_jalan_number,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
        ]);

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil diperbarui.');
    }

    // ===== LAPORAN =====
    public function laporanKeuangan(Request $request)
    {
        // --- Filter parameters ---
        $filterBulanDari  = $request->get('bulan_dari');
        $filterBulanSampai = $request->get('bulan_sampai');
        $filterTahun      = $request->get('tahun');
        $filterCustomer   = $request->get('customer_name');
        $filterSupplier   = $request->get('supplier_name');

        // --- Daftar dropdown untuk filter (cached 10 min) ---
        $customers = Cache::remember('laporan_customers', 600, function () {
            return Order::whereNotNull('customer_name')
                ->where('customer_name', '!=', '')
                ->distinct()->orderBy('customer_name')->pluck('customer_name');
        });

        $suppliers = Cache::remember('laporan_suppliers', 600, function () {
            return \App\Models\PurchaseOrder::select('supplier_name')
                ->distinct()->orderBy('supplier_name')->pluck('supplier_name');
        });

        $availableYears = Cache::remember('laporan_available_years', 600, function () {
            return DB::table('orders')
                ->select(DB::raw('YEAR(ordered_at) as tahun'))
                ->union(
                    DB::table('purchase_orders')->select(DB::raw('YEAR(ordered_at) as tahun'))
                )
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');
        });

        // --- Helper: Apply month range filter ---
        $applyMonthRange = function ($query) use ($filterBulanDari, $filterBulanSampai) {
            if ($filterBulanDari && $filterBulanSampai) {
                if ($filterBulanDari <= $filterBulanSampai) {
                    $query->whereRaw('MONTH(ordered_at) >= ?', [$filterBulanDari])
                          ->whereRaw('MONTH(ordered_at) <= ?', [$filterBulanSampai]);
                } else {
                    // Edge case: wrap around (e.g. Nov-Feb) — unlikely but safe
                    $query->where(function($q) use ($filterBulanDari, $filterBulanSampai) {
                        $q->whereRaw('MONTH(ordered_at) >= ?', [$filterBulanDari])
                           ->orWhereRaw('MONTH(ordered_at) <= ?', [$filterBulanSampai]);
                    });
                }
            } elseif ($filterBulanDari) {
                $query->whereMonth('ordered_at', $filterBulanDari);
            } elseif ($filterBulanSampai) {
                $query->whereMonth('ordered_at', $filterBulanSampai);
            }
            return $query;
        };

        // --- Helper: Apply filters to order query ---
        $applyOrderFilters = function ($query) use ($filterTahun, $filterCustomer, $applyMonthRange) {
            $applyMonthRange($query);
            if ($filterTahun) $query->whereYear('ordered_at', $filterTahun);
            if ($filterCustomer) $query->where('customer_name', $filterCustomer);
            return $query;
        };

        $applyPoFilters = function ($query) use ($filterTahun, $filterSupplier, $applyMonthRange) {
            $applyMonthRange($query);
            if ($filterTahun) $query->whereYear('ordered_at', $filterTahun);
            if ($filterSupplier) $query->where('supplier_name', $filterSupplier);
            return $query;
        };

        // --- Ringkasan penjualan per bulan (filtered) ---
        $laporanPenjualan = $applyOrderFilters(DB::table('orders'))
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

        // --- Ringkasan pembelian (PO) per bulan (filtered) ---
        $laporanPembelian = $applyPoFilters(DB::table('purchase_orders'))
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

        // --- Transaksi penjualan terbaru (detail, filtered, paginated) ---
        $recentOrders = $applyOrderFilters(Order::with('user'))
            ->latest('ordered_at')->paginate(20, ['*'], 'orders_page')->withQueryString();

        // --- Transaksi PO terbaru (detail, filtered, paginated) ---
        $recentPOs = $applyPoFilters(\App\Models\PurchaseOrder::with('user'))
            ->latest('ordered_at')->paginate(20, ['*'], 'po_page')->withQueryString();

        // --- Grand totals (filtered) ---
        $totalPenjualan = $applyOrderFilters(DB::table('orders'))->sum('total_price');
        $totalPembelian = $applyPoFilters(DB::table('purchase_orders'))->sum('total_price');

        // --- Chart data: per-bulan penjualan vs pembelian ---
        $namaBulanArr = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                         7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];

        $chartLabels = [];
        $chartPenjualan = [];
        $chartPembelian = [];

        // Build per-month data from the filtered summaries
        $penjualanByMonth = $laporanPenjualan->keyBy(fn($item) => $item->tahun . '-' . $item->bulan);
        $pembelianByMonth = $laporanPembelian->keyBy(fn($item) => $item->tahun . '-' . $item->bulan);

        // Collect all unique year-months and sort
        $allMonthKeys = $penjualanByMonth->keys()->merge($pembelianByMonth->keys())->unique()->sort();

        foreach ($allMonthKeys as $key) {
            $parts = explode('-', $key);
            $yr = $parts[0];
            $mo = $parts[1];
            $chartLabels[] = ($namaBulanArr[(int)$mo] ?? $mo) . ' ' . $yr;
            $chartPenjualan[] = (float) ($penjualanByMonth->get($key)->total ?? 0);
            $chartPembelian[] = (float) ($pembelianByMonth->get($key)->total ?? 0);
        }

        $chartData = [
            'labels' => $chartLabels,
            'penjualan' => $chartPenjualan,
            'pembelian' => $chartPembelian,
            'totalPenjualan' => $totalPenjualan,
            'totalPembelian' => $totalPembelian,
        ];

        // --- Omset bulanan (DPP + PPN) Jan-Des untuk bar chart ---
        $tahunOmset = $filterTahun ?: now()->year;
        $omsetRaw = DB::table('orders')
            ->select(
                DB::raw('MONTH(ordered_at) as bulan'),
                DB::raw('SUM(total_price) as total_dpp'),
                DB::raw('SUM(CASE WHEN use_ppn = 1 OR use_ppn IS NULL THEN ROUND(total_price * 0.11) ELSE 0 END) as total_ppn')
            )
            ->whereYear('ordered_at', $tahunOmset)
            ->groupBy(DB::raw('MONTH(ordered_at)'))
            ->get()
            ->keyBy('bulan');

        $omsetLabels = [];
        $omsetValues = [];
        $namaBulanFull = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                          7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
        for ($m = 1; $m <= 12; $m++) {
            $omsetLabels[] = $namaBulanFull[$m];
            $row = $omsetRaw->get($m);
            $omsetValues[] = $row ? (float)$row->total_dpp + (float)$row->total_ppn : 0;
        }
        $omsetChartData = [
            'labels' => $omsetLabels,
            'values' => $omsetValues,
            'tahun'  => $tahunOmset,
        ];

        // Backward compat: keep $laporan for old export
        $laporan = $laporanPenjualan;

        // Filters for the view
        $filters = compact('filterBulanDari', 'filterBulanSampai', 'filterTahun', 'filterCustomer', 'filterSupplier');

        return view('laporan.index', compact(
            'laporanPenjualan', 'laporanPembelian',
            'recentOrders', 'recentPOs',
            'totalPenjualan', 'totalPembelian',
            'laporan', 'filters',
            'customers', 'suppliers', 'availableYears',
            'chartData', 'omsetChartData'
        ));
    }

    public function exportExcel(Request $request)
    {
        $type = $request->query('type', 'penjualan');
        $filters = [
            'bulan_dari' => $request->query('bulan_dari'),
            'bulan_sampai' => $request->query('bulan_sampai'),
            'tahun' => $request->query('tahun'),
            'customer_name' => $request->query('customer_name'),
            'supplier_name' => $request->query('supplier_name'),
        ];

        $filename = 'laporan_' . $type . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new LaporanKeuanganExport($type, $filters), $filename);
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
