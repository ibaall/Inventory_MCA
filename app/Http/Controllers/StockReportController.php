<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Exports\StockReportExport;
use Carbon\Carbon;

class StockReportController extends Controller
{
    /**
     * Display the stock report table.
     * Combines purchase (stock in) and sales (stock out) transactions
     * to show a chronological stock movement per product.
     */
    public function index(Request $request)
    {
        // --- Filters ---
        $filterProduct   = $request->get('product_id');
        $filterCustomer  = $request->get('customer');
        $filterSupplier  = $request->get('supplier');
        $filterDateFrom  = $request->get('date_from');
        $filterDateTo    = $request->get('date_to');

        // --- Dropdown data ---
        $products  = Product::orderBy('name')->get();
        $customers = Order::whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()->orderBy('customer_name')->pluck('customer_name');
        $suppliers = PurchaseOrder::select('supplier_name')
            ->distinct()->orderBy('supplier_name')->pluck('supplier_name');

        // --- Build stock movements ---
        // 1. Sales (stock out) - from order_items joined with orders
        $salesQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                DB::raw("'sale' as type"),
                'orders.ordered_at as tanggal',
                'products.id as product_id',
                'products.kode_barang',
                'products.name as nama_barang',
                'orders.customer_name as pelanggan',
                DB::raw("NULL as supplier"),
                'orders.invoice_number',
                'orders.surat_jalan_number',
                DB::raw("NULL as po_number"),
                'order_items.quantity',
                'products.stock as current_stock'
            );

        if ($filterProduct) {
            $salesQuery->where('order_items.product_id', $filterProduct);
        }
        if ($filterCustomer) {
            $salesQuery->where('orders.customer_name', $filterCustomer);
        }
        if ($filterDateFrom) {
            $salesQuery->whereDate('orders.ordered_at', '>=', $filterDateFrom);
        }
        if ($filterDateTo) {
            $salesQuery->whereDate('orders.ordered_at', '<=', $filterDateTo);
        }

        // 2. Purchases (stock in) - from purchase_order_items joined with purchase_orders
        //    Only count "diterima" status POs as actual stock in
        $purchaseQuery = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('products', 'purchase_order_items.product_id', '=', 'products.id')
            ->select(
                DB::raw("'purchase' as type"),
                'purchase_orders.ordered_at as tanggal',
                'products.id as product_id',
                'products.kode_barang',
                'products.name as nama_barang',
                DB::raw("NULL as pelanggan"),
                'purchase_orders.supplier_name as supplier',
                DB::raw("NULL as invoice_number"),
                DB::raw("NULL as surat_jalan_number"),
                'purchase_orders.po_number',
                'purchase_order_items.quantity',
                'products.stock as current_stock'
            )
            ->where('purchase_orders.status', 'diterima');

        if ($filterProduct) {
            $purchaseQuery->where('purchase_order_items.product_id', $filterProduct);
        }
        if ($filterSupplier) {
            $purchaseQuery->where('purchase_orders.supplier_name', $filterSupplier);
        }
        if ($filterDateFrom) {
            $purchaseQuery->whereDate('purchase_orders.ordered_at', '>=', $filterDateFrom);
        }
        if ($filterDateTo) {
            $purchaseQuery->whereDate('purchase_orders.ordered_at', '<=', $filterDateTo);
        }

        // Combine and sort by date
        $salesData    = $salesQuery->get();
        $purchaseData = $purchaseQuery->get();

        $allMovements = $salesData->merge($purchaseData)->sortByDesc('tanggal')->values();

        // --- Calculate running stock (stok awal) for each movement ---
        // Group by product and calculate reverse running total
        $movementsByProduct = $allMovements->groupBy('product_id');
        $processedMovements = collect();

        foreach ($movementsByProduct as $productId => $movements) {
            $currentStock = $movements->first()->current_stock;

            // Process in reverse chronological order to compute stok_awal
            // Current stock is what we have NOW
            // Working backwards: stok_akhir = current_stock (for most recent)
            // For each transaction going back:
            //   If sale: stok_akhir = stok_awal - quantity_sold
            //   If purchase: stok_akhir = stok_awal + quantity_purchased
            
            $runningStock = $currentStock;
            $sortedMovements = $movements->sortByDesc('tanggal')->values();

            foreach ($sortedMovements as $index => $movement) {
                $movement->stok_akhir = $runningStock;
                
                if ($movement->type === 'sale') {
                    // Before this sale, stock was higher
                    $movement->stok_awal = $runningStock + $movement->quantity;
                    $movement->penjualan = $movement->quantity;
                    $movement->pembelian = 0;
                } else {
                    // Before this purchase, stock was lower
                    $movement->stok_awal = $runningStock - $movement->quantity;
                    $movement->penjualan = 0;
                    $movement->pembelian = $movement->quantity;
                }

                $runningStock = $movement->stok_awal;
                $processedMovements->push($movement);
            }
        }

        // Sort final result by date descending
        $stockMovements = $processedMovements->sortByDesc('tanggal')->values();

        // Paginate manually
        $perPage = 25;
        $currentPage = $request->get('page', 1);
        $pagedData = $stockMovements->forPage($currentPage, $perPage);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $stockMovements->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $filters = compact('filterProduct', 'filterCustomer', 'filterSupplier', 'filterDateFrom', 'filterDateTo');

        return view('stock-report.index', compact(
            'paginator', 'products', 'customers', 'suppliers', 'filters'
        ));
    }

    /**
     * Export stock report to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'product_id' => $request->query('product_id'),
            'customer'   => $request->query('customer'),
            'supplier'   => $request->query('supplier'),
            'date_from'  => $request->query('date_from'),
            'date_to'    => $request->query('date_to'),
        ];

        $filename = 'laporan_stok_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new StockReportExport($filters), $filename);
    }
}
