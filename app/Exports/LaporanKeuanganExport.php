<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\PurchaseOrder;

class LaporanKeuanganExport implements FromView, ShouldAutoSize
{
    protected $type;
    protected $filters;

    public function __construct($type, $filters)
    {
        $this->type = $type;
        $this->filters = $filters;
    }

    public function view(): View
    {
        $filterBulanDari = $this->filters['bulan_dari'] ?? null;
        $filterBulanSampai = $this->filters['bulan_sampai'] ?? null;
        $filterTahun = $this->filters['tahun'] ?? null;
        $filterCustomer = $this->filters['customer_name'] ?? null;
        $filterSupplier = $this->filters['supplier_name'] ?? null;

        // Helper range bulan
        $applyMonthRange = function ($query) use ($filterBulanDari, $filterBulanSampai) {
            if ($filterBulanDari && $filterBulanSampai) {
                if ($filterBulanDari <= $filterBulanSampai) {
                    $query->whereRaw('MONTH(ordered_at) >= ?', [$filterBulanDari])
                          ->whereRaw('MONTH(ordered_at) <= ?', [$filterBulanSampai]);
                } else {
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

        if ($this->type === 'penjualan') {
            $query = Order::with('user')->latest('ordered_at');
            $applyMonthRange($query);
            if ($filterTahun) $query->whereYear('ordered_at', $filterTahun);
            if ($filterCustomer) $query->where('customer_name', $filterCustomer);
            
            $orders = $query->get();
            $totalPrice = $orders->sum('total_price');
            $totalPpn = $orders->sum(fn($o) => ($o->use_ppn ?? true) ? round($o->total_price * 0.11) : 0);
            $grandTotal = $totalPrice + $totalPpn;

            return view('exports.laporan-penjualan', [
                'orders' => $orders,
                'totalPrice' => $totalPrice,
                'totalPpn' => $totalPpn,
                'grandTotal' => $grandTotal,
                'filters' => $this->filters
            ]);

        } elseif ($this->type === 'pembelian') {
            $query = PurchaseOrder::with('user')->latest('ordered_at');
            $applyMonthRange($query);
            if ($filterTahun) $query->whereYear('ordered_at', $filterTahun);
            if ($filterSupplier) $query->where('supplier_name', $filterSupplier);

            $pos = $query->get();
            $totalPrice = $pos->sum('total_price');
            $totalPpn = $pos->sum(fn($p) => ($p->use_ppn ?? true) ? round($p->total_price * 0.11) : 0);
            $grandTotal = $totalPrice + $totalPpn;

            return view('exports.laporan-pembelian', [
                'pos' => $pos,
                'totalPrice' => $totalPrice,
                'totalPpn' => $totalPpn,
                'grandTotal' => $grandTotal,
                'filters' => $this->filters
            ]);

        } else {
            // Ringkasan Bulanan
            $orderQuery = DB::table('orders');
            $applyMonthRange($orderQuery);
            if ($filterTahun) $orderQuery->whereYear('ordered_at', $filterTahun);
            if ($filterCustomer) $orderQuery->where('customer_name', $filterCustomer);

            $laporanPenjualan = $orderQuery->select(
                DB::raw('MONTH(ordered_at) as bulan'),
                DB::raw('YEAR(ordered_at) as tahun'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_price) as total')
            )->groupBy(DB::raw('YEAR(ordered_at)'), DB::raw('MONTH(ordered_at)'))
            ->orderBy(DB::raw('YEAR(ordered_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(ordered_at)'), 'desc')
            ->get();

            $poQuery = DB::table('purchase_orders');
            $applyMonthRange($poQuery);
            if ($filterTahun) $poQuery->whereYear('ordered_at', $filterTahun);
            if ($filterSupplier) $poQuery->where('supplier_name', $filterSupplier);

            $laporanPembelian = $poQuery->select(
                DB::raw('MONTH(ordered_at) as bulan'),
                DB::raw('YEAR(ordered_at) as tahun'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total_price) as total')
            )->groupBy(DB::raw('YEAR(ordered_at)'), DB::raw('MONTH(ordered_at)'))
            ->orderBy(DB::raw('YEAR(ordered_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(ordered_at)'), 'desc')
            ->get();

            return view('exports.laporan-ringkasan', [
                'laporanPenjualan' => $laporanPenjualan,
                'laporanPembelian' => $laporanPembelian,
                'filters' => $this->filters
            ]);
        }
    }
}
