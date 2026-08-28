@extends('layouts.app')
@section('title', $jenis === 'BBM' ? 'Edit Bukti Bank Masuk' : 'Edit Bukti Bank Keluar')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi {{ $jenis === 'BBM' ? 'bi-bank' : 'bi-bank2' }}"></i> {{ $jenis === 'BBM' ? 'Edit Bukti Bank Masuk' : 'Edit Bukti Bank Keluar' }}</h2>
        <a href="{{ route($jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    @if($errors->any())<div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <form action="{{ route('bukti-bank.update', $bukti->id) }}" method="POST" id="buktiBankForm">
        @csrf @method('PUT')

        <div class="card mb-3">
            <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-info-circle"></i> Informasi {{ $jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar' }}</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">No. Bukti</label>
                        <input type="text" name="no_bukti" class="form-control" value="{{ old('no_bukti', $bukti->no_bukti) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $bukti->tanggal->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $jenis === 'BBM' ? 'Diterima Dari' : 'Dibayarkan Kepada' }} <span class="text-danger">*</span></label>
                        <input type="text" name="pihak" class="form-control" value="{{ old('pihak', $bukti->pihak) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $jenis === 'BBM' ? 'Bank Tujuan' : 'Bank Sumber' }}</label>
                        <select name="bank_account_id" class="form-select select2-perkiraan">
                            <option value="">-- Pilih Bank --</option>
                            @foreach($noPerkiraans as $p)
                                <option value="{{ $p->id }}" {{ old('bank_account_id', $bukti->bank_account_id) == $p->id ? 'selected' : '' }}>{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($jenis === 'BBM')
                    <div class="col-md-4"><label class="form-label">No. Invoice</label><input type="text" name="no_invoice" class="form-control" value="{{ old('no_invoice', $bukti->no_invoice) }}"></div>
                    @else
                    <div class="col-md-4"><label class="form-label">No. PO</label><input type="text" name="no_po" class="form-control" value="{{ old('no_po', $bukti->no_po) }}"></div>
                    <div class="col-md-4"><label class="form-label">BG / Cheque No.</label><input type="text" name="bg_cheque_no" class="form-control" value="{{ old('bg_cheque_no', $bukti->bg_cheque_no) }}"></div>
                    @endif
                    <div class="col-md-{{ $jenis === 'BBM' ? '4' : '12' }}">
                        <label class="form-label">Keterangan Utama</label>
                        <input type="text" name="keterangan_utama" class="form-control" value="{{ old('keterangan_utama', $bukti->keterangan_utama) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol"></i> Detail Transaksi</span>
                <button type="button" class="btn btn-success btn-sm" id="addRow"><i class="bi bi-plus-circle"></i> Tambah Baris</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:4%" class="text-center">No</th>
                                <th style="width:20%">No. Account</th>
                                <th style="width:18%">Nama Perkiraan</th>
                                <th style="width:28%">Keterangan <span class="text-danger">*</span></th>
                                <th style="width:20%">Jumlah (Rp) <span class="text-danger">*</span></th>
                                <th style="width:10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($bukti->details as $i => $d)
                            <tr class="item-row">
                                <td class="row-number text-center align-middle fw-semibold">{{ $i+1 }}</td>
                                <td>
                                    <select name="items[{{ $i }}][no_perkiraan_id]" class="form-select form-select-sm select2-perkiraan" onchange="fillNamaPerkiraan(this)">
                                        <option value="">-- Pilih --</option>
                                        @foreach($noPerkiraans as $p)<option value="{{ $p->id }}" data-nama="{{ $p->nama_perkiraan }}" {{ $d->no_perkiraan_id == $p->id ? 'selected' : '' }}>{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>@endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="items[{{ $i }}][nama_perkiraan]" class="form-control form-control-sm nama-perkiraan-input" readonly value="{{ $d->nama_perkiraan }}"></td>
                                <td><input type="text" name="items[{{ $i }}][keterangan]" class="form-control form-control-sm" value="{{ $d->keterangan }}" required></td>
                                <td><input type="text" name="items[{{ $i }}][jumlah]" class="form-control form-control-sm text-end jumlah-input" value="{{ number_format($d->jumlah,0,',','.') }}" required></td>
                                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold">Total Keseluruhan</label><div class="form-control bg-light fw-bold fs-5 text-end" id="grandTotal">Rp {{ number_format($bukti->total,0,',','.') }}</div></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Terbilang</label><div class="form-control bg-light fst-italic" id="terbilangDisplay" style="min-height:38px;font-size:.9rem">{{ $bukti->terbilang ?: '-' }}</div></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route($jenis === 'BBM' ? 'bukti-bank.masuk.index' : 'bukti-bank.keluar.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Simpan {{ $jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar' }}</button>
        </div>
    </form>
</div>
@endsection
@section('scripts')
@include('bukti-bank._scripts', ['rowCount' => count($bukti->details)])
@endsection
