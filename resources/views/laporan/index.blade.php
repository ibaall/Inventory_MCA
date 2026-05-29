@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('content')
<div class="container">
    <h2 class="mb-4">📊 Laporan Keuangan</h2>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold py-2"><i class="bi bi-funnel"></i> Filter Laporan</div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.keuangan') }}">
                <div class="row g-3 align-items-end">
                    @php $nb=[1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Bulan Dari</label>
                        <select name="bulan_dari" class="form-select form-select-sm">
                            <option value="">-- Awal --</option>
                            @foreach($nb as $n=>$nm)<option value="{{$n}}" {{($filters['filterBulanDari']??'')==$n?'selected':''}}>{{$nm}}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Bulan Sampai</label>
                        <select name="bulan_sampai" class="form-select form-select-sm">
                            <option value="">-- Akhir --</option>
                            @foreach($nb as $n=>$nm)<option value="{{$n}}" {{($filters['filterBulanSampai']??'')==$n?'selected':''}}>{{$nm}}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($availableYears as $yr)<option value="{{$yr}}" {{($filters['filterTahun']??'')==$yr?'selected':''}}>{{$yr}}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Pelanggan</label>
                        <select name="customer_name" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($customers as $c)<option value="{{$c}}" {{($filters['filterCustomer']??'')==$c?'selected':''}}>{{$c}}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Supplier</label>
                        <select name="supplier_name" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($suppliers as $s)<option value="{{$s}}" {{($filters['filterSupplier']??'')==$s?'selected':''}}>{{$s}}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-sm w-100 mb-1"><i class="bi bi-search"></i> Filter</button>
                        <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @php $hasFilter=($filters['filterBulanDari']??null)||($filters['filterBulanSampai']??null)||($filters['filterTahun']??null)||($filters['filterCustomer']??null)||($filters['filterSupplier']??null); @endphp
    @if($hasFilter)
    <div class="mb-3">
        <span class="text-muted small me-2">Filter aktif:</span>
        @if(($filters['filterBulanDari']??null)&&($filters['filterBulanSampai']??null))
            <span class="badge bg-info text-dark me-1">Bulan: {{$nb[$filters['filterBulanDari']]}} - {{$nb[$filters['filterBulanSampai']]}}</span>
        @elseif($filters['filterBulanDari']??null)
            <span class="badge bg-info text-dark me-1">Dari: {{$nb[$filters['filterBulanDari']]}}</span>
        @elseif($filters['filterBulanSampai']??null)
            <span class="badge bg-info text-dark me-1">Sampai: {{$nb[$filters['filterBulanSampai']]}}</span>
        @endif
        @if($filters['filterTahun']??null)<span class="badge bg-info text-dark me-1">Tahun: {{$filters['filterTahun']}}</span>@endif
        @if($filters['filterCustomer']??null)<span class="badge bg-primary me-1">Pelanggan: {{$filters['filterCustomer']}}</span>@endif
        @if($filters['filterSupplier']??null)<span class="badge bg-warning text-dark me-1">Supplier: {{$filters['filterSupplier']}}</span>@endif
    </div>
    @endif

    {{-- RINGKASAN + DUA CHART SIDE-BY-SIDE --}}
    <div class="row mb-4 g-3 align-items-stretch">
        {{-- Kolom Summary Cards --}}
        <div class="col-lg-4 d-flex flex-column justify-content-between">
            <div class="card border-0 shadow-sm mb-3 flex-grow-1">
                <div class="card-body d-flex flex-column justify-content-center py-3">
                    <div class="text-muted small mb-1">Total Penjualan (DPP)</div>
                    <h3 class="text-success fw-bold mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3 flex-grow-1">
                <div class="card-body d-flex flex-column justify-content-center py-3">
                    <div class="text-muted small mb-1">Total Pembelian (PO)</div>
                    <h3 class="text-danger fw-bold mb-0">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="card border-0 shadow-sm flex-grow-1">
                <div class="card-body d-flex flex-column justify-content-center py-3">
                    <div class="text-muted small mb-1">Laba Kotor</div>
                    @php $laba=$totalPenjualan-$totalPembelian; @endphp
                    <h3 class="fw-bold mb-0 {{$laba>=0?'text-primary':'text-danger'}}">Rp {{ number_format($laba, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        {{-- Chart 1: Perbandingan Penjualan vs Pembelian --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-semibold text-center pt-3 pb-0">
                    Ratio Penjualan vs Pembelian
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 240px;">
                    <div style="width: 100%; max-width: 220px; height: 220px;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart 2: Status Pembayaran Penjualan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-semibold text-center pt-3 pb-0">
                    Status Pembayaran Penjualan (DPP)
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 240px;">
                    <div style="width: 100%; max-width: 220px; height: 220px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-0" id="laporanTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#pane-penjualan" type="button">📈 Penjualan</button></li>
        <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#pane-pembelian" type="button">🛒 Pembelian (PO)</button></li>
        <li class="nav-item"><button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#pane-ringkasan" type="button">📋 Ringkasan Bulanan</button></li>
    </ul>
    <div class="tab-content border border-top-0 rounded-bottom bg-white p-3">

        {{-- TAB PENJUALAN --}}
        <div class="tab-pane fade show active" id="pane-penjualan">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Transaksi Penjualan {{$hasFilter?'(Filtered)':''}}</h5>
                <a href="{{ route('laporan.export', array_merge(request()->query(), ['type' => 'penjualan'])) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Penjualan (Excel)
                </a>
            </div>
            @if($recentOrders->isEmpty())
                <div class="alert alert-info">Belum ada transaksi penjualan{{$hasFilter?' sesuai filter.':'.'}}</div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark"><tr>
                        <th class="text-center">No</th><th>ID</th><th>Pelanggan</th><th>Penjual</th>
                        <th class="text-end">DPP</th><th class="text-end">PPN</th><th class="text-end">Total</th>
                        <th>Tanggal</th><th class="text-center">Status</th><th class="text-center">No. Invoice</th>
                    </tr></thead>
                    <tbody>
                    @foreach($recentOrders as $order)
                    @php $ppn=($order->use_ppn??true)?round($order->total_price*0.11):0; @endphp
                    <tr>
                        <td class="text-center">{{$loop->iteration}}</td>
                        <td><span class="badge bg-secondary">#{{$order->id}}</span></td>
                        <td>{{$order->customer_name??'-'}}</td>
                        <td>{{$order->user->name??'-'}}</td>
                        <td class="text-end">Rp {{number_format($order->total_price,0,',','.')}}</td>
                        <td class="text-end">Rp {{number_format($ppn,0,',','.')}}</td>
                        <td class="text-end fw-semibold">Rp {{number_format($order->total_price+$ppn,0,',','.')}}</td>
                        <td>{{\Carbon\Carbon::parse($order->ordered_at)->format('d/m/Y')}}</td>
                        <td class="text-center">
                            @if($order->status_pembayaran==='lunas')<span class="badge bg-success">Lunas</span>
                            @else <span class="badge bg-warning text-dark">Belum</span>@endif
                        </td>
                        <td class="text-center">
                            @if($order->invoice_number)<span class="badge bg-dark">{{$order->invoice_number}}</span>
                            @else <span class="text-muted">-</span>@endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold"><tr>
                        <td colspan="4" class="text-end">Total:</td>
                        <td class="text-end">Rp {{number_format($recentOrders->sum('total_price'),0,',','.')}}</td>
                        <td class="text-end">Rp {{number_format($recentOrders->sum(fn($o)=>($o->use_ppn??true)?round($o->total_price*0.11):0),0,',','.')}}</td>
                        <td class="text-end text-success">Rp {{number_format($recentOrders->sum(fn($o)=>$o->total_price+(($o->use_ppn??true)?round($o->total_price*0.11):0)),0,',','.')}}</td>
                        <td colspan="3"></td>
                    </tr></tfoot>
                </table>
            </div>
            @endif
        </div>

        {{-- TAB PEMBELIAN --}}
        <div class="tab-pane fade" id="pane-pembelian">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Transaksi Pembelian (PO) {{$hasFilter?'(Filtered)':''}}</h5>
                <a href="{{ route('laporan.export', array_merge(request()->query(), ['type' => 'pembelian'])) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Pembelian (Excel)
                </a>
            </div>
            @if($recentPOs->isEmpty())
                <div class="alert alert-info">Belum ada transaksi pembelian{{$hasFilter?' sesuai filter.':'.'}}</div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark"><tr>
                        <th class="text-center">No</th><th>No. PO</th><th>Supplier</th><th>Dibuat oleh</th>
                        <th class="text-end">DPP</th><th class="text-end">PPN</th><th class="text-end">Total</th>
                        <th>Tanggal</th><th class="text-center">Status</th><th class="text-center">Aksi</th>
                    </tr></thead>
                    <tbody>
                    @foreach($recentPOs as $po)
                    @php $ppnPo=($po->use_ppn??true)?round($po->total_price*0.11):0; @endphp
                    <tr>
                        <td class="text-center">{{$loop->iteration}}</td>
                        <td>@if($po->po_number)<span class="badge bg-secondary">{{$po->po_number}}</span>@else<span class="text-muted">-</span>@endif</td>
                        <td>{{$po->supplier_name}}</td>
                        <td>{{$po->user->name??'-'}}</td>
                        <td class="text-end">Rp {{number_format($po->total_price,0,',','.')}}</td>
                        <td class="text-end">Rp {{number_format($ppnPo,0,',','.')}}</td>
                        <td class="text-end fw-semibold">Rp {{number_format($po->total_price+$ppnPo,0,',','.')}}</td>
                        <td>{{\Carbon\Carbon::parse($po->ordered_at)->format('d/m/Y')}}</td>
                        <td class="text-center">
                            @if($po->status==='diterima')<span class="badge bg-success">Diterima</span>
                            @else <span class="badge bg-warning text-dark">Pending</span>@endif
                        </td>
                        <td class="text-center"><a href="{{route('purchase-orders.show',$po->id)}}" class="btn btn-info btn-sm">Detail</a></td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-secondary fw-bold"><tr>
                        <td colspan="4" class="text-end">Total:</td>
                        <td class="text-end">Rp {{number_format($recentPOs->sum('total_price'),0,',','.')}}</td>
                        <td class="text-end">Rp {{number_format($recentPOs->sum(fn($p)=>($p->use_ppn??true)?round($p->total_price*0.11):0),0,',','.')}}</td>
                        <td class="text-end text-danger">Rp {{number_format($recentPOs->sum(fn($p)=>$p->total_price+(($p->use_ppn??true)?round($p->total_price*0.11):0)),0,',','.')}}</td>
                        <td colspan="3"></td>
                    </tr></tfoot>
                </table>
            </div>
            @endif
        </div>

        {{-- TAB RINGKASAN --}}
        <div class="tab-pane fade" id="pane-ringkasan">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Ringkasan Bulanan {{$hasFilter?'(Filtered)':''}}</h5>
                <a href="{{ route('laporan.export', array_merge(request()->query(), ['type' => 'ringkasan'])) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Ringkasan (Excel)
                </a>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white fw-semibold">📈 Penjualan per Bulan</div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light"><tr><th>Bulan</th><th class="text-center">Transaksi</th><th class="text-end">Total</th><th class="text-center">Export</th></tr></thead>
                                <tbody>
                                @forelse($laporanPenjualan as $data)
                                <tr>
                                    <td>{{\Carbon\Carbon::create()->month($data->bulan)->translatedFormat('F')}} {{$data->tahun}}</td>
                                    <td class="text-center">{{$data->jumlah_transaksi}}</td>
                                    <td class="text-end text-success">Rp {{number_format($data->total,0,',','.')}}</td>
                                    <td class="text-center">
                                        <a href="{{ route('laporan.export', ['type' => 'penjualan', 'bulan_dari' => $data->bulan, 'bulan_sampai' => $data->bulan, 'tahun' => $data->tahun]) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Export Excel Bulan Ini">📥</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-danger text-white fw-semibold">🛒 Pembelian (PO) per Bulan</div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light"><tr><th>Bulan</th><th class="text-center">Transaksi</th><th class="text-end">Total</th><th class="text-center">Export</th></tr></thead>
                                <tbody>
                                @forelse($laporanPembelian as $data)
                                <tr>
                                    <td>{{\Carbon\Carbon::create()->month($data->bulan)->translatedFormat('F')}} {{$data->tahun}}</td>
                                    <td class="text-center">{{$data->jumlah_transaksi}}</td>
                                    <td class="text-end text-danger">Rp {{number_format($data->total,0,',','.')}}</td>
                                    <td class="text-center">
                                        <a href="{{ route('laporan.export', ['type' => 'pembelian', 'bulan_dari' => $data->bulan, 'bulan_sampai' => $data->bulan, 'tahun' => $data->tahun]) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Export Excel Bulan Ini">📥</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cd = @json($chartData);
    
    // Hitung status pembayaran penjualan (Lunas vs Belum Lunas) dari data order yang ada
    @php
        $totalLunas = $recentOrders->where('status_pembayaran', 'lunas')->sum('total_price');
        $totalBelum = $recentOrders->where('status_pembayaran', '!=', 'lunas')->sum('total_price');
    @endphp

    // Chart 1: Ratio Penjualan vs Pembelian
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Penjualan (DPP)', 'Pembelian (PO)'],
            datasets: [{
                data: [cd.totalPenjualan, cd.totalPembelian],
                backgroundColor: ['#198754', '#dc3545'],
                borderWidth: 1.5, borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Chart 2: Status Pembayaran Penjualan
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Dibayar'],
            datasets: [{
                data: [{{ $totalLunas }}, {{ $totalBelum }}],
                backgroundColor: ['#0d6efd', '#ffc107'],
                borderWidth: 1.5, borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
