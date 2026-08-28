<?php

namespace App\Http\Controllers;

use App\Models\BuktiKas;
use App\Models\BuktiKasDetail;
use App\Models\NoPerkiraan;
use Illuminate\Http\Request;

class BuktiKasController extends Controller
{
    /**
     * Daftar BKK (Bukti Kas Keluar)
     */
    public function indexBkk(Request $request)
    {
        return $this->index($request, 'BKK');
    }

    /**
     * Daftar BKM (Bukti Kas Masuk)
     */
    public function indexBkm(Request $request)
    {
        return $this->index($request, 'BKM');
    }

    /**
     * Shared index logic
     */
    private function index(Request $request, string $jenis)
    {
        $query = BuktiKas::where('jenis', $jenis)->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhere('pihak', 'like', "%{$search}%")
                  ->orWhere('keterangan_utama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        $data = $query->paginate(15);

        return view('bukti-kas.index', [
            'data' => $data,
            'jenis' => $jenis,
        ]);
    }

    /**
     * Form buat BKK baru
     */
    public function createBkk()
    {
        return view('bukti-kas.create', [
            'jenis' => 'BKK',
            'noBukti' => BuktiKas::generateNoBukti('BKK'),
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
        ]);
    }

    /**
     * Form buat BKM baru
     */
    public function createBkm()
    {
        return view('bukti-kas.create', [
            'jenis' => 'BKM',
            'noBukti' => BuktiKas::generateNoBukti('BKM'),
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
        ]);
    }

    /**
     * Simpan BKK / BKM baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:BKK,BKM',
            'tanggal' => 'required|date',
            'pihak' => 'required|string|max:255',
            'keterangan_utama' => 'nullable|string|max:255',
            'no_bukti' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.keterangan' => 'required|string|max:500',
            'items.*.jumlah' => 'required|string',
        ], [
            'items.required' => 'Minimal harus ada 1 detail transaksi.',
            'items.min' => 'Minimal harus ada 1 detail transaksi.',
            'items.*.keterangan.required' => 'Keterangan detail wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah transaksi wajib diisi.',
        ]);

        $items = $request->input('items', []);
        $totalKeseluruhan = 0;
        $detailRows = [];
        $urutan = 0;

        foreach ($items as $item) {
            $jumlah = intval(str_replace(['.', ','], '', $item['jumlah'] ?? '0'));
            if ($jumlah <= 0) continue;

            $urutan++;
            $totalKeseluruhan += $jumlah;

            $perkiraan = null;
            if (!empty($item['no_perkiraan_id'])) {
                $perkiraan = NoPerkiraan::find($item['no_perkiraan_id']);
            }

            $detailRows[] = [
                'no_perkiraan_id' => $perkiraan?->id,
                'no_account' => $perkiraan?->kode_perkiraan ?? ($item['no_account'] ?? null),
                'nama_perkiraan' => $perkiraan?->nama_perkiraan ?? ($item['nama_perkiraan'] ?? null),
                'keterangan' => $item['keterangan'],
                'jumlah' => $jumlah,
                'urutan' => $urutan,
            ];
        }

        if (empty($detailRows)) {
            return back()->withInput()->withErrors(['items' => 'Minimal harus ada 1 detail transaksi dengan jumlah lebih dari 0.']);
        }

        $terbilang = self::angkaKeTerbilang($totalKeseluruhan) . ' Rupiah';

        $bukti = BuktiKas::create([
            'jenis' => $request->jenis,
            'no_bukti' => $request->no_bukti,
            'tanggal' => $request->tanggal,
            'pihak' => $request->pihak,
            'keterangan_utama' => $request->keterangan_utama,
            'total' => $totalKeseluruhan,
            'terbilang' => $terbilang,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($detailRows as $row) {
            $bukti->details()->create($row);
        }

        $route = $request->jenis === 'BKK' ? 'bukti-kas.bkk.index' : 'bukti-kas.bkm.index';
        $label = $request->jenis === 'BKK' ? 'Bukti Kas Keluar' : 'Bukti Kas Masuk';
        return redirect()->route($route)->with('success', "{$label} berhasil disimpan.");
    }

    /**
     * Detail BKK / BKM
     */
    public function show($id)
    {
        $bukti = BuktiKas::with('details', 'creator')->findOrFail($id);
        return view('bukti-kas.show', compact('bukti'));
    }

    /**
     * Form edit BKK / BKM
     */
    public function edit($id)
    {
        $bukti = BuktiKas::with('details')->findOrFail($id);

        return view('bukti-kas.edit', [
            'bukti' => $bukti,
            'jenis' => $bukti->jenis,
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
        ]);
    }

    /**
     * Update BKK / BKM
     */
    public function update(Request $request, $id)
    {
        $bukti = BuktiKas::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'pihak' => 'required|string|max:255',
            'keterangan_utama' => 'nullable|string|max:255',
            'no_bukti' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.keterangan' => 'required|string|max:500',
            'items.*.jumlah' => 'required|string',
        ], [
            'items.required' => 'Minimal harus ada 1 detail transaksi.',
            'items.min' => 'Minimal harus ada 1 detail transaksi.',
            'items.*.keterangan.required' => 'Keterangan detail wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah transaksi wajib diisi.',
        ]);

