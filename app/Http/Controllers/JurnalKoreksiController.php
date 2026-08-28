<?php

namespace App\Http\Controllers;

use App\Models\JurnalKoreksi;
use App\Models\JurnalKoreksiDetail;
use App\Models\NoPerkiraan;
use Illuminate\Http\Request;

class JurnalKoreksiController extends Controller
{
    /**
     * Daftar Jurnal Koreksi
     */
    public function index(Request $request)
    {
        $query = JurnalKoreksi::with('creator')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_jurnal', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        $data = $query->paginate(15);
        return view('jurnal-koreksi.index', compact('data'));
    }

    /**
     * Form buat Jurnal Koreksi baru
     */
    public function create()
    {
        return view('jurnal-koreksi.create', [
            'noJurnal' => JurnalKoreksi::generateNoJurnal(),
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
        ]);
    }

    /**
     * Simpan Jurnal Koreksi
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.keterangan' => 'nullable|string|max:500',
        ], [
            'items.required' => 'Minimal harus ada 1 detail jurnal.',
            'items.min' => 'Minimal harus ada 1 detail jurnal.',
        ]);

        $items = $request->input('items', []);
        $detailRows = [];
        $urutan = 0;

        foreach ($items as $item) {
            $debit = intval(str_replace(['.', ','], '', $item['debit'] ?? '0'));
            $kredit = intval(str_replace(['.', ','], '', $item['kredit'] ?? '0'));
            if ($debit <= 0 && $kredit <= 0) continue;

            $urutan++;
            $perkiraan = null;
            if (!empty($item['no_perkiraan_id'])) {
                $perkiraan = NoPerkiraan::find($item['no_perkiraan_id']);
            }

            $detailRows[] = [
                'no_perkiraan_id' => $perkiraan?->id,
                'kode_perkiraan' => $perkiraan?->kode_perkiraan ?? null,
                'nama_perkiraan' => $perkiraan?->nama_perkiraan ?? null,
                'keterangan' => $item['keterangan'] ?? null,
                'debit' => $debit,
                'kredit' => $kredit,
                'urutan' => $urutan,
            ];
        }

        if (empty($detailRows)) {
            return back()->withInput()->withErrors(['items' => 'Minimal harus ada 1 detail jurnal dengan Debit atau Kredit lebih dari 0.']);
        }

        $jurnal = JurnalKoreksi::create([
            'no_jurnal' => $request->no_jurnal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($detailRows as $row) {
            $jurnal->details()->create($row);
        }

        return redirect()->route('jurnal-koreksi.index')->with('success', 'Jurnal Koreksi berhasil disimpan.');
    }

    /**
     * Detail Jurnal Koreksi
     */
    public function show($id)
    {
        $jurnal = JurnalKoreksi::with('details', 'creator')->findOrFail($id);
        return view('jurnal-koreksi.show', compact('jurnal'));
    }

    /**
     * Form edit Jurnal Koreksi
     */
    public function edit($id)
    {
        $jurnal = JurnalKoreksi::with('details')->findOrFail($id);

        return view('jurnal-koreksi.edit', [
            'jurnal' => $jurnal,
            'noPerkiraans' => NoPerkiraan::orderBy('kode_perkiraan')->get(),
        ]);
    }

    /**
     * Update Jurnal Koreksi
     */
    public function update(Request $request, $id)
    {
        $jurnal = JurnalKoreksi::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
        ]);

        $items = $request->input('items', []);
        $detailRows = [];
        $urutan = 0;

        foreach ($items as $item) {
            $debit = intval(str_replace(['.', ','], '', $item['debit'] ?? '0'));
            $kredit = intval(str_replace(['.', ','], '', $item['kredit'] ?? '0'));
            if ($debit <= 0 && $kredit <= 0) continue;

            $urutan++;
            $perkiraan = null;
            if (!empty($item['no_perkiraan_id'])) {
                $perkiraan = NoPerkiraan::find($item['no_perkiraan_id']);
            }

            $detailRows[] = [
                'no_perkiraan_id' => $perkiraan?->id,
                'kode_perkiraan' => $perkiraan?->kode_perkiraan ?? null,
                'nama_perkiraan' => $perkiraan?->nama_perkiraan ?? null,
                'keterangan' => $item['keterangan'] ?? null,
                'debit' => $debit,
                'kredit' => $kredit,
                'urutan' => $urutan,
            ];
        }

        if (empty($detailRows)) {
            return back()->withInput()->withErrors(['items' => 'Minimal harus ada 1 detail jurnal.']);
        }

        $jurnal->update([
            'no_jurnal' => $request->no_jurnal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        $jurnal->details()->delete();
        foreach ($detailRows as $row) {
            $jurnal->details()->create($row);
        }

        return redirect()->route('jurnal-koreksi.index')->with('success', 'Jurnal Koreksi berhasil diperbarui.');
    }

    /**
     * Hapus Jurnal Koreksi
     */
    public function destroy($id)
    {
        $jurnal = JurnalKoreksi::findOrFail($id);

        $jurnal->delete();
        return redirect()->route('jurnal-koreksi.index')->with('success', 'Jurnal Koreksi berhasil dihapus.');
    }

    /**
     * Konfirmasi Jurnal Koreksi
     */
    public function konfirmasi($id)
    {
        $jurnal = JurnalKoreksi::findOrFail($id);
        $jurnal->update(['status' => 'konfirmasi']);
        return back()->with('success', 'Jurnal Koreksi berhasil dikonfirmasi.');
    }
}
