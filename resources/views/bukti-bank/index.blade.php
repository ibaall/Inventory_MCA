@extends('layouts.app')
@section('title', $jenis === 'BBM' ? 'Daftar Bukti Bank Masuk' : 'Daftar Bukti Bank Keluar')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi {{ $jenis === 'BBM' ? 'bi-bank' : 'bi-bank2' }}"></i>
            {{ $jenis === 'BBM' ? 'Daftar Bukti Bank Masuk' : 'Daftar Bukti Bank Keluar' }}
        </h2>
        <a href="{{ route($jenis === 'BBM' ? 'bukti-bank.masuk.create' : 'bukti-bank.keluar.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat {{ $jenis === 'BBM' ? 'Bank Masuk' : 'Bank Keluar' }}
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
                <div class="col-md-3"><label class="form-label form-label-sm mb-1">Cari</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="No. Bukti / Pihak / Invoice / PO..."></div>
                <div class="col-md-3"><label class="form-label form-label-sm mb-1">Tanggal Awal</label><input type="date" name="tanggal_awal" class="form-control form-control-sm" value="{{ request('tanggal_awal') }}"></div>
                <div class="col-md-3"><label class="form-label form-label-sm mb-1">Tanggal Akhir</label><input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}"></div>
                <div class="col-md-1"><button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-search"></i></button></div>
                <div class="col-md-2"><a href="{{ route($jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x-circle"></i> Reset</a></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="bi {{ $jenis === 'BBM' ? 'bi-arrow-down-left-circle' : 'bi-arrow-up-right-circle' }}"></i>
            Daftar {{ $jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar' }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:3%">No</th>
                            <th style="width:10%">No. Bukti</th>
                            <th style="width:9%">Tanggal</th>
                            <th style="width:15%">{{ $jenis === 'BBM' ? 'Diterima Dari' : 'Dibayarkan Kepada' }}</th>
                            <th style="width:11%">Bank</th>
                            <th style="width:10%">{{ $jenis === 'BBM' ? 'No. Invoice' : 'No. PO' }}</th>
                            <th style="width:12%">Keterangan</th>
                            <th style="width:12%" class="text-end">Total</th>
                            <th style="width:18%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $bk)
                            <tr>
                                <td>{{ $data->firstItem() + $i }}</td>
                                <td><span class="badge bg-secondary">{{ $bk->no_bukti ?: '-' }}</span></td>
                                <td>{{ $bk->tanggal->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $bk->pihak }}</td>
                                <td><small>{{ $bk->bankAccount ? $bk->bankAccount->kode_perkiraan . ' - ' . $bk->bankAccount->nama_perkiraan : '-' }}</small></td>
                                <td><small>{{ $jenis === 'BBM' ? ($bk->no_invoice ?: '-') : ($bk->no_po ?: '-') }}</small></td>
                                <td><small>{{ $bk->keterangan_utama ?: '-' }}</small></td>
                                <td class="text-end fw-bold">Rp {{ number_format($bk->total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('bukti-bank.show', $bk->id) }}" class="btn btn-info btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('bukti-bank.edit', $bk->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="{{ route('bukti-bank.cetak', $bk->id) }}" class="btn btn-dark btn-sm" title="Cetak" target="_blank"><i class="bi bi-printer"></i></a>
                                    <form action="{{ route('bukti-bank.destroy', $bk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($data->hasPages())<div class="card-footer">{{ $data->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