        $items = $request->input('items', []);
        $totalKeseluruhan = 0;
        $detailRows = [];
        $urutan = 0;

        foreach ($items as $item) {
            $jumlah = intval(str_replace(['.', ','], '', $item['jumlah'] ?? '0'));
            if ($jumlah <= 0) continue;

            $urutan++;
            $totalKeseluruhan += $jumlah;

            $perkiraan = null;
            if (!empty($item['no_perkiraan_id'])) {
                $perkiraan = NoPerkiraan::find($item['no_perkiraan_id']);
            }

            $detailRows[] = [
                'no_perkiraan_id' => $perkiraan?->id,
                'no_account' => $perkiraan?->kode_perkiraan ?? ($item['no_account'] ?? null),
                'nama_perkiraan' => $perkiraan?->nama_perkiraan ?? ($item['nama_perkiraan'] ?? null),
                'keterangan' => $item['keterangan'],
                'jumlah' => $jumlah,
                'urutan' => $urutan,
            ];
        }

        if (empty($detailRows)) {
            return back()->withInput()->withErrors(['items' => 'Minimal harus ada 1 detail transaksi dengan jumlah lebih dari 0.']);
        }

        $terbilang = self::angkaKeTerbilang($totalKeseluruhan) . ' Rupiah';

        $bukti->update([
            'no_bukti' => $request->no_bukti,
            'tanggal' => $request->tanggal,
            'pihak' => $request->pihak,
            'keterangan_utama' => $request->keterangan_utama,
            'total' => $totalKeseluruhan,
            'terbilang' => $terbilang,
        ]);

        // Hapus detail lama, buat ulang
        $bukti->details()->delete();
        foreach ($detailRows as $row) {
            $bukti->details()->create($row);
        }

        $route = $bukti->jenis === 'BKK' ? 'bukti-kas.bkk.index' : 'bukti-kas.bkm.index';
        $label = $bukti->jenis === 'BKK' ? 'Bukti Kas Keluar' : 'Bukti Kas Masuk';
        return redirect()->route($route)->with('success', "{$label} berhasil diperbarui.");
    }

    /**
     * Hapus BKK / BKM
     */
    public function destroy($id)
    {
        $bukti = BuktiKas::findOrFail($id);

        $jenis = $bukti->jenis;
        $bukti->delete();

        $route = $jenis === 'BKK' ? 'bukti-kas.bkk.index' : 'bukti-kas.bkm.index';
        return redirect()->route($route)->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Cetak BKK / BKM
     */
    public function print($id)
    {
        $bukti = BuktiKas::with('details', 'creator')->findOrFail($id);
        return view('bukti-kas.print', compact('bukti'));
    }

    /**
     * Konfirmasi transaksi BKK / BKM
     */
    public function konfirmasi($id)
    {
        $bukti = BuktiKas::findOrFail($id);
        $bukti->update(['status' => 'konfirmasi']);

        $route = $bukti->jenis === 'BKK' ? 'bukti-kas.bkk.index' : 'bukti-kas.bkm.index';
        $label = $bukti->jenis === 'BKK' ? 'Bukti Kas Keluar' : 'Bukti Kas Masuk';
        return redirect()->route($route)->with('success', "{$label} berhasil dikonfirmasi.");
    }

    /**
     * Konversi angka ke terbilang Indonesia
     */
    public static function angkaKeTerbilang($angka): string
    {
        $angka = abs((int) $angka);
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            return $satuan[$angka];
        } elseif ($angka < 20) {
            return $satuan[$angka - 10] . ' Belas';
        } elseif ($angka < 100) {
            return $satuan[intval($angka / 10)] . ' Puluh ' . self::angkaKeTerbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'Seratus ' . self::angkaKeTerbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $satuan[intval($angka / 100)] . ' Ratus ' . self::angkaKeTerbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'Seribu ' . self::angkaKeTerbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return self::angkaKeTerbilang(intval($angka / 1000)) . ' Ribu ' . self::angkaKeTerbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return self::angkaKeTerbilang(intval($angka / 1000000)) . ' Juta ' . self::angkaKeTerbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return self::angkaKeTerbilang(intval($angka / 1000000000)) . ' Miliar ' . self::angkaKeTerbilang($angka % 1000000000);
        } elseif ($angka < 1000000000000000) {
            return self::angkaKeTerbilang(intval($angka / 1000000000000)) . ' Triliun ' . self::angkaKeTerbilang($angka % 1000000000000);
        }

        return '';
    }
}
