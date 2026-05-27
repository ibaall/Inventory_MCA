{{-- Laporan Pembelian / Hutang Supplier --}}
@php
    $data = $reportData;
    $entries = $data['entries'] ?? collect();
    $saldoAwal = $data['saldo_awal'] ?? 0;
    $totalPembelian = $data['total_pembelian'] ?? 0;
    $totalPembayaran = $data['total_pembayaran'] ?? 0;
    $saldoAkhir = $data['saldo_akhir'] ?? 0;
@endphp

@if(!($filters['supplierName'] ?? null))
    <div class="alert alert-info m-3">
        <i class="bi bi-info-circle"></i>
        Silakan pilih <strong>Supplier</strong> terlebih dahulu untuk melihat laporan hutang.
    </div>
@elseif($entries->isEmpty())
    <div class="alert alert-warning m-3">
        <i class="bi bi-exclamation-triangle"></i>
        Tidak ada transaksi untuk <strong>{{ $filters['supplierName'] }}</strong> pada periode ini.
        <br><small class="text-muted">Saldo Awal: Rp {{ number_format($saldoAwal, 0, ',', '.') }}</small>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width:40px;">No</th>
                    <th style="width:120px;">Tanggal</th>
                    <th style="width:180px;">No. PO / Invoice</th>
                    <th>Keterangan</th>
                    <th class="text-end" style="width:140px;">Saldo Awal</th>
                    <th class="text-end" style="width:140px;">Pembelian</th>
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
                <tr class="{{ $entry['type'] == 'payment' ? 'table-success' : '' }}">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td>
                        @if($entry['type'] == 'purchase')
                            <span class="badge bg-secondary">{{ $entry['nomor'] }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $entry['keterangan'] }}</td>
                    <td class="text-end">Rp {{ number_format($entry['saldo_awal_baris'], 0, ',', '.') }}</td>
                    <td class="text-end {{ $entry['pembelian'] > 0 ? 'text-danger fw-semibold' : '' }}">
                        {{ $entry['pembelian'] > 0 ? 'Rp ' . number_format($entry['pembelian'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end {{ $entry['type'] == 'payment' && $entry['pembayaran'] > 0 ? 'text-success fw-semibold' : '' }}">
                        @if($entry['type'] == 'purchase')
                            @if(($entry['remaining'] ?? 0) <= 0)
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <form action="{{ route('payments.store') }}" method="POST" class="d-flex align-items-center justify-content-end gap-1 m-0">
                                    @csrf
                                    <input type="hidden" name="transaction_type" value="purchase">
                                    <input type="hidden" name="transaction_id" value="{{ $entry['id'] }}">
                                    <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                    <div class="input-group input-group-sm" style="width: 140px;">
                                        <input type="number" name="amount" class="form-control text-end border-success" 
                                               placeholder="{{ number_format($entry['remaining'], 0, '', '') }}" 
                                               max="{{ $entry['remaining'] }}" min="1" required style="font-size: 11px; padding: 2px 5px;">
                                        <button class="btn btn-success py-0 px-2" type="submit" title="Bayar PO Ini" style="font-size: 11px;">
                                            Bayar
                                        </button>
                                    </div>
                                </form>
                                <div class="text-muted text-end mt-1" style="font-size: 9px;">
                                    Sisa: Rp {{ number_format($entry['remaining'], 0, ',', '.') }}
                                </div>
                            @endif
                        @else
                            {{ $entry['pembayaran'] > 0 ? 'Rp ' . number_format($entry['pembayaran'], 0, ',', '.') : '-' }}
                        @endif
                    </td>
                    <td class="text-end fw-bold">Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">-</td>
                    <td class="text-end text-danger">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                    <td class="text-end text-primary">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
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
            <div class="card border-danger">
                <div class="card-body text-center py-2">
                    <small class="text-muted">Total Pembelian</small>
                    <h6 class="mb-0 text-danger">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h6>
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
                    <small class="text-muted">Saldo Akhir (Hutang)</small>
                    <h6 class="mb-0 {{ $saldoAkhir > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
    </div>
@endif
