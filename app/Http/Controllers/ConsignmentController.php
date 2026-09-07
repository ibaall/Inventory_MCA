<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer; // We don't have Customer model, let's just use string input for customer name if needed, but let's check MasterDataController to see how customers are stored. Wait, in Order, customer_name is just string.

class ConsignmentController extends Controller
{
    private function bulanRomawi($bulan)
    {
        return ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    }

    public function index()
    {
        $consignments = Consignment::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('consignments.index', compact('consignments'));
    }

    public function create()
    {
        $products = Product::with('variants')->get();
        $customers = \App\Models\Customer::orderBy('name', 'asc')->get();
        return view('consignments.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_total' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            // Generate Surat Jalan Number - format & urutan menyambung dengan SJ biasa
            $now = now();
            $bulan  = $now->format('n');
            $tahun  = $now->format('Y');
            $romawi = $this->bulanRomawi($bulan);

            // Hitung total SJ yang sudah ada dari KEDUA tabel (orders + consignments)
            $countOrders = \App\Models\Order::whereNotNull('surat_jalan_number')
                ->whereMonth('ordered_at', $bulan)
                ->whereYear('ordered_at', $tahun)
                ->count();
            $countConsign = Consignment::whereNotNull('surat_jalan_number')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->count();

            $seq = str_pad($countOrders + $countConsign + 1, 2, '0', STR_PAD_LEFT);
            $suratJalanNumber = "{$seq}/SJ/MCA/{$romawi}/{$tahun}";

            $consignment = Consignment::create([
                'surat_jalan_number' => $suratJalanNumber,
                'customer_name' => $request->customer_name,
                'status' => 'aktif',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $itemData) {
                // Determine if variant is used (value might be "product_id-variant_id")
                $prodId = $itemData['product_id'];
                $varId = null;
                
                if (str_contains($prodId, '-')) {
                    $parts = explode('-', $prodId);
                    $prodId = $parts[0];
                    $varId = $parts[1];
                }

                $qty = $itemData['qty_total'];

                ConsignmentItem::create([
                    'consignment_id' => $consignment->id,
                    'product_id' => $prodId,
                    'product_variant_id' => $varId,
                    'qty_total' => $qty,
                    'qty_terpakai' => 0,
                    'harga_satuan' => $itemData['harga_satuan'],
                ]);

                // Reduce stock from Gudang
                if ($varId) {
                    $variant = ProductVariant::find($varId);
                    if ($variant) {
                        $variant->decrement('stock', $qty);
                    }
                    // Update main product stock
                    $product = Product::find($prodId);
                    if ($product) {
                        $product->update(['stock' => $product->variants()->sum('stock')]);
                    }
                } else {
                    $product = Product::find($prodId);
                    if ($product) {
                        $product->decrement('stock', $qty);
                    }
                }
            }

            return redirect()->route('consignments.show', $consignment->id)
                ->with('success', 'Konsinyasi berhasil dibuat dan stok telah dikurangi.');
        });
    }

    public function show($id)
    {
        $consignment = Consignment::with(['items.product', 'items.variant', 'user'])->findOrFail($id);
        return view('consignments.show', compact('consignment'));
    }

    public function storeUsage(Request $request, $id)
    {
        $request->validate([
            'usage' => 'required|array',
            'usage.*' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $consignment = Consignment::with('items')->findOrFail($id);
            
            if ($consignment->status === 'selesai') {
                return redirect()->back()->with('error', 'Konsinyasi sudah selesai.');
            }

            $hasUsage = false;
            $totalInvoiceAmount = 0;
            $itemsToInvoice = [];

            foreach ($request->usage as $itemId => $qtyPakai) {
                if (!$qtyPakai || $qtyPakai <= 0) continue;

                $item = $consignment->items->where('id', $itemId)->first();
                if (!$item) continue;

                $sisa = $item->qty_total - $item->qty_terpakai;
                if ($qtyPakai > $sisa) {
                    return redirect()->back()->with('error', 'Qty terpakai melebihi sisa barang konsinyasi.');
                }

                $hasUsage = true;
                $itemTotal = $qtyPakai * $item->harga_satuan;
                $totalInvoiceAmount += $itemTotal;

                $itemsToInvoice[] = [
                    'consignment_item' => $item,
                    'qty' => $qtyPakai,
                    'total' => $itemTotal,
                ];

                $item->increment('qty_terpakai', $qtyPakai);
            }

            if (!$hasUsage) {
                return redirect()->back()->with('error', 'Tidak ada pemakaian yang diinput.');
            }

            // Generate Invoice (Order)
            $now = now();
            $bulan   = $now->format('n');
            $tahun   = $now->format('Y');
            $romawi  = $this->bulanRomawi($bulan);

            $orderCount = Order::whereNotNull('invoice_number')
                ->whereMonth('ordered_at', $bulan)
                ->whereYear('ordered_at', $tahun)
                ->count();
            
            $seq = str_pad($orderCount + 1, 2, '0', STR_PAD_LEFT);
            $invoiceNumber = "{$seq}/INV/MCA/{$romawi}/{$tahun}";

            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $consignment->customer_name,
                'total_price' => $totalInvoiceAmount,
                'ordered_at' => $now,
                'status_pembayaran' => 'belum dibayar',
                'metode_pembayaran' => 'Konsinyasi',
                'invoice_number' => $invoiceNumber,
                // Do not generate SJ for usage, SJ was generated at consignment creation
            ]);

            foreach ($itemsToInvoice as $data) {
                $cItem = $data['consignment_item'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cItem->product_id,
                    'product_variant_id' => $cItem->product_variant_id,
                    'quantity' => $data['qty'],
                    'price' => $cItem->harga_satuan,
                    'subtotal' => $data['total'],
                ]);
            }

            // Check if all items are fully used
            $consignment->refresh();
            $allUsed = true;
            foreach ($consignment->items as $item) {
                if ($item->qty_terpakai < $item->qty_total) {
                    $allUsed = false;
                    break;
                }
            }

            if ($allUsed) {
                $consignment->update(['status' => 'selesai']);
            }

            return redirect()->back()->with('success', "Pemakaian berhasil dicatat dan Invoice {$invoiceNumber} telah dibuat.");
        });
    }

    public function printSuratJalan($id)
    {
        $consignment = Consignment::with(['items.product', 'items.variant'])->findOrFail($id);
        return view('consignments.surat-jalan', compact('consignment'));
    }
}
