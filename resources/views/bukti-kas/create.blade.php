@extends('layouts.app')

@section('title', $jenis === 'BKK' ? 'Buat Bukti Kas Keluar' : 'Buat Bukti Kas Masuk')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi {{ $jenis === 'BKK' ? 'bi-cash-stack' : 'bi-cash-coin' }}"></i>
            {{ $jenis === 'BKK' ? 'Buat Bukti Kas Keluar' : 'Buat Bukti Kas Masuk' }}
        </h2>
        <a href="{{ route($jenis === 'BKK' ? 'bukti-kas.bkk.index' : 'bukti-kas.bkm.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('bukti-kas.store') }}" method="POST" id="buktiKasForm">
        @csrf
        <input type="hidden" name="jenis" value="{{ $jenis }}">

        <div class="card mb-3">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="bi bi-info-circle"></i> Informasi {{ $jenis === 'BKK' ? 'Bukti Kas Keluar' : 'Bukti Kas Masuk' }}
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">No. Bukti</label>
                        <input type="text" name="no_bukti" class="form-control" value="{{ old('no_bukti', $noBukti) }}" placeholder="Auto: {{ $noBukti }}">
                        <small class="text-muted">Otomatis terisi, boleh diedit manual.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $jenis === 'BKK' ? 'Dibayarkan Kepada' : 'Diterima Dari' }} <span class="text-danger">*</span></label>
                        <input type="text" name="pihak" class="form-control" value="{{ old('pihak') }}" required
                               placeholder="{{ $jenis === 'BKK' ? 'Contoh: RATNA / TOKO ATK / SUPPLIER' : 'Contoh: KAS BESAR MCA - RATNA' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keterangan Utama</label>
                        <input type="text" name="keterangan_utama" class="form-control" value="{{ old('keterangan_utama') }}"
                               placeholder="{{ $jenis === 'BKK' ? 'Contoh: PEMBELIAN ALAT TULIS KANTOR' : 'Contoh: PENCAIRAN KAS HARIAN' }}">
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
                    <table class="table table-bordered mb-0" id="itemsTable">
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
                            <tr class="item-row">
                                <td class="row-number text-center align-middle fw-semibold">1</td>
                                <td>
                                    <select name="items[0][no_perkiraan_id]" class="form-select form-select-sm select2-perkiraan" onchange="fillNamaPerkiraan(this)">
                                        <option value="">-- Pilih --</option>
                                        @foreach($noPerkiraans as $p)<option value="{{ $p->id }}" data-nama="{{ $p->nama_perkiraan }}">{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>@endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="items[0][nama_perkiraan]" class="form-control form-control-sm nama-perkiraan-input" readonly placeholder="Otomatis"></td>
                                <td><input type="text" name="items[0][keterangan]" class="form-control form-control-sm" placeholder="{{ $jenis === 'BKK' ? 'Ongkos kirim' : 'Pencairan kas harian' }}" required></td>
                                <td><input type="text" name="items[0][jumlah]" class="form-control form-control-sm text-end jumlah-input" placeholder="0" required></td>
                                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Hapus baris"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold">Total Keseluruhan</label><div class="form-control bg-light fw-bold fs-5 text-end" id="grandTotal">Rp 0</div></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Terbilang</label><div class="form-control bg-light fst-italic" id="terbilangDisplay" style="min-height:38px; font-size:0.9rem;">-</div></div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route($jenis === 'BKK' ? 'bukti-kas.bkk.index' : 'bukti-kas.bkm.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Simpan {{ $jenis }}</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@include('bukti-kas._scripts', ['rowCount' => 1])
@endsection
