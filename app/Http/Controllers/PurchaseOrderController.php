<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    private function bulanRomawi($bulan)
    {
        return ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan - 1];
    }

    private function assignPoNumber(PurchaseOrder $po)
    {
        if ($po->po_number) {
            return $po->po_number;
        }

        $tanggal = \Carbon\Carbon::parse($po->ordered_at);
        $bulan   = $tanggal->format('n');
        $tahun   = $tanggal->format('Y');
        $romawi  = $this->bulanRomawi($bulan);

        $count = PurchaseOrder::whereNotNull('po_number')
            ->whereMonth('ordered_at', $bulan)
            ->whereYear('ordered_at', $tahun)
            ->count();

        $seq = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        $nomorPO = "{$seq}/PO/MCA/{$romawi}/{$tahun}";

        $po->update(['po_number' => $nomorPO]);

        return $nomorPO;
    }

    // ===== INDEX =====
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('user')->latest()->get();
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    // ===== CREATE =====
    public function create()
    {
        $products = Product::with('variants')->orderBy('name')->get();
        $savedAddresses = PurchaseOrder::whereNotNull('alamat')
            ->where('alamat', '!=', '')
            ->distinct()
            ->pluck('alamat');
        return view('purchase-orders.create', compact('products', 'savedAddresses'));
    }

    // ===== STORE =====
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'alamat'        => 'nullable|string|max:1000',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|integer|min:0|max:60',
        ]);

        $usePpn = $request->has('use_ppn') ? 1 : 0;

        $totalPrice = 0;
        foreach ($request->items as $item) {
            $discountPct = $item['discount_percent'] ?? 0;
            $originalPrice = $item['price'];
            $discountedPrice = $discountPct > 0 ? round($originalPrice * (1 - $discountPct / 100)) : $originalPrice;
            $totalPrice += $discountedPrice * $item['quantity'];
        }

        $po = PurchaseOrder::create([
            'user_id'       => Auth::id(),
            'supplier_name' => $request->supplier_name,
            'alamat'        => $request->alamat,
            'total_price'   => $totalPrice,
            'ordered_at'    => now(),
            'status'        => 'pending',
            'use_ppn'       => $usePpn,
            'catatan'       => $request->catatan,
        ]);

        foreach ($request->items as $item) {
            $variantId   = $item['variant_id'] ?? null;
            $namaVarian  = null;

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                $namaVarian = $variant ? $variant->nama_varian : null;
            }

            $discountPct = $item['discount_percent'] ?? 0;
            $originalPrice = $item['price'];
            $discountedPrice = $discountPct > 0 ? round($originalPrice * (1 - $discountPct / 100)) : $originalPrice;

            PurchaseOrderItem::create([
                'purchase_order_id'  => $po->id,
                'product_id'         => $item['product_id'],
                'product_variant_id' => $variantId ?: null,
                'nama_varian'        => $namaVarian,
                'quantity'           => $item['quantity'],
                'price'              => $discountedPrice,
                'original_price'     => $originalPrice,
                'discount_percent'   => $discountPct,
                'subtotal'           => $discountedPrice * $item['quantity'],
            ]);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil dibuat.');
    }

    // ===== SHOW =====
    public function show($id)
    {
        $po = PurchaseOrder::with('items.product', 'items.variant', 'user')->findOrFail($id);
        return view('purchase-orders.show', compact('po'));
    }

    // ===== TERIMA (update stock) =====
    public function terima($id)
    {
        $po = PurchaseOrder::with('items.product', 'items.variant')->findOrFail($id);

        if ($po->status === 'diterima') {
            return redirect()->back()->with('error', 'PO ini sudah diterima sebelumnya.');
        }

        foreach ($po->items as $item) {
            if ($item->product_variant_id && $item->variant) {
                // Update variant stock
                $item->variant->increment('stock', $item->quantity);

                // Also update parent product total stock (sum of all variants)
                $product = $item->product;
                if ($product) {
                    $totalVariantStock = $product->variants()->sum('stock');
                    $product->update(['stock' => $totalVariantStock]);
                }
            } else {
                // No variant, update main product stock
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        $po->update(['status' => 'diterima']);

        return redirect()->route('purchase-orders.show', $po->id)
            ->with('success', 'Barang diterima! Stok produk/varian telah diperbarui.');
    }

    // ===== DELETE =====
    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        if ($po->status === 'diterima') {
            return redirect()->back()->with('error', 'Tidak bisa menghapus PO yang sudah diterima.');
        }

        $po->items()->delete();
        $po->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil dihapus.');
    }

    // ===== PDF =====
    public function exportPdf($id)
    {
        $po      = PurchaseOrder::with(['items.product', 'items.variant', 'user'])->findOrFail($id);
        $nomorPO = $this->assignPoNumber($po);
        $tanggal = \Carbon\Carbon::parse($po->ordered_at);

        $dpp    = $po->total_price;
        $usePpn = $po->use_ppn ?? true;
        $ppn    = $usePpn ? round($dpp * 0.11) : 0;
        $jumlah = $dpp + $ppn;

        $pdf = Pdf::loadView('purchase-orders.po-pdf', compact('po', 'nomorPO', 'tanggal', 'dpp', 'ppn', 'jumlah', 'usePpn'))
                   ->setPaper('a4', 'portrait');

        $namaFile = 'PO-' . str_replace('/', '-', $nomorPO) . '.pdf';
        return $pdf->download($namaFile);
    }

    // ===== PRINT =====
    public function printPo($id)
    {
        $po      = PurchaseOrder::with(['items.product', 'items.variant', 'user'])->findOrFail($id);
        $nomorPO = $this->assignPoNumber($po);
        $tanggal = \Carbon\Carbon::parse($po->ordered_at);

        $dpp    = $po->total_price;
        $usePpn = $po->use_ppn ?? true;
        $ppn    = $usePpn ? round($dpp * 0.11) : 0;
        $jumlah = $dpp + $ppn;

        return view('purchase-orders.po-print', compact('po', 'nomorPO', 'tanggal', 'dpp', 'ppn', 'jumlah', 'usePpn'));
    }
}
