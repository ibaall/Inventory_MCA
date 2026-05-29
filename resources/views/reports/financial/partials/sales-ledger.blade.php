{{-- Laporan Penjualan / Piutang Customer --}}
@php
    $data = $reportData;
    $entries = $data['entries'] ?? collect();
    $saldoAwal = $data['saldo_awal'] ?? 0;
    $totalPenjualan = $data['total_penjualan'] ?? 0;
    $totalPembayaran = $data['total_pembayaran'] ?? 0;
    $saldoAkhir = $data['saldo_akhir'] ?? 0;
@endphp

@if(!($filters['customerName'] ?? null))
    <div class="alert alert-info m-3">
        <i class="bi bi-info-circle"></i>
        Silakan pilih <strong>Customer / PT</strong> terlebih dahulu untuk melihat laporan piutang.
    </div>
@elseif($entries->isEmpty())
    <div class="alert alert-warning m-3">
        <i class="bi bi-exclamation-triangle"></i>
        Tidak ada transaksi untuk <strong>{{ $filters['customerName'] }}</strong> pada periode ini.
        <br><small class="text-muted">Saldo Awal: Rp {{ number_format($saldoAwal, 0, ',', '.') }}</small>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width:40px;">No</th>
                    <th style="width:120px;">Tanggal</th>
                    <th style="width:180px;">No. Invoice</th>
                    <th>Keterangan</th>
                    <th class="text-end" style="width:140px;">Saldo Awal</th>
                    <th class="text-end" style="width:140px;">Penjualan</th>
                    <th class="text-end" style="width:220px;">Pembayaran</th>
                    <th class="text-end" style="width:140px;">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                {{-- Baris Saldo Awal --}}
                <tr class="table-light">
                    <td class="text-center">-</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="fw-semibold fst-italic">Saldo Awal Periode</td>
                    <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    <td class="text-end">-</td>
                    <td class="text-end">-</td>
                    <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>

                @foreach($entries as $entry)
                {{-- Baris utama (Invoice atau standalone payment) --}}
                @php
                    $subPayments = $entry['payments'] ?? [];
                    $subPayCount = count($subPayments);
                    $rowspan = ($entry['type'] === 'sale' && $subPayCount > 0) ? 1 + $subPayCount : 1;
                @endphp

                <tr class="{{ $entry['type'] === 'payment_standalone' ? 'table-success' : '' }}">
                    <td class="text-center" @if($rowspan > 1) rowspan="{{ $rowspan }}" style="vertical-align: middle;" @endif>{{ $loop->iteration }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" style="vertical-align: middle;" @endif>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" style="vertical-align: middle;" @endif>
                        @if($entry['type'] === 'sale')
                            <span class="badge bg-secondary">{{ $entry['nomor'] }}</span>
                        @elseif($entry['type'] === 'payment_standalone')
                            <span class="badge bg-info text-dark">{{ $entry['nomor'] }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" style="vertical-align: middle;" @endif>{{ $entry['keterangan'] }}</td>
                    <td class="text-end" @if($rowspan > 1) rowspan="{{ $rowspan }}" style="vertical-align: middle;" @endif>Rp {{ number_format($entry['saldo_awal_baris'], 0, ',', '.') }}</td>

                    {{-- Kolom Penjualan --}}
                    <td class="text-end {{ ($entry['penjualan'] ?? 0) > 0 ? 'text-primary fw-semibold' : '' }}">
                        {{ ($entry['penjualan'] ?? 0) > 0 ? 'Rp ' . number_format($entry['penjualan'], 0, ',', '.') : '-' }}
                    </td>

                    {{-- Kolom Pembayaran --}}
                    <td class="text-end">
                        @if($entry['type'] === 'sale')
                            @if(($entry['remaining'] ?? 0) <= 0)
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <form action="{{ route('payments.store') }}" method="POST" class="d-flex align-items-center justify-content-end gap-1 m-0">
                                    @csrf
                                    <input type="hidden" name="transaction_type" value="sale">
                                    <input type="hidden" name="transaction_id" value="{{ $entry['id'] }}">
                                    <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                    <div class="input-group input-group-sm" style="width: 140px;">
                                        <input type="number" name="amount" class="form-control text-end border-success" 
                                               placeholder="{{ number_format($entry['remaining'], 0, '', '') }}" 
                                               max="{{ $entry['remaining'] }}" min="1" required style="font-size: 11px; padding: 2px 5px;">
                                        <button class="btn btn-success py-0 px-2" type="submit" title="Bayar Invoice Ini" style="font-size: 11px;">
                                            Bayar
                                        </button>
                                    </div>
                                </form>
                                <div class="text-muted text-end mt-1" style="font-size: 9px;">
                                    Sisa: Rp {{ number_format($entry['remaining'], 0, ',', '.') }}
                                </div>
                            @endif
                        @elseif($entry['type'] === 'payment_standalone')
                            <span class="text-success fw-semibold">Rp {{ number_format($entry['pembayaran'], 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>

                    {{-- Saldo Akhir --}}
                    <td class="text-end fw-bold" @if($rowspan > 1) rowspan="{{ $rowspan }}" style="vertical-align: middle;" @endif>Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>

                {{-- Sub-baris pembayaran (di bawah baris Invoice, dalam kolom Penjualan & Pembayaran) --}}
                @if($entry['type'] === 'sale' && $subPayCount > 0)
                    @foreach($subPayments as $sp)
                    <tr class="table-success" style="background-color: #e8f5e9 !important;">
                        {{-- Kolom Penjualan: tampilkan tanggal pembayaran --}}
                        <td class="text-end text-muted" style="font-size: 11px;">
                            <i class="bi bi-arrow-return-right"></i> {{ Carbon\Carbon::parse($sp['date'])->format('d/m/Y') }}
                        </td>
                        {{-- Kolom Pembayaran --}}
                        <td class="text-end text-success fw-semibold" style="font-size: 12px;">
                            Rp {{ number_format($sp['amount'], 0, ',', '.') }}
                            @if($sp['note'])
                                <br><small class="text-muted fst-italic">{{ $sp['note'] }}</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endif
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">-</td>
                    <td class="text-end text-primary">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Ringkasan card --}}
    <div class="row m-3">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center py-2">
                    <small class="text-muted">Saldo Awal</small>
                    <h6 class="mb-0 text-primary">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center py-2">
                    <small class="text-muted">Total Penjualan</small>
                    <h6 class="mb-0 text-info">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center py-2">
                    <small class="text-muted">Total Pembayaran</small>
                    <h6 class="mb-0 text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center py-2">
                    <small class="text-muted">Saldo Akhir (Piutang)</small>
                    <h6 class="mb-0 {{ $saldoAkhir > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
    </div>
@endif
