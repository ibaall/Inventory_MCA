@extends('layouts.app')
@section('title', 'Laporan Stok Barang')

@section('content')
<div class="container">
    <h2 class="mb-4">📦 Laporan Stok Barang</h2>

    {{-- FILTER CARD --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-semibold py-2">
            <i class="bi bi-funnel"></i> Filter Stok
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stock-report.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Produk</label>
                        <select name="product_id" class="form-select form-select-sm">
                            <option value="">Semua Produk</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ ($filters['filterProduct'] ?? '') == $p->id ? 'selected' : '' }}>
                                    {{ $p->kode_barang ? $p->kode_barang . ' - ' : '' }}{{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Pelanggan</label>
                        <select name="customer" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($customers as $c)
                                <option value="{{ $c }}" {{ ($filters['filterCustomer'] ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Supplier</label>
                        <select name="supplier" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s }}" {{ ($filters['filterSupplier'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['filterDateFrom'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['filterDateTo'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-sm w-100 mb-1">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('stock-report.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Active Filter Badges --}}
    @php
        $hasFilter = ($filters['filterProduct'] ?? null) ||
                     ($filters['filterCustomer'] ?? null) ||
                     ($filters['filterSupplier'] ?? null) ||
                     ($filters['filterDateFrom'] ?? null) ||
                     ($filters['filterDateTo'] ?? null);
    @endphp
    @if($hasFilter)
    <div class="mb-3">
        <span class="text-muted small me-2">Filter aktif:</span>
        @if($filters['filterProduct'] ?? null)
            @php $fp = $products->find($filters['filterProduct']); @endphp
            <span class="badge bg-primary me-1">Produk: {{ $fp ? $fp->name : $filters['filterProduct'] }}</span>
        @endif
        @if($filters['filterCustomer'] ?? null)
            <span class="badge bg-info text-dark me-1">Pelanggan: {{ $filters['filterCustomer'] }}</span>
        @endif
        @if($filters['filterSupplier'] ?? null)
            <span class="badge bg-warning text-dark me-1">Supplier: {{ $filters['filterSupplier'] }}</span>
        @endif
        @if($filters['filterDateFrom'] ?? null)
            <span class="badge bg-secondary me-1">Dari: {{ \Carbon\Carbon::parse($filters['filterDateFrom'])->format('d/m/Y') }}</span>
        @endif
        @if($filters['filterDateTo'] ?? null)
            <span class="badge bg-secondary me-1">Sampai: {{ \Carbon\Carbon::parse($filters['filterDateTo'])->format('d/m/Y') }}</span>
        @endif
    </div>
    @endif

    {{-- STOCK TABLE --}}
    @if($paginator->isEmpty())
        <div class="alert alert-info text-center">
            <h5 class="mb-0">Belum ada data pergerakan stok{{ $hasFilter ? ' sesuai filter.' : '.' }}</h5>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mb-0" id="stockTable">
                        <thead>
                            <tr class="stock-header">
                                <th class="text-center" style="width: 35px;">No</th>
                                <th>Tanggal</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Pelanggan</th>
                                <th>No PO</th>
                                <th>Surat Jalan</th>
                                <th class="text-center col-stok-awal">Stok Awal</th>
                                <th class="text-center col-pembelian">Pembelian</th>
                                <th class="text-center col-penjualan">Penjualan</th>
                                <th class="text-center col-stok-akhir">Stok Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginator as $index => $row)
                            <tr>
                                <td class="text-center text-muted">{{ $paginator->firstItem() + $index }}</td>
                                <td class="text-nowrap">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($row->kode_barang)
                                        <span class="badge bg-secondary font-monospace">{{ $row->kode_barang }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $row->nama_barang }}</td>
                                <td>
                                    @if($row->type === 'sale')
                                        <span class="text-primary fw-medium">{{ $row->pelanggan ?? '-' }}</span>
                                    @else
                                        <span class="text-success fw-medium">{{ $row->supplier ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->po_number)
                                        <span class="badge bg-warning text-dark">{{ $row->po_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->surat_jalan_number)
                                        <span class="badge bg-dark">{{ $row->surat_jalan_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">
                                    {{ $row->stok_awal }}
                                </td>
                                <td class="text-center">
                                    @if($row->pembelian > 0)
                                        <span class="badge bg-success">+ {{ $row->pembelian }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row->penjualan > 0)
                                        <span class="badge bg-danger">- {{ $row->penjualan }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold {{ $row->stok_akhir <= 5 ? 'text-danger' : '' }}">
                                    {{ $row->stok_akhir }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $paginator->links() }}
        </div>
    @endif
</div>

<style>
    /* Force table to stay within container */
    #stockTable {
        table-layout: auto;
        width: 100%;
    }

    /* Header style */
    .stock-header {
        background-color: #4FC3F7;
        color: #000;
    }

    .stock-header th {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 7px 6px;
        border-color: #29B6F6;
        vertical-align: middle;
        text-align: center;
    }

    /* Stok columns - subtle highlight */
    .stock-header .col-stok-awal {
        background-color: #29B6F6;
    }

    .stock-header .col-pembelian {
        background-color: #66BB6A;
        color: #fff;
    }

    .stock-header .col-penjualan {
        background-color: #EF5350;
        color: #fff;
    }

    .stock-header .col-stok-akhir {
        background-color: #29B6F6;
    }

    /* Table body */
    #stockTable tbody td {
        padding: 5px 6px;
        vertical-align: middle;
        font-size: 0.8rem;
        border-color: #e0e0e0;
    }

    #stockTable tbody tr {
        transition: background-color 0.12s ease;
    }

    #stockTable tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    #stockTable tbody tr:hover {
        background-color: #e3f2fd !important;
    }

    /* Badge styling */
    #stockTable .badge {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .stock-header th {
            font-size: 0.65rem;
            padding: 4px 4px;
        }
        #stockTable tbody td {
            font-size: 0.7rem;
            padding: 3px 4px;
        }
        #stockTable .badge {
            font-size: 0.62rem;
            padding: 2px 4px;
        }
    }
</style>
@endsection
