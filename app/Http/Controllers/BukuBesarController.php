<?php

namespace App\Http\Controllers;

use App\Models\BuktiKas;
use App\Models\BuktiBank;
use App\Models\JurnalKoreksi;
use App\Models\NoPerkiraan;
use App\Models\Order;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BukuBesarController extends Controller
{
    /**
     * Halaman Buku Besar (Laporan, bukan input)
     */
    public function index(Request $request)
    {
        $noPerkiraans = NoPerkiraan::orderBy('kode_perkiraan')->get();
        $entries = collect();

        $selectedPerkiraan = null;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $jenisTransaksi = $request->input('jenis_transaksi'); // kas, bank, semua
        $keyword = $request->input('keyword');

        // Compute date range from month/year
        $periodeAwal = sprintf('%04d-%02d-01', $tahun, $bulan);
        $periodeAkhir = \Carbon\Carbon::parse($periodeAwal)->endOfMonth()->format('Y-m-d');

        if ($request->filled('no_perkiraan_id')) {
            $selectedPerkiraan = NoPerkiraan::find($request->no_perkiraan_id);

            if ($selectedPerkiraan) {
                $entries = $this->getEntriesForPerkiraan(
                    $selectedPerkiraan,
                    $periodeAwal,
                    $periodeAkhir,
                    $jenisTransaksi,
                    $keyword
                );
            }
        }

        return view('buku-besar.index', [
            'noPerkiraans' => $noPerkiraans,
            'entries' => $entries,
            'selectedPerkiraan' => $selectedPerkiraan,
            'filters' => [
                'no_perkiraan_id' => $request->no_perkiraan_id,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jenis_transaksi' => $jenisTransaksi,
                'keyword' => $keyword,
            ],
        ]);
    }

    /**
     * Cetak PDF Buku Besar
     */
    public function cetak(Request $request)
    {
        $selectedPerkiraan = null;
        $entries = collect();
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $periodeAwal = sprintf('%04d-%02d-01', $tahun, $bulan);
        $periodeAkhir = \Carbon\Carbon::parse($periodeAwal)->endOfMonth()->format('Y-m-d');

        if ($request->filled('no_perkiraan_id')) {
            $selectedPerkiraan = NoPerkiraan::find($request->no_perkiraan_id);
            if ($selectedPerkiraan) {
                $entries = $this->getEntriesForPerkiraan(
                    $selectedPerkiraan,
                    $periodeAwal,
                    $periodeAkhir,
                    $request->input('jenis_transaksi'),
                    $request->input('keyword')
                );
            }
        }

        return view('buku-besar.print', [
            'entries' => $entries,
            'selectedPerkiraan' => $selectedPerkiraan,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    /**
     * Ambil semua entri Buku Besar untuk Nomor Perkiraan tertentu
     */
    private function getEntriesForPerkiraan(
        NoPerkiraan $perkiraan,
        ?string $periodeAwal,
        ?string $periodeAkhir,
        ?string $jenisTransaksi,
        ?string $keyword
    ): Collection {
        $entries = collect();

        $kodePerkiraan = $perkiraan->kode_perkiraan;
        $perkiraanId = $perkiraan->id;

        // 1. Bukti Kas Keluar (BKK) - debit the expense/asset account
        if (!$jenisTransaksi || $jenisTransaksi === 'kas' || $jenisTransaksi === 'semua') {
            $bkkQuery = BuktiKas::with(['details' => function ($q) use ($perkiraanId) {
                $q->where('no_perkiraan_id', $perkiraanId);
            }])->where('jenis', 'BKK');

            $this->applyDateFilter($bkkQuery, $periodeAwal, $periodeAkhir);

            foreach ($bkkQuery->get() as $bkk) {
                foreach ($bkk->details as $detail) {
                    $ket = $detail->keterangan ?: $bkk->keterangan_utama ?: 'BKK - ' . $bkk->pihak;
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $bkk->tanggal,
                        'keterangan' => $ket,
                        'debit' => $detail->jumlah,
                        'kredit' => 0,
                        'sumber' => 'BKK',
                        'no_bukti' => $bkk->no_bukti,
                    ]);
                }
            }

            // 2. Bukti Kas Masuk (BKM) - credit the revenue/asset account
            $bkmQuery = BuktiKas::with(['details' => function ($q) use ($perkiraanId) {
                $q->where('no_perkiraan_id', $perkiraanId);
            }])->where('jenis', 'BKM');

            $this->applyDateFilter($bkmQuery, $periodeAwal, $periodeAkhir);

            foreach ($bkmQuery->get() as $bkm) {
                foreach ($bkm->details as $detail) {
                    $ket = $detail->keterangan ?: $bkm->keterangan_utama ?: 'BKM - ' . $bkm->pihak;
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $bkm->tanggal,
                        'keterangan' => $ket,
                        'debit' => 0,
                        'kredit' => $detail->jumlah,
                        'sumber' => 'BKM',
                        'no_bukti' => $bkm->no_bukti,
                    ]);
                }
            }
        }

        // 3. Bukti Bank Keluar (BBK) - debit expense/asset
        if (!$jenisTransaksi || $jenisTransaksi === 'bank' || $jenisTransaksi === 'semua') {
            $bbkQuery = BuktiBank::with(['details' => function ($q) use ($perkiraanId) {
                $q->where('no_perkiraan_id', $perkiraanId);
            }])->where('jenis', 'BBK');

            $this->applyDateFilter($bbkQuery, $periodeAwal, $periodeAkhir);

            foreach ($bbkQuery->get() as $bbk) {
                foreach ($bbk->details as $detail) {
                    $ket = $detail->keterangan ?: $bbk->keterangan_utama ?: 'BBK - ' . $bbk->pihak;
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $bbk->tanggal,
                        'keterangan' => $ket,
                        'debit' => $detail->jumlah,
                        'kredit' => 0,
                        'sumber' => 'BBK',
                        'no_bukti' => $bbk->no_bukti,
                    ]);
                }
            }

            // 4. Bukti Bank Masuk (BBM) - credit revenue/asset
            $bbmQuery = BuktiBank::with(['details' => function ($q) use ($perkiraanId) {
                $q->where('no_perkiraan_id', $perkiraanId);
            }])->where('jenis', 'BBM');

            $this->applyDateFilter($bbmQuery, $periodeAwal, $periodeAkhir);

            foreach ($bbmQuery->get() as $bbm) {
                foreach ($bbm->details as $detail) {
                    $ket = $detail->keterangan ?: $bbm->keterangan_utama ?: 'BBM - ' . $bbm->pihak;
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $bbm->tanggal,
                        'keterangan' => $ket,
                        'debit' => 0,
                        'kredit' => $detail->jumlah,
                        'sumber' => 'BBM',
                        'no_bukti' => $bbm->no_bukti,
                    ]);
                }
            }
        }

        // 5. Jurnal Koreksi - menggunakan debit/kredit sesuai input
        if (!$jenisTransaksi || $jenisTransaksi === 'jurnal_koreksi' || $jenisTransaksi === 'semua') {
            $jkQuery = JurnalKoreksi::with(['details' => function ($q) use ($perkiraanId) {
                $q->where('no_perkiraan_id', $perkiraanId);
            }]);

            $this->applyDateFilter($jkQuery, $periodeAwal, $periodeAkhir);

            foreach ($jkQuery->get() as $jk) {
                foreach ($jk->details as $detail) {
                    $ket = $detail->keterangan ?: $jk->keterangan ?: 'Jurnal Koreksi';
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $jk->tanggal,
                        'keterangan' => $ket,
                        'debit' => $detail->debit,
                        'kredit' => $detail->kredit,
                        'sumber' => 'JK',
                        'no_bukti' => $jk->no_jurnal,
                    ]);
                }
            }
        }

        // 6. Purchase Order - kode 700 (Pembelian) debit
        if ($kodePerkiraan === '700') {
            $poQuery = PurchaseOrder::query();
            $this->applyDateFilter($poQuery, $periodeAwal, $periodeAkhir, 'ordered_at');

            foreach ($poQuery->get() as $po) {
                $ket = 'PO - ' . ($po->supplier_name ?? 'Supplier') . ' (' . ($po->po_number ?? '-') . ')';
                if ($keyword && stripos($ket, $keyword) === false) continue;
                $entries->push([
                    'tanggal' => $po->ordered_at ? \Carbon\Carbon::parse($po->ordered_at) : $po->created_at,
                    'keterangan' => $ket,
                    'debit' => $po->total_price,
                    'kredit' => 0,
                    'sumber' => 'PO',
                    'no_bukti' => $po->po_number ?? '-',
                ]);
            }
        }

        // 7. Invoice/Order - kode 600 (Penjualan) kredit
        if ($kodePerkiraan === '600') {
            $orderQuery = Order::query();
            $this->applyDateFilter($orderQuery, $periodeAwal, $periodeAkhir, 'ordered_at');

            foreach ($orderQuery->get() as $order) {
                $ket = 'Invoice - ' . ($order->customer_name ?? 'Customer') . ' (' . ($order->invoice_number ?? '-') . ')';
                if ($keyword && stripos($ket, $keyword) === false) continue;
                $entries->push([
                    'tanggal' => $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at) : $order->created_at,
                    'keterangan' => $ket,
                    'debit' => 0,
                    'kredit' => $order->total_price,
                    'sumber' => 'INV',
                    'no_bukti' => $order->invoice_number ?? '-',
                ]);
            }
        }

        // 8. Invoice - kode 120 (Piutang Usaha) debit
        if ($kodePerkiraan === '120') {
            $orderQuery = Order::query();
            $this->applyDateFilter($orderQuery, $periodeAwal, $periodeAkhir, 'ordered_at');

            foreach ($orderQuery->get() as $order) {
                if ($order->remaining > 0) {
                    $ket = 'Piutang - ' . ($order->customer_name ?? 'Customer') . ' (' . ($order->invoice_number ?? '-') . ')';
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at) : $order->created_at,
                        'keterangan' => $ket,
                        'debit' => $order->remaining,
                        'kredit' => 0,
                        'sumber' => 'INV',
                        'no_bukti' => $order->invoice_number ?? '-',
                    ]);
                }
            }
        }

        // 9. PO - kode 300 (Hutang Usaha) kredit
        if ($kodePerkiraan === '300') {
            $poQuery = PurchaseOrder::query();
            $this->applyDateFilter($poQuery, $periodeAwal, $periodeAkhir, 'ordered_at');

            foreach ($poQuery->get() as $po) {
                if ($po->remaining > 0) {
                    $ket = 'Hutang - ' . ($po->supplier_name ?? 'Supplier') . ' (' . ($po->po_number ?? '-') . ')';
                    if ($keyword && stripos($ket, $keyword) === false) continue;
                    $entries->push([
                        'tanggal' => $po->ordered_at ? \Carbon\Carbon::parse($po->ordered_at) : $po->created_at,
                        'keterangan' => $ket,
                        'debit' => 0,
                        'kredit' => $po->remaining,
                        'sumber' => 'PO',
                        'no_bukti' => $po->po_number ?? '-',
                    ]);
                }
            }
        }

        // Sort by tanggal ascending
        return $entries->sortBy('tanggal')->values();
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, ?string $awal, ?string $akhir, string $column = 'tanggal')
    {
        if ($awal) {
            $query->whereDate($column, '>=', $awal);
        }
        if ($akhir) {
            $query->whereDate($column, '<=', $akhir);
        }
    }
}
