<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialReportController extends Controller
{
    /**
     * Helper: Nama bulan Indonesia
     */
    private function namaBulan($bulan)
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $nama[$bulan] ?? '';
    }

    /**
     * Format tanggal Indonesia: 08 Januari 2026
     */
    private function formatTanggal($date)
    {
        $d = Carbon::parse($date);
        return $d->format('d') . ' ' . $this->namaBulan($d->month) . ' ' . $d->format('Y');
    }

    /**
     * Hitung total akhir transaksi termasuk PPN
     */
    private function hitungGrandTotal($totalPrice, $usePpn)
    {
        $ppn = $usePpn ? round($totalPrice * 0.11) : 0;
        return $totalPrice + $ppn;
    }

    /**
     * Halaman utama Laporan Keuangan
     */
    public function index(Request $request)
    {
        $jenisLaporan = $request->get('jenis_laporan', 'purchase_ledger');
        $bulan        = $request->get('bulan', now()->month);
        $tahun        = $request->get('tahun', now()->year);
        $tanggalAwal  = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $supplierName = $request->get('supplier_name');
        $customerName = $request->get('customer_name');

        // Validasi tanggal
        if ($tanggalAwal && $tanggalAkhir && $tanggalAwal > $tanggalAkhir) {
            return back()->withErrors(['tanggal_awal' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.']);
        }

        // Ambil daftar supplier dan customer untuk dropdown filter (cached 10 min)
        $suppliers = Cache::remember('financial_suppliers', 600, function () {
            return PurchaseOrder::select('supplier_name')
                        ->distinct()
                        ->orderBy('supplier_name')
                        ->pluck('supplier_name');
        });

        $customers = Cache::remember('financial_customers', 600, function () {
            return Order::select('customer_name')
                        ->whereNotNull('customer_name')
                        ->where('customer_name', '!=', '')
                        ->distinct()
                        ->orderBy('customer_name')
                        ->pluck('customer_name');
        });

        // Data laporan berdasarkan jenis
        $reportData = [];
        $reportTitle = '';
        $periodLabel = '';

        // Tentukan periode
        if ($tanggalAwal && $tanggalAkhir) {
            $startDate = Carbon::parse($tanggalAwal)->startOfDay();
            $endDate   = Carbon::parse($tanggalAkhir)->endOfDay();
            $periodLabel = $this->formatTanggal($tanggalAwal) . ' s/d ' . $this->formatTanggal($tanggalAkhir);
        } else {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
            $periodLabel = $this->namaBulan($bulan) . ' ' . $tahun;
        }

        switch ($jenisLaporan) {
            case 'purchase_ledger':
                $reportData = $this->getPurchaseLedger($startDate, $endDate, $supplierName);
                $reportTitle = 'Laporan Pembelian / Hutang Supplier';
                break;

            case 'sales_ledger':
                $reportData = $this->getSalesLedger($startDate, $endDate, $customerName);
                $reportTitle = 'Laporan Penjualan / Piutang Customer';
                break;

            case 'purchase_register':
                $reportData = $this->getPurchaseRegister($startDate, $endDate, $supplierName);
                $reportTitle = 'Register Pembelian';
                break;

            case 'sales_register':
                $reportData = $this->getSalesRegister($startDate, $endDate, $customerName);
                $reportTitle = 'Register Penjualan';
                break;
        }

        $filters = [
            'jenis_laporan' => $jenisLaporan,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'tanggal_awal'  => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
            'supplier_name' => $supplierName,
            'customer_name' => $customerName,
        ];

        return view('reports.financial.index', compact(
            'reportData', 'reportTitle', 'periodLabel',
            'suppliers', 'customers', 'filters',
            'jenisLaporan', 'startDate', 'endDate'
        ));
    }

    /**
     * ===================================================
     * LAPORAN PEMBELIAN / HUTANG SUPPLIER
     * ===================================================
     */
    private function getPurchaseLedger($startDate, $endDate, $supplierName = null)
    {
        if (!$supplierName) {
            return ['entries' => collect(), 'saldo_awal' => 0, 'total_pembelian' => 0, 'total_pembayaran' => 0, 'saldo_akhir' => 0];
        }

        // --- Hitung Saldo Awal ---
        $purchasesBefore = PurchaseOrder::where('supplier_name', $supplierName)
            ->where('ordered_at', '<', $startDate)
            ->get();
        $totalPurchaseBefore = $purchasesBefore->sum(function ($po) {
            return $this->hitungGrandTotal($po->total_price, $po->use_ppn);
        });

        $totalPaymentBefore = Payment::where('transaction_type', 'purchase')
            ->where('party_name', $supplierName)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        $saldoAwal = $totalPurchaseBefore - $totalPaymentBefore;

        // --- Ambil transaksi periode berjalan ---
        $purchases = PurchaseOrder::where('supplier_name', $supplierName)
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->orderBy('ordered_at')
            ->orderBy('id')
            ->get();

        // Ambil semua pembayaran periode ini, dikelompokkan per transaction_id
        $allPayments = Payment::where('transaction_type', 'purchase')
            ->where('party_name', $supplierName)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $paymentsByTxId = $allPayments->groupBy('transaction_id');
        $purchaseIds = $purchases->pluck('id')->toArray();

        // --- Bangun entries: setiap PO membawa sub-pembayaran ---
        $entries = collect();

        foreach ($purchases as $po) {
            $grandTotal = $this->hitungGrandTotal($po->total_price, $po->use_ppn);
            $poPayments = $paymentsByTxId->get($po->id, collect());

            $subPayments = $poPayments->map(function ($pay) {
                return [
                    'id'          => $pay->id,
                    'date'        => Carbon::parse($pay->payment_date),
                    'amount'      => $pay->amount,
                    'note'        => $pay->note,
                ];
            })->values()->toArray();

            $entries->push([
                'id'          => $po->id,
                'date'        => Carbon::parse($po->ordered_at),
                'nomor'       => $po->po_number ?: 'PO-' . $po->id,
                'keterangan'  => 'Pembelian dari ' . $po->supplier_name,
                'pembelian'   => $grandTotal,
                'pembayaran'  => 0,
                'type'        => 'purchase',
                'sort_order'  => 0,
                'remaining'   => $po->remaining,
                'payments'    => $subPayments,
            ]);
        }

        // Pembayaran yg tidak punya PO di periode ini (bayar PO periode lalu)
        foreach ($paymentsByTxId as $txId => $pays) {
            if (!in_array($txId, $purchaseIds)) {
                // Cari info PO untuk label
                $relatedPo = PurchaseOrder::find($txId);
                $poLabel = $relatedPo ? ($relatedPo->po_number ?: 'PO-' . $relatedPo->id) : 'PO-' . $txId;

                foreach ($pays as $pay) {
                    $entries->push([
                        'date'        => Carbon::parse($pay->payment_date),
                        'nomor'       => $poLabel,
                        'keterangan'  => 'Pembayaran' . ($pay->note ? ': ' . $pay->note : '') . ' (' . $poLabel . ')',
                        'pembelian'   => 0,
                        'pembayaran'  => $pay->amount,
                        'type'        => 'payment_standalone',
                        'sort_order'  => 1,
                        'payments'    => [],
                    ]);
                }
            }
        }

        // Urutkan: tanggal → sort_order
        $entries = $entries->sortBy([
            ['date', 'asc'],
            ['sort_order', 'asc'],
        ])->values();

        // --- Hitung running balance ---
        $runningBalance = $saldoAwal;
        $totalPembelian = 0;
        $totalPembayaran = 0;

        $entries = $entries->map(function ($entry) use (&$runningBalance, &$totalPembelian, &$totalPembayaran) {
            $entry['saldo_awal_baris'] = $runningBalance;
            $runningBalance += $entry['pembelian'];

            // Hitung pembayaran dari sub-payments
            $subPayTotal = 0;
            if (!empty($entry['payments'])) {
                foreach ($entry['payments'] as &$sp) {
                    $subPayTotal += $sp['amount'];
                }
            }
            // Untuk standalone payment entries
            if ($entry['type'] === 'payment_standalone') {
                $subPayTotal = $entry['pembayaran'];
            }

            $runningBalance -= $subPayTotal;
            $entry['saldo_akhir'] = $runningBalance;
            $entry['total_pembayaran_baris'] = $subPayTotal;
            $totalPembelian += $entry['pembelian'];
            $totalPembayaran += $subPayTotal;
            return $entry;
        });

        return [
            'entries'          => $entries,
            'saldo_awal'       => $saldoAwal,
            'total_pembelian'  => $totalPembelian,
            'total_pembayaran' => $totalPembayaran,
            'saldo_akhir'      => $runningBalance,
        ];
    }

    /**
     * ===================================================
     * LAPORAN PENJUALAN / PIUTANG CUSTOMER
     * ===================================================
     */
    private function getSalesLedger($startDate, $endDate, $customerName = null)
    {
        if (!$customerName) {
            return ['entries' => collect(), 'saldo_awal' => 0, 'total_penjualan' => 0, 'total_pembayaran' => 0, 'saldo_akhir' => 0];
        }

        // --- Hitung Saldo Awal ---
        $salesBefore = Order::where('customer_name', $customerName)
            ->where('ordered_at', '<', $startDate)
            ->get();
        $totalSaleBefore = $salesBefore->sum(function ($order) {
            return $this->hitungGrandTotal($order->total_price, $order->use_ppn);
        });

        $totalPaymentBefore = Payment::where('transaction_type', 'sale')
            ->where('party_name', $customerName)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        $saldoAwal = $totalSaleBefore - $totalPaymentBefore;

        // --- Ambil transaksi periode berjalan ---
        $sales = Order::where('customer_name', $customerName)
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->orderBy('ordered_at')
            ->orderBy('id')
            ->get();

        // Ambil semua pembayaran periode ini, dikelompokkan per transaction_id
        $allPayments = Payment::where('transaction_type', 'sale')
            ->where('party_name', $customerName)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $paymentsByTxId = $allPayments->groupBy('transaction_id');
        $saleIds = $sales->pluck('id')->toArray();

        // --- Bangun entries: setiap Order membawa sub-pembayaran ---
        $entries = collect();

        foreach ($sales as $order) {
            $grandTotal = $this->hitungGrandTotal($order->total_price, $order->use_ppn);
            $orderPayments = $paymentsByTxId->get($order->id, collect());

            $subPayments = $orderPayments->map(function ($pay) {
                return [
                    'id'          => $pay->id,
                    'date'        => Carbon::parse($pay->payment_date),
                    'amount'      => $pay->amount,
                    'note'        => $pay->note,
                ];
            })->values()->toArray();

            $entries->push([
                'id'          => $order->id,
                'date'        => Carbon::parse($order->ordered_at),
                'nomor'       => $order->invoice_number ?: 'INV-' . $order->id,
                'keterangan'  => 'Penjualan ke ' . $order->customer_name,
                'penjualan'   => $grandTotal,
                'pembayaran'  => 0,
                'type'        => 'sale',
                'sort_order'  => 0,
                'remaining'   => $order->remaining,
                'payments'    => $subPayments,
            ]);
        }

        // Pembayaran yg tidak punya Order di periode ini (bayar Order periode lalu)
        foreach ($paymentsByTxId as $txId => $pays) {
            if (!in_array($txId, $saleIds)) {
                $relatedOrder = Order::find($txId);
                $invLabel = $relatedOrder ? ($relatedOrder->invoice_number ?: 'INV-' . $relatedOrder->id) : 'INV-' . $txId;

                foreach ($pays as $pay) {
                    $entries->push([
                        'date'        => Carbon::parse($pay->payment_date),
                        'nomor'       => $invLabel,
                        'keterangan'  => 'Pembayaran' . ($pay->note ? ': ' . $pay->note : '') . ' (' . $invLabel . ')',
                        'penjualan'   => 0,
                        'pembayaran'  => $pay->amount,
                        'type'        => 'payment_standalone',
                        'sort_order'  => 1,
                        'payments'    => [],
                    ]);
                }
            }
        }

        $entries = $entries->sortBy([
            ['date', 'asc'],
            ['sort_order', 'asc'],
        ])->values();

        // --- Hitung running balance ---
        $runningBalance = $saldoAwal;
        $totalPenjualan = 0;
        $totalPembayaran = 0;

        $entries = $entries->map(function ($entry) use (&$runningBalance, &$totalPenjualan, &$totalPembayaran) {
            $entry['saldo_awal_baris'] = $runningBalance;
            $runningBalance += ($entry['penjualan'] ?? 0);

            // Hitung pembayaran dari sub-payments
            $subPayTotal = 0;
            if (!empty($entry['payments'])) {
                foreach ($entry['payments'] as &$sp) {
                    $subPayTotal += $sp['amount'];
                }
            }
            if ($entry['type'] === 'payment_standalone') {
                $subPayTotal = $entry['pembayaran'];
            }

            $runningBalance -= $subPayTotal;
            $entry['saldo_akhir'] = $runningBalance;
            $entry['total_pembayaran_baris'] = $subPayTotal;
            $totalPenjualan += ($entry['penjualan'] ?? 0);
            $totalPembayaran += $subPayTotal;
            return $entry;
        });

        return [
            'entries'          => $entries,
            'saldo_awal'       => $saldoAwal,
            'total_penjualan'  => $totalPenjualan,
            'total_pembayaran' => $totalPembayaran,
            'saldo_akhir'      => $runningBalance,
        ];
    }

    /**
     * ===================================================
     * REGISTER PEMBELIAN (Detail Barang per PO)
     * ===================================================
     */
    private function getPurchaseRegister($startDate, $endDate, $supplierName = null)
    {
        $query = PurchaseOrder::with(['items.product'])
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->orderBy('ordered_at')
            ->orderBy('id');

        if ($supplierName) {
            $query->where('supplier_name', $supplierName);
        }

        $purchaseOrders = $query->get();

        $registerData = collect();
        $grandTotalAll = 0;

        foreach ($purchaseOrders as $po) {
            $poGrandTotal = $this->hitungGrandTotal($po->total_price, $po->use_ppn);
            $ppnRate = $po->use_ppn ? 0.11 : 0;

            foreach ($po->items as $item) {
                $ppnItem = round($item->subtotal * $ppnRate);
                $totalBarang = $item->subtotal + $ppnItem;

                $originalPrice = $item->original_price ?? $item->price;
                $discountPercent = $item->discount_percent ?? 0;

                $registerData->push([
                    'date'                 => Carbon::parse($po->ordered_at),
                    'supplier_name'        => $po->supplier_name,
                    'nomor'                => $po->po_number ?: 'PO-' . $po->id,
                    'nama_barang'          => $item->product->name ?? 'Produk #' . $item->product_id,
                    'nama_varian'          => $item->nama_varian,
                    'qty'                  => $item->quantity,
                    'satuan'               => $item->product->satuan ?? '-',
                    'harga_asli'           => $originalPrice,
                    'discount_percent'     => $discountPercent,
                    'harga'                => $item->price,
                    'ppn'                  => $ppnItem,
                    'total_barang'         => $totalBarang,
                    'total_invoice'        => $poGrandTotal,
                    'po_id'                => $po->id,
                ]);
            }

            $grandTotalAll += $poGrandTotal;
        }

        return [
            'items'       => $registerData,
            'grand_total' => $grandTotalAll,
        ];
    }

    /**
     * ===================================================
     * REGISTER PENJUALAN (Detail Barang per Invoice)
     * ===================================================
     */
    private function getSalesRegister($startDate, $endDate, $customerName = null)
    {
        $query = Order::with(['items.product'])
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->orderBy('ordered_at')
            ->orderBy('id');

        if ($customerName) {
            $query->where('customer_name', $customerName);
        }

        $orders = $query->get();

        $registerData = collect();
        $grandTotalAll = 0;

        foreach ($orders as $order) {
            $orderGrandTotal = $this->hitungGrandTotal($order->total_price, $order->use_ppn);
            $ppnRate = ($order->use_ppn ?? true) ? 0.11 : 0;

            foreach ($order->items as $item) {
                $ppnItem = round($item->subtotal * $ppnRate);
                $totalBarang = $item->subtotal + $ppnItem;

                $originalPrice = $item->original_price ?? $item->price;
                $discountPercent = $item->discount_percent ?? 0;

                $registerData->push([
                    'date'                 => Carbon::parse($order->ordered_at),
                    'customer_name'        => $order->customer_name,
                    'nomor'                => $order->invoice_number ?: 'INV-' . $order->id,
                    'nama_barang'          => $item->product->name ?? 'Produk #' . $item->product_id,
                    'qty'                  => $item->quantity,
                    'satuan'               => $item->product->satuan ?? '-',
                    'harga_asli'           => $originalPrice,
                    'discount_percent'     => $discountPercent,
                    'harga'                => $item->price,
                    'ppn'                  => $ppnItem,
                    'total_barang'         => $totalBarang,
                    'total_invoice'        => $orderGrandTotal,
                    'order_id'             => $order->id,
                ]);
            }

            $grandTotalAll += $orderGrandTotal;
        }

        return [
            'items'       => $registerData,
            'grand_total' => $grandTotalAll,
        ];
    }

    /**
     * ===================================================
     * CETAK (Print View)
     * ===================================================
     */
    public function cetak(Request $request)
    {
        // Sama seperti index tapi pakai view print
        $data = $this->buildReportData($request);
        $data['tanggalCetak'] = $this->formatTanggal(now());
        return view('reports.financial.print', $data);
    }

    /**
     * ===================================================
     * EXPORT PDF
     * ===================================================
     */
    public function pdf(Request $request)
    {
        $data = $this->buildReportData($request);
        $data['tanggalCetak'] = $this->formatTanggal(now());

        $pdf = Pdf::loadView('reports.financial.pdf', $data)
                   ->setPaper('a4', 'landscape');

        $filename = 'Laporan-Keuangan-' . str_replace(' ', '-', $data['periodLabel']) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Helper: Build report data dari request (dipakai di cetak & pdf)
     */
    private function buildReportData(Request $request)
    {
        $jenisLaporan = $request->get('jenis_laporan', 'purchase_ledger');
        $bulan        = $request->get('bulan', now()->month);
        $tahun        = $request->get('tahun', now()->year);
        $tanggalAwal  = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $supplierName = $request->get('supplier_name');
        $customerName = $request->get('customer_name');

        if ($tanggalAwal && $tanggalAkhir) {
            $startDate = Carbon::parse($tanggalAwal)->startOfDay();
            $endDate   = Carbon::parse($tanggalAkhir)->endOfDay();
            $periodLabel = $this->formatTanggal($tanggalAwal) . ' s/d ' . $this->formatTanggal($tanggalAkhir);
        } else {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth()->endOfDay();
            $periodLabel = $this->namaBulan($bulan) . ' ' . $tahun;
        }

        $reportData = [];
        $reportTitle = '';

        switch ($jenisLaporan) {
            case 'purchase_ledger':
                $reportData = $this->getPurchaseLedger($startDate, $endDate, $supplierName);
                $reportTitle = 'Laporan Pembelian / Hutang Supplier';
                break;
            case 'sales_ledger':
                $reportData = $this->getSalesLedger($startDate, $endDate, $customerName);
                $reportTitle = 'Laporan Penjualan / Piutang Customer';
                break;
            case 'purchase_register':
                $reportData = $this->getPurchaseRegister($startDate, $endDate, $supplierName);
                $reportTitle = 'Register Pembelian';
                break;
            case 'sales_register':
                $reportData = $this->getSalesRegister($startDate, $endDate, $customerName);
                $reportTitle = 'Register Penjualan';
                break;
        }

        $filters = compact(
            'jenisLaporan', 'bulan', 'tahun',
            'tanggalAwal', 'tanggalAkhir',
            'supplierName', 'customerName'
        );

        return compact(
            'reportData', 'reportTitle', 'periodLabel',
            'filters', 'jenisLaporan', 'supplierName', 'customerName'
        );
    }
}
