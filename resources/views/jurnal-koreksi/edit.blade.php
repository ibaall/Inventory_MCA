@extends('layouts.app')
@section('title', 'Edit Jurnal Koreksi')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-journal-check"></i> Edit Jurnal Koreksi</h2>
        <a href="{{ route('jurnal-koreksi.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('jurnal-koreksi.update', $jurnal->id) }}" method="POST" id="jurnalForm">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-info-circle"></i> Informasi Jurnal Koreksi</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">No. Jurnal</label>
                        <input type="text" name="no_jurnal" class="form-control" value="{{ old('no_jurnal', $jurnal->no_jurnal) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $jurnal->tanggal->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $jurnal->keterangan) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol"></i> Detail Jurnal</span>
                <button type="button" class="btn btn-success btn-sm" id="addRow"><i class="bi bi-plus-circle"></i> Tambah Baris</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="itemsTable">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:4%" class="text-center">No</th>
                                <th style="width:20%">No. Perkiraan</th>
                                <th style="width:16%">Nama Perkiraan</th>
                                <th style="width:22%">Keterangan</th>
                                <th style="width:14%">Debit (Rp)</th>
                                <th style="width:14%">Kredit (Rp)</th>
                                <th style="width:10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($jurnal->details as $i => $detail)
                            <tr class="item-row">
                                <td class="row-number text-center align-middle fw-semibold">{{ $i + 1 }}</td>
                                <td>
                                    <select name="items[{{ $i }}][no_perkiraan_id]" class="form-select form-select-sm select2-perkiraan" onchange="fillNamaPerkiraan(this)">
                                        <option value="">-- Pilih --</option>
                                        @foreach($noPerkiraans as $p)<option value="{{ $p->id }}" data-nama="{{ $p->nama_perkiraan }}" {{ $detail->no_perkiraan_id == $p->id ? 'selected' : '' }}>{{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}</option>@endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="items[{{ $i }}][nama_perkiraan]" class="form-control form-control-sm nama-perkiraan-input" readonly value="{{ $detail->nama_perkiraan }}"></td>
                                <td><input type="text" name="items[{{ $i }}][keterangan]" class="form-control form-control-sm" value="{{ $detail->keterangan }}"></td>
                                <td><input type="text" name="items[{{ $i }}][debit]" class="form-control form-control-sm text-end debit-input" value="{{ $detail->debit > 0 ? number_format($detail->debit, 0, ',', '.') : '' }}"></td>
                                <td><input type="text" name="items[{{ $i }}][kredit]" class="form-control form-control-sm text-end kredit-input" value="{{ $detail->kredit > 0 ? number_format($detail->kredit, 0, ',', '.') : '' }}"></td>
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
                    <div class="col-md-6"><label class="form-label fw-bold">Total Debit</label><div class="form-control bg-light fw-bold fs-5 text-end" id="totalDebit">Rp 0</div></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Total Kredit</label><div class="form-control bg-light fw-bold fs-5 text-end" id="totalKredit">Rp 0</div></div>
                </div>
                <div id="balanceAlert" class="alert alert-danger mt-2 d-none"><i class="bi bi-exclamation-triangle"></i> Total Debit dan Kredit harus sama (balance).</div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('jurnal-koreksi.index') }}" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Perbarui Jurnal</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
@include('jurnal-koreksi._scripts', ['rowCount' => $jurnal->details->count()])
@endsection
