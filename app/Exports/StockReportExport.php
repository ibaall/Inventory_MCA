<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $counter = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $filterProduct  = $this->filters['product_id'] ?? null;
        $filterCustomer = $this->filters['customer'] ?? null;
        $filterSupplier = $this->filters['supplier'] ?? null;
        $filterDateFrom = $this->filters['date_from'] ?? null;
        $filterDateTo   = $this->filters['date_to'] ?? null;

        // Sales
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

        if ($filterProduct) $salesQuery->where('order_items.product_id', $filterProduct);
        if ($filterCustomer) $salesQuery->where('orders.customer_name', $filterCustomer);
        if ($filterDateFrom) $salesQuery->whereDate('orders.ordered_at', '>=', $filterDateFrom);
        if ($filterDateTo) $salesQuery->whereDate('orders.ordered_at', '<=', $filterDateTo);

        // Purchases
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

        if ($filterProduct) $purchaseQuery->where('purchase_order_items.product_id', $filterProduct);
        if ($filterSupplier) $purchaseQuery->where('purchase_orders.supplier_name', $filterSupplier);
        if ($filterDateFrom) $purchaseQuery->whereDate('purchase_orders.ordered_at', '>=', $filterDateFrom);
        if ($filterDateTo) $purchaseQuery->whereDate('purchase_orders.ordered_at', '<=', $filterDateTo);

        $allMovements = $salesQuery->get()->merge($purchaseQuery->get())->sortByDesc('tanggal')->values();

        // Calculate running stock
        $movementsByProduct = $allMovements->groupBy('product_id');
        $processedMovements = collect();

        foreach ($movementsByProduct as $productId => $movements) {
            $currentStock = $movements->first()->current_stock;
            $runningStock = $currentStock;
            $sortedMovements = $movements->sortByDesc('tanggal')->values();

            foreach ($sortedMovements as $movement) {
                $movement->stok_akhir = $runningStock;
                if ($movement->type === 'sale') {
                    $movement->stok_awal = $runningStock + $movement->quantity;
                    $movement->penjualan = $movement->quantity;
                    $movement->pembelian = 0;
                } else {
                    $movement->stok_awal = $runningStock - $movement->quantity;
                    $movement->penjualan = 0;
                    $movement->pembelian = $movement->quantity;
                }
                $runningStock = $movement->stok_awal;
                $processedMovements->push($movement);
            }
        }

        return $processedMovements->sortByDesc('tanggal')->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Pelanggan/Supplier',
            'No PO',
            'Surat Jalan',
            'Stok Awal',
            'Pembelian',
            'Penjualan',
            'Stok Akhir',
        ];
    }

    public function map($row): array
    {
        $this->counter++;
        return [
            $this->counter,
            \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
            $row->kode_barang ?? '-',
            $row->nama_barang,
            $row->type === 'sale' ? ($row->pelanggan ?? '-') : ($row->supplier ?? '-'),
            $row->po_number ?? '-',
            $row->surat_jalan_number ?? '-',
            $row->stok_awal,
            $row->pembelian > 0 ? '+' . $row->pembelian : '-',
            $row->penjualan > 0 ? '-' . $row->penjualan : '-',
            $row->stok_akhir,
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '212529']]],
        ];
    }
}
