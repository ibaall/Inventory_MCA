@extends('layouts.app')
@section('title', 'Daftar Jurnal Koreksi')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-journal-check"></i> Daftar Jurnal Koreksi</h2>
        <a href="{{ route('jurnal-koreksi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Jurnal Koreksi
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3"><label class="form-label form-label-sm mb-1">Cari</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="No. Jurnal / Keterangan..."></div>
                <div class="col-md-3"><label class="form-label form-label-sm mb-1">Tanggal Awal</label><input type="date" name="tanggal_awal" class="form-control form-control-sm" value="{{ request('tanggal_awal') }}"></div>
                <div class="col-md-3"><label class="form-label form-label-sm mb-1">Tanggal Akhir</label><input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}"></div>
                <div class="col-md-1"><button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-search"></i></button></div>
                <div class="col-md-2"><a href="{{ route('jurnal-koreksi.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x-circle"></i> Reset</a></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold"><i class="bi bi-journal-check"></i> Daftar Jurnal Koreksi</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:15%">No. Jurnal</th>
                            <th style="width:12%">Tanggal</th>
                            <th style="width:28%">Keterangan</th>
                            <th style="width:14%" class="text-end">Total Debit</th>
                            <th style="width:14%" class="text-end">Total Kredit</th>
                            <th style="width:12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $jk)
                            <tr>
                                <td>{{ $data->firstItem() + $i }}</td>
                                <td><span class="badge bg-secondary">{{ $jk->no_jurnal ?: '-' }}</span></td>
                                <td>{{ $jk->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $jk->keterangan ?: '-' }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($jk->total_debit, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($jk->total_kredit, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('jurnal-koreksi.show', $jk->id) }}" class="btn btn-info btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('jurnal-koreksi.edit', $jk->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('jurnal-koreksi.destroy', $jk->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin hapus jurnal ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data Jurnal Koreksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($data->hasPages())<div class="card-footer">{{ $data->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
