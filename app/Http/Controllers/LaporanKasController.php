<?php

namespace App\Http\Controllers;

use App\Models\BuktiKas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanKasController extends Controller
{
    /**
     * Laporan Kas
     * Kolom: No, Tanggal, Keterangan (No Bukti), Debit (BKM masuk), Kredit (BKK keluar), Saldo
     */
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $data = $this->getData($bulan, $tahun);

        return view('laporan-kas.index', [
            'entries'     => $data['entries'],
            'totalDebit'  => $data['totalDebit'],
            'totalKredit' => $data['totalKredit'],
            'saldoAkhir'  => $data['saldoAkhir'],
            'bulan'       => $bulan,
            'tahun'       => $tahun,
        ]);
    }

    /**
     * Cetak Laporan Kas
     */
    public function cetak(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $data = $this->getData($bulan, $tahun);

        return view('laporan-kas.print', [
            'entries'     => $data['entries'],
            'totalDebit'  => $data['totalDebit'],
            'totalKredit' => $data['totalKredit'],
            'saldoAkhir'  => $data['saldoAkhir'],
            'bulan'       => $bulan,
            'tahun'       => $tahun,
        ]);
    }

    /**
     * Ambil data laporan kas bulanan
     */
    private function getData(int $bulan, int $tahun): array
    {
        // BKM (Bukti Kas Masuk) → kolom DEBIT
        $bkm = BuktiKas::where('jenis', 'BKM')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal'    => $item->tanggal,
                    'keterangan' => $item->no_bukti,
                    'pihak'      => $item->pihak,
                    'debit'      => $item->total,
                    'kredit'     => 0,
                    'jenis'      => 'BKM',
                ];
            });

        // BKK (Bukti Kas Keluar) → kolom KREDIT
        $bkk = BuktiKas::where('jenis', 'BKK')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal'    => $item->tanggal,
                    'keterangan' => $item->no_bukti,
                    'pihak'      => $item->pihak,
                    'debit'      => 0,
                    'kredit'     => $item->total,
                    'jenis'      => 'BKK',
                ];
            });

        // Gabung & urutkan berdasarkan tanggal
        $entries = $bkm->merge($bkk)->sortBy('tanggal')->values();

        // Hitung saldo berjalan
        $saldo = 0;
        $entries = $entries->map(function ($entry) use (&$saldo) {
            $saldo += $entry['debit'] - $entry['kredit'];
            $entry['saldo'] = $saldo;
            return $entry;
        });

        $totalDebit  = $entries->sum('debit');
        $totalKredit = $entries->sum('kredit');
        $saldoAkhir  = $totalDebit - $totalKredit;

        return [
            'entries'     => $entries,
            'totalDebit'  => $totalDebit,
            'totalKredit' => $totalKredit,
            'saldoAkhir'  => $saldoAkhir,
        ];
    }
}
