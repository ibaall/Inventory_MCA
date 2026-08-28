<?php

namespace App\Http\Controllers;

use App\Models\NoPerkiraan;
use Illuminate\Http\Request;

class NoPerkiraanController extends Controller
{
    public function index(Request $request)
    {
        $query = NoPerkiraan::orderBy('kode_perkiraan');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_perkiraan', 'like', "%{$s}%")
                  ->orWhere('nama_perkiraan', 'like', "%{$s}%");
            });
        }
        $data = $query->paginate(50);
        return view('no-perkiraan.index', compact('data'));
    }

    public function create()
    {
        return view('no-perkiraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_perkiraan' => 'required|string|max:10|unique:no_perkiraans,kode_perkiraan',
            'nama_perkiraan' => 'required|string|max:255',
        ]);
        NoPerkiraan::create($request->only('kode_perkiraan', 'nama_perkiraan'));
        return redirect()->route('no-perkiraan.index')->with('success', 'No. Perkiraan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $perkiraan = NoPerkiraan::findOrFail($id);
        return view('no-perkiraan.edit', compact('perkiraan'));
    }

    public function update(Request $request, $id)
    {
        $perkiraan = NoPerkiraan::findOrFail($id);
        $request->validate([
            'kode_perkiraan' => 'required|string|max:10|unique:no_perkiraans,kode_perkiraan,' . $id,
            'nama_perkiraan' => 'required|string|max:255',
        ]);
        $perkiraan->update($request->only('kode_perkiraan', 'nama_perkiraan'));
        return redirect()->route('no-perkiraan.index')->with('success', 'No. Perkiraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        NoPerkiraan::findOrFail($id)->delete();
        return redirect()->route('no-perkiraan.index')->with('success', 'No. Perkiraan berhasil dihapus.');
    }

    /** JSON API for searchable dropdowns */
    public function apiList(Request $request)
    {
        $query = NoPerkiraan::orderBy('kode_perkiraan');
        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q) use ($s) {
                $q->where('kode_perkiraan', 'like', "%{$s}%")
                  ->orWhere('nama_perkiraan', 'like', "%{$s}%");
            });
        }
        return response()->json($query->get()->map(fn($p) => [
            'id' => $p->id,
            'kode' => $p->kode_perkiraan,
            'nama' => $p->nama_perkiraan,
            'label' => $p->kode_perkiraan . ' - ' . $p->nama_perkiraan,
        ]));
    }
}
