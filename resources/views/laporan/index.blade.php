@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container">
    <h2 class="mb-4">📊 Laporan Keuangan</h2>

    {{-- ===== RINGKASAN CARD ===== --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Total Penjualan (DPP)</div>
                    <h4 class="text-success fw-bold mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Total Pembelian (PO)</div>
                    <h4 class="text-danger fw-bold mb-0">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Laba Kotor (Penjualan - Pembelian)</div>
                    @php $laba = $totalPenjualan - $totalPembelian; @endphp
                    <h4 class="fw-bold mb-0 {{ $laba >= 0 ? 'text-primary' : 'text-danger' }}">
                        Rp {{ number_format($laba, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TAB NAVIGATION ===== --}}
    <ul class="nav nav-tabs mb-0" id="laporanTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-semibold" id="tab-penjualan" data-bs-toggle="tab" data-bs-target="#pane-penjualan" type="button">
                📈 Penjualan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold" id="tab-pembelian" data-bs-toggle="tab" data-bs-target="#pane-pembelian" type="button">
                🛒 Pembelian (PO)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold" id="tab-ringkasan" data-bs-toggle="tab" data-bs-target="#pane-ringkasan" type="button">
                📋 Ringkasan Bulanan
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 rounded-bottom bg-white p-3">

        {{-- ===== TAB 1: TRANSAKSI PENJUALAN ===== --}}
        <div class="tab-pane fade show active" id="pane-penjualan">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Transaksi Penjualan Terbaru</h5>
                <a href="{{ route('laporan.export') }}" class="btn btn-success btn-sm">📥 Export Excel</a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="alert alert-info">Belum ada transaksi penjualan.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">No</th>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Penjual</th>
                                <th class="text-end">DPP</th>
                                <th class="text-end">PPN</th>
                                <th class="text-end">Total</th>
                                <th>Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">No. Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            @php
                                $ppn = ($order->use_ppn ?? true) ? round($order->total_price * 0.11) : 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
                                <td>{{ $order->customer_name ?? '-' }}</td>
                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($order->total_price + $ppn, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->ordered_at)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if($order->status_pembayaran === 'lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($order->invoice_number)
                                        <span class="badge bg-dark">{{ $order->invoice_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total:</td>
                                <td class="text-end">Rp {{ number_format($recentOrders->sum('total_price'), 0, ',', '.') }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($recentOrders->sum(fn($o) => ($o->use_ppn ?? true) ? round($o->total_price * 0.11) : 0), 0, ',', '.') }}
                                </td>
                                <td class="text-end text-success">
                                    Rp {{ number_format($recentOrders->sum(fn($o) => $o->total_price + (($o->use_ppn ?? true) ? round($o->total_price * 0.11) : 0)), 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- ===== TAB 2: TRANSAKSI PEMBELIAN (PO) ===== --}}
        <div class="tab-pane fade" id="pane-pembelian">
            <h5 class="mb-3">Transaksi Pembelian (Purchase Order) Terbaru</h5>

            @if($recentPOs->isEmpty())
                <div class="alert alert-info">Belum ada transaksi pembelian.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">No</th>
                                <th>No. PO</th>
                                <th>Supplier</th>
                                <th>Dibuat oleh</th>
                                <th class="text-end">DPP</th>
                                <th class="text-end">PPN</th>
                                <th class="text-end">Total</th>
                                <th>Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPOs as $po)
                            @php
                                $ppnPo = ($po->use_ppn ?? true) ? round($po->total_price * 0.11) : 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    @if($po->po_number)
                                        <span class="badge bg-secondary">{{ $po->po_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $po->supplier_name }}</td>
                                <td>{{ $po->user->name ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($ppnPo, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($po->total_price + $ppnPo, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($po->ordered_at)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if($po->status === 'diterima')
                                        <span class="badge bg-success">Diterima</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total:</td>
                                <td class="text-end">Rp {{ number_format($recentPOs->sum('total_price'), 0, ',', '.') }}</td>
                                <td class="text-end">
                                    Rp {{ number_format($recentPOs->sum(fn($p) => ($p->use_ppn ?? true) ? round($p->total_price * 0.11) : 0), 0, ',', '.') }}
                                </td>
                                <td class="text-end text-danger">
                                    Rp {{ number_format($recentPOs->sum(fn($p) => $p->total_price + (($p->use_ppn ?? true) ? round($p->total_price * 0.11) : 0)), 0, ',', '.') }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- ===== TAB 3: RINGKASAN BULANAN ===== --}}
        <div class="tab-pane fade" id="pane-ringkasan">
            <div class="row">
                {{-- Ringkasan Penjualan --}}
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white fw-semibold">📈 Penjualan per Bulan</div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bulan</th>
                                        <th class="text-center">Transaksi</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Export</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laporanPenjualan as $data)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::create()->month($data->bulan)->translatedFormat('F') }} {{ $data->tahun }}</td>
                                        <td class="text-center">{{ $data->jumlah_transaksi }}</td>
                                        <td class="text-end text-success">Rp {{ number_format($data->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('laporan.export', ['bulan' => $data->bulan, 'tahun' => $data->tahun]) }}"
                                               class="btn btn-outline-primary btn-sm">📥</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Pembelian --}}
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-danger text-white fw-semibold">🛒 Pembelian (PO) per Bulan</div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bulan</th>
                                        <th class="text-center">Transaksi</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporanPembelian as $data)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::create()->month($data->bulan)->translatedFormat('F') }} {{ $data->tahun }}</td>
                                        <td class="text-center">{{ $data->jumlah_transaksi }}</td>
                                        <td class="text-end text-danger">Rp {{ number_format($data->total, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bootstrap Tab JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
