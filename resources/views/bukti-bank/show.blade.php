@extends('layouts.app')
@section('title', ($bukti->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar') . ' - ' . ($bukti->no_bukti ?: 'Detail'))
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi {{ $bukti->jenis === 'BBM' ? 'bi-bank' : 'bi-bank2' }}"></i> Detail {{ $bukti->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar' }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('bukti-bank.cetak', $bukti->id) }}" class="btn btn-dark" target="_blank"><i class="bi bi-printer"></i> Cetak</a>
            <a href="{{ route('bukti-bank.edit', $bukti->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
            <a href="{{ route($bukti->jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-info-circle"></i> Informasi {{ $bukti->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar' }}</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>No. Bukti:</strong><br><span class="badge bg-secondary fs-6">{{ $bukti->no_bukti ?: '-' }}</span></div>
                <div class="col-md-4"><strong>Tanggal:</strong><br>{{ $bukti->tanggal->format('d/m/Y') }}</div>
                <div class="col-md-4"><strong>{{ $bukti->jenis === 'BBM' ? 'Diterima Dari' : 'Dibayarkan Kepada' }}:</strong><br><span class="fw-semibold">{{ $bukti->pihak }}</span></div>
                <div class="col-md-4"><strong>{{ $bukti->jenis === 'BBM' ? 'Bank Tujuan' : 'Bank Sumber' }}:</strong><br>{{ $bukti->bankAccount ? $bukti->bankAccount->kode_perkiraan . ' - ' . $bukti->bankAccount->nama_perkiraan : '-' }}</div>
                @if($bukti->jenis === 'BBM')
                <div class="col-md-4"><strong>No. Invoice:</strong><br>{{ $bukti->no_invoice ?: '-' }}</div>
                @else
                <div class="col-md-4"><strong>No. PO:</strong><br>{{ $bukti->no_po ?: '-' }}</div>
                <div class="col-md-4"><strong>BG / Cheque No.:</strong><br>{{ $bukti->bg_cheque_no ?: '-' }}</div>
                @endif
                <div class="col-md-4"><strong>Keterangan Utama:</strong><br>{{ $bukti->keterangan_utama ?: '-' }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white fw-semibold"><i class="bi bi-list-ol"></i> Detail Transaksi</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:5%" class="text-center">No</th>
                            <th style="width:12%">No. Account</th>
                            <th style="width:18%">Nama Perkiraan</th>
                            <th style="width:35%">Keterangan</th>
                            <th style="width:30%" class="text-end">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bukti->details as $i => $d)
                        <tr>
                            <td class="text-center">{{ $i+1 }}</td>
                            <td>{{ $d->kode_perkiraan ?: '-' }}</td>
                            <td>{{ $d->nama_perkiraan ?: '-' }}</td>
                            <td>{{ $d->keterangan }}</td>
                            <td class="text-end fw-semibold">{{ number_format($d->jumlah,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr class="table-warning"><td colspan="4" class="text-end fw-bold">Total Keseluruhan</td><td class="text-end fw-bold fs-5">Rp {{ number_format($bukti->total,0,',','.') }}</td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body"><strong>Terbilang:</strong> <span class="fst-italic">{{ $bukti->terbilang ?: '-' }}</span></div></div>
    <div class="card mb-4"><div class="card-body py-2"><small class="text-muted"><i class="bi bi-person"></i> Dibuat oleh: {{ $bukti->creator->name ?? '-' }} &nbsp;|&nbsp; <i class="bi bi-clock"></i> {{ $bukti->created_at->format('d/m/Y H:i') }}</small></div></div>
</div>
@endsection
