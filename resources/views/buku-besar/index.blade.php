@extends('layouts.app')
@section('title', 'Buku Besar')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-book"></i> Buku Besar</h2>
        @if($selectedPerkiraan && $entries->count() > 0)
            <a href="{{ route('buku-besar.cetak', request()->all()) }}" class="btn btn-dark" target="_blank">
                <i class="bi bi-printer"></i> Cetak PDF
            </a>
        @endif
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-funnel"></i> Filter Buku Besar</div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Nomor Perkiraan <span class="text-danger">*</span></label>
                    <select name="no_perkiraan_id" class="form-select select2-perkiraan" required>
                        <option value="">-- Pilih Nomor Perkiraan --</option>
                        @foreach($noPerkiraans as $p)
                            <option value="{{ $p->id }}" {{ ($filters['no_perkiraan_id'] ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->kode_perkiraan }} - {{ $p->nama_perkiraan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ ($filters['bulan'] ?? now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ ($filters['tahun'] ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jenis Transaksi</label>
                    <select name="jenis_transaksi" class="form-select">
                        <option value="">Semua</option>
                        <option value="kas" {{ ($filters['jenis_transaksi'] ?? '') === 'kas' ? 'selected' : '' }}>Kas</option>
                        <option value="bank" {{ ($filters['jenis_transaksi'] ?? '') === 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="jurnal_koreksi" {{ ($filters['jenis_transaksi'] ?? '') === 'jurnal_koreksi' ? 'selected' : '' }}>Jurnal Koreksi (JK)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kata Kunci</label>
                    <input type="text" name="keyword" class="form-control" value="{{ $filters['keyword'] ?? '' }}" placeholder="Cari keterangan...">
                </div>
                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
                    <a href="{{ route('buku-besar.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Result --}}
    @if($selectedPerkiraan)
        <div class="card mb-3">
            <div class="card-header bg-dark text-white fw-semibold">
                <i class="bi bi-book"></i>
                Buku Besar: {{ $selectedPerkiraan->kode_perkiraan }} - {{ $selectedPerkiraan->nama_perkiraan }}
                <small class="ms-2">(Periode: {{ \Carbon\Carbon::create()->month($filters['bulan'])->translatedFormat('F') }} {{ $filters['tahun'] }})</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:5%" class="text-center">No</th>
                                <th style="width:12%">Tanggal</th>
                                <th style="width:10%">Sumber</th>
                                <th style="width:12%">No. Bukti</th>
                                <th style="width:31%">Keterangan</th>
                                <th style="width:15%" class="text-end">Debit (Rp)</th>
                                <th style="width:15%" class="text-end">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $i => $entry)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $sumberColors = [
                                                'BKK' => 'bg-danger',
                                                'BKM' => 'bg-primary',
                                                'BBK' => 'bg-warning text-dark',
                                                'BBM' => 'bg-info text-dark',
                                                'JK' => 'bg-secondary',
                                                'PO' => 'bg-dark',
                                                'INV' => 'bg-success',
                                            ];
                                        @endphp
                                        <span class="badge {{ $sumberColors[$entry['sumber']] ?? 'bg-secondary' }}">{{ $entry['sumber'] }}</span>
                                    </td>
                                    <td><small>{{ $entry['no_bukti'] }}</small></td>
                                    <td>{{ $entry['keterangan'] }}</td>
                                    <td class="text-end fw-semibold">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                                    <td class="text-end fw-semibold">{{ $entry['kredit'] > 0 ? number_format($entry['kredit'], 0, ',', '.') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada transaksi untuk Nomor Perkiraan ini.</td></tr>
                            @endforelse
                        </tbody>
                        @if($entries->count() > 0)
                        <tfoot>
                            <tr class="table-warning">
                                <td colspan="5" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold fs-5">Rp {{ number_format($entries->sum('debit'), 0, ',', '.') }}</td>
                                <td class="text-end fw-bold fs-5">Rp {{ number_format($entries->sum('kredit'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-book fs-1 d-block mb-3"></i>
                <h5>Pilih Nomor Perkiraan untuk menampilkan Buku Besar</h5>
                <p class="mb-0">Buku Besar akan mengambil data otomatis dari semua transaksi berdasarkan Nomor Perkiraan yang dipilih.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-perkiraan').select2({ theme: 'bootstrap-5', placeholder: '-- Pilih Nomor Perkiraan --', allowClear: true, width: '100%' });
    }
</script>
@endsection
