<?php

namespace App\Http\Controllers;

use App\Models\BuktiBank;
use App\Models\NoPerkiraan;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use Illuminate\Http\Request;

class BuktiBankController extends Controller
{
    public function indexMasuk(Request $request) { return $this->index($request, 'BBM'); }
    public function indexKeluar(Request $request) { return $this->index($request, 'BBK'); }

    private function index(Request $request, string $jenis)
    {
        $query = BuktiBank::with('bankAccount')->where('jenis', $jenis)->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_bukti', 'like', "%{$s}%")
                  ->orWhere('pihak', 'like', "%{$s}%")
                  ->orWhere('keterangan_utama', 'like', "%{$s}%")
                  ->orWhere('no_invoice', 'like', "%{$s}%")
                  ->orWhere('no_po', 'like', "%{$s}%");
            });
        }
        if ($request->filled('tanggal_awal')) $query->whereDate('tanggal', '>=', $request->tanggal_awal);
        if ($request->filled('tanggal_akhir')) $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        $data = $query->paginate(15);
        return view('bukti-bank.index', ['data' => $data, 'jenis' => $jenis]);
    }

    public function createMasuk()
    {
        $invoices = Order::orderBy('invoice_number', 'desc')->get()->map(function ($o) {
            return [
                'id' => $o->id,
                'invoice_number' => $o->invoice_number,
                'customer_name' => $o->customer_name,
                'total' => $o->total_price,
                'remaining' => $o->remaining,
            ];
        });

        return view('bukti-bank.create', [
            'jenis' => 'BBM',
            'noBukti' => BuktiBank::generateNoBukti('BBM'),
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
            'invoices' => $invoices,
            'purchaseOrders' => collect(),
        ]);
    }

    public function createKeluar()
    {
        $purchaseOrders = PurchaseOrder::orderBy('po_number', 'desc')->get()->map(function ($po) {
            return [
                'id' => $po->id,
                'po_number' => $po->po_number,
                'supplier_name' => $po->supplier_name,
                'total' => $po->total_price,
                'remaining' => $po->remaining,
            ];
        });

        return view('bukti-bank.create', [
            'jenis' => 'BBK',
            'noBukti' => BuktiBank::generateNoBukti('BBK'),
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
            'invoices' => collect(),
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:BBM,BBK',
            'tanggal' => 'required|date',
            'pihak' => 'required|string|max:255',
            'bank_account_id' => 'nullable|exists:no_perkiraans,id',
            'items' => 'required|array|min:1',
            'items.*.keterangan' => 'required|string|max:500',
            'items.*.jumlah' => 'required|string',
        ]);

        $detailRows = [];
        $total = 0;
        $urutan = 0;

        foreach ($request->input('items', []) as $item) {
            $jumlah = intval(str_replace(['.', ','], '', $item['jumlah'] ?? '0'));
            if ($jumlah <= 0) continue;
            $urutan++;
            $total += $jumlah;

            $perkiraan = null;
            if (!empty($item['no_perkiraan_id'])) {
                $perkiraan = NoPerkiraan::find($item['no_perkiraan_id']);
            }

            $detailRows[] = [
                'no_perkiraan_id' => $perkiraan?->id,
                'kode_perkiraan' => $perkiraan?->kode_perkiraan ?? ($item['kode_perkiraan'] ?? null),
                'nama_perkiraan' => $perkiraan?->nama_perkiraan ?? ($item['nama_perkiraan'] ?? null),
                'keterangan' => $item['keterangan'],
                'jumlah' => $jumlah,
                'urutan' => $urutan,
            ];
        }

        if (empty($detailRows)) {
            return back()->withInput()->withErrors(['items' => 'Minimal harus ada 1 detail transaksi dengan jumlah lebih dari 0.']);
        }

        $terbilang = \App\Http\Controllers\BuktiKasController::angkaKeTerbilang($total) . ' Rupiah';

        $bukti = BuktiBank::create([
            'jenis' => $request->jenis,
            'no_bukti' => $request->no_bukti,
            'tanggal' => $request->tanggal,
            'pihak' => $request->pihak,
            'bank_account_id' => $request->bank_account_id,
            'no_invoice' => $request->no_invoice,
            'no_po' => $request->no_po,
            'bg_cheque_no' => $request->bg_cheque_no,
            'keterangan_utama' => $request->keterangan_utama,
            'total' => $total,
            'terbilang' => $terbilang,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($detailRows as $row) {
            $bukti->details()->create($row);
        }

        // Auto-reduce piutang jika BBM + ada invoice
        if ($request->jenis === 'BBM' && $request->filled('invoice_id')) {
            $order = Order::find($request->invoice_id);
            if ($order) {
                Payment::create([
                    'transaction_type' => 'sale',
                    'transaction_id' => $order->id,
                    'amount' => $total,
                    'payment_date' => $request->tanggal,
                    'method' => 'bank_transfer',
                    'note' => 'Pembayaran via Bank - ' . ($bukti->no_bukti ?? ''),
                ]);
            }
        }

        // Auto-reduce hutang jika BBK + ada PO
        if ($request->jenis === 'BBK' && $request->filled('po_id')) {
            $po = PurchaseOrder::find($request->po_id);
            if ($po) {
                Payment::create([
                    'transaction_type' => 'purchase',
                    'transaction_id' => $po->id,
                    'amount' => $total,
                    'payment_date' => $request->tanggal,
                    'method' => 'bank_transfer',
                    'note' => 'Pembayaran via Bank - ' . ($bukti->no_bukti ?? ''),
                ]);
            }
        }

        $route = $request->jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index';
        $label = $request->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar';
        return redirect()->route($route)->with('success', "{$label} berhasil disimpan.");
    }

    public function show($id)
    {
        $bukti = BuktiBank::with('details', 'bankAccount', 'creator')->findOrFail($id);
        return view('bukti-bank.show', compact('bukti'));
    }

    public function edit($id)
    {
        $bukti = BuktiBank::with('details')->findOrFail($id);

        $invoices = $bukti->jenis === 'BBM' ? Order::orderBy('invoice_number', 'desc')->get()->map(function ($o) {
            return [
                'id' => $o->id,
                'invoice_number' => $o->invoice_number,
                'customer_name' => $o->customer_name,
                'total' => $o->total_price,
                'remaining' => $o->remaining,
            ];
        }) : collect();

        $purchaseOrders = $bukti->jenis === 'BBK' ? PurchaseOrder::orderBy('po_number', 'desc')->get()->map(function ($po) {
            return [
                'id' => $po->id,
                'po_number' => $po->po_number,
                'supplier_name' => $po->supplier_name,
                'total' => $po->total_price,
                'remaining' => $po->remaining,
            ];
        }) : collect();

        return view('bukti-bank.edit', [
            'bukti' => $bukti,
            'jenis' => $bukti->jenis,
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
            'invoices' => $invoices,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    public function update(Request $request, $id)
    {
        $bukti = BuktiBank::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'pihak' => 'required|string|max:255',
            'bank_account_id' => 'nullable|exists:no_perkiraans,id',
            'items' => 'required|array|min:1',
            'items.*.keterangan' => 'required|string|max:500',
            'items.*.jumlah' => 'required|string',
        ]);

        $detailRows = [];
        $total = 0;
        $urutan = 0;

        foreach ($request->input('items', []) as $item) {
            $jumlah = intval(str_replace(['.', ','], '', $item['jumlah'] ?? '0'));
            if ($jumlah <= 0) continue;
            $urutan++;
            $total += $jumlah;

            $perkiraan = null;
            if (!empty($item['no_perkiraan_id'])) {
                $perkiraan = NoPerkiraan::find($item['no_perkiraan_id']);
            }

            $detailRows[] = [
                'no_perkiraan_id' => $perkiraan?->id,
                'kode_perkiraan' => $perkiraan?->kode_perkiraan ?? ($item['kode_perkiraan'] ?? null),
                'nama_perkiraan' => $perkiraan?->nama_perkiraan ?? ($item['nama_perkiraan'] ?? null),
                'keterangan' => $item['keterangan'],
                'jumlah' => $jumlah,
                'urutan' => $urutan,
            ];
        }

        if (empty($detailRows)) {
            return back()->withInput()->withErrors(['items' => 'Minimal harus ada 1 detail transaksi dengan jumlah lebih dari 0.']);
        }

        $terbilang = \App\Http\Controllers\BuktiKasController::angkaKeTerbilang($total) . ' Rupiah';

        $bukti->update([
            'no_bukti' => $request->no_bukti,
            'tanggal' => $request->tanggal,
            'pihak' => $request->pihak,
            'bank_account_id' => $request->bank_account_id,
            'no_invoice' => $request->no_invoice,
            'no_po' => $request->no_po,
            'bg_cheque_no' => $request->bg_cheque_no,
            'keterangan_utama' => $request->keterangan_utama,
            'total' => $total,
            'terbilang' => $terbilang,
        ]);

        $bukti->details()->delete();
        foreach ($detailRows as $row) {
            $bukti->details()->create($row);
        }

        $route = $bukti->jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index';
        $label = $bukti->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar';
        return redirect()->route($route)->with('success', "{$label} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $bukti = BuktiBank::findOrFail($id);

        $jenis = $bukti->jenis;
        $bukti->delete();
        $route = $jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index';
        return redirect()->route($route)->with('success', 'Data berhasil dihapus.');
    }

    public function cetak($id)
    {
        $bukti = BuktiBank::with('details', 'bankAccount', 'creator')->findOrFail($id);
        return view('bukti-bank.print', compact('bukti'));
    }

    /**
     * Konfirmasi transaksi Bukti Bank
     */
    public function konfirmasi($id)
    {
        $bukti = BuktiBank::findOrFail($id);
        $bukti->update(['status' => 'konfirmasi']);

        $route = $bukti->jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index';
        $label = $bukti->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar';
        return redirect()->route($route)->with('success', "{$label} berhasil dikonfirmasi.");
    }

    /**
     * API: Get invoices for BBM dropdown
     */
    public function apiInvoices()
    {
        $invoices = Order::orderBy('invoice_number', 'desc')->get()->map(function ($o) {
            return [
                'id' => $o->id,
                'invoice_number' => $o->invoice_number,
                'customer_name' => $o->customer_name,
                'total' => $o->total_price,
                'remaining' => $o->remaining,
            ];
        });
        return response()->json($invoices);
    }

    /**
     * API: Get POs for BBK dropdown
     */
    public function apiPurchaseOrders()
    {
        $pos = PurchaseOrder::orderBy('po_number', 'desc')->get()->map(function ($po) {
            return [
                'id' => $po->id,
                'po_number' => $po->po_number,
                'supplier_name' => $po->supplier_name,
                'total' => $po->total_price,
                'remaining' => $po->remaining,
            ];
        });
        return response()->json($pos);
    }
}
