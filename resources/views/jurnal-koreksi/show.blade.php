@extends('layouts.app')
@section('title', 'Detail Jurnal Koreksi')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-journal-check"></i> Detail Jurnal Koreksi</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('jurnal-koreksi.edit', $jurnal->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
            <a href="{{ route('jurnal-koreksi.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-info-circle"></i> Informasi Jurnal Koreksi</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>No. Jurnal:</strong><br><span class="badge bg-secondary fs-6">{{ $jurnal->no_jurnal ?: '-' }}</span></div>
                <div class="col-md-4"><strong>Tanggal:</strong><br>{{ $jurnal->tanggal->format('d/m/Y') }}</div>
                <div class="col-md-12"><strong>Keterangan:</strong><br>{{ $jurnal->keterangan ?: '-' }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white fw-semibold"><i class="bi bi-list-ol"></i> Detail Jurnal</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:5%" class="text-center">No</th>
                            <th style="width:12%">No. Perkiraan</th>
                            <th style="width:18%">Nama Perkiraan</th>
                            <th style="width:25%">Keterangan</th>
                            <th style="width:20%" class="text-end">Debit (Rp)</th>
                            <th style="width:20%" class="text-end">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal->details as $i => $detail)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $detail->kode_perkiraan ?: '-' }}</td>
                            <td>{{ $detail->nama_perkiraan ?: '-' }}</td>
                            <td>{{ $detail->keterangan ?: '-' }}</td>
                            <td class="text-end fw-semibold">{{ $detail->debit > 0 ? number_format($detail->debit, 0, ',', '.') : '-' }}</td>
                            <td class="text-end fw-semibold">{{ $detail->kredit > 0 ? number_format($detail->kredit, 0, ',', '.') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-warning">
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold fs-5">Rp {{ number_format($jurnal->total_debit, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold fs-5">Rp {{ number_format($jurnal->total_kredit, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4"><div class="card-body py-2"><small class="text-muted"><i class="bi bi-person"></i> Dibuat oleh: {{ $jurnal->creator->name ?? '-' }} &nbsp;|&nbsp; <i class="bi bi-clock"></i> {{ $jurnal->created_at->format('d/m/Y H:i') }}</small></div></div>
</div>
@endsection
