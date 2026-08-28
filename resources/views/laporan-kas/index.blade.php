@extends('layouts.app')

@section('title', 'Laporan Kas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-journal-richtext"></i> Laporan Kas
        </h2>
        <a href="{{ route('laporan-kas.cetak', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
           class="btn btn-dark" target="_blank">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>

    {{-- Filter Bulan & Tahun --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('laporan-kas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm mb-1">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm mb-1">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-funnel"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center" style="background: linear-gradient(135deg, #28a745, #20c997); border-radius: 8px;">
                    <small class="text-white text-uppercase fw-semibold" style="letter-spacing:1px;">Total Debit (Masuk)</small>
                    <h4 class="text-white mb-0 mt-1">Rp {{ number_format($totalDebit, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center" style="background: linear-gradient(135deg, #dc3545, #e74c3c); border-radius: 8px;">
                    <small class="text-white text-uppercase fw-semibold" style="letter-spacing:1px;">Total Kredit (Keluar)</small>
                    <h4 class="text-white mb-0 mt-1">Rp {{ number_format($totalKredit, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center" style="background: linear-gradient(135deg, #0d6efd, #6610f2); border-radius: 8px;">
                    <small class="text-white text-uppercase fw-semibold" style="letter-spacing:1px;">Saldo Akhir</small>
                    <h4 class="text-white mb-0 mt-1">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Laporan --}}
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="bi bi-table"></i>
            Laporan Kas {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle" id="tbl-laporan-kas">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:5%" class="text-center">No</th>
                            <th style="width:12%">Tanggal</th>
                            <th style="width:28%">Keterangan</th>
                            <th style="width:18%" class="text-end">Debit (Masuk)</th>
                            <th style="width:18%" class="text-end">Kredit (Keluar)</th>
                            <th style="width:19%" class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $i => $entry)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $entry['tanggal'] instanceof \Carbon\Carbon ? $entry['tanggal']->format('d/m/Y') : \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $entry['jenis'] === 'BKM' ? 'bg-success' : 'bg-danger' }} me-1">
                                        {{ $entry['jenis'] }}
                                    </span>
                                    {{ $entry['keterangan'] ?: '-' }}
                                    @if($entry['pihak'])
                                        <br><small class="text-muted">{{ $entry['pihak'] }}</small>
                                    @endif
                                </td>
                                <td class="text-end {{ $entry['debit'] > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                                    {{ $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end {{ $entry['kredit'] > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end fw-semibold {{ $entry['saldo'] >= 0 ? 'text-primary' : 'text-danger' }}">
                                    Rp {{ number_format($entry['saldo'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Tidak ada transaksi kas pada bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($entries->count() > 0)
                        <tfoot class="table-dark">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-center">TOTAL</td>
                                <td class="text-end text-success">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                                <td class="text-end {{ $saldoAkhir >= 0 ? 'text-info' : 'text-warning' }}">
                                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
