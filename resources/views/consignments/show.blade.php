@extends('layouts.app')

@section('title', 'Detail Konsinyasi')

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Konsinyasi</h2>
        <div>
            <a href="{{ route('consignments.print', $consignment->id) }}" class="btn btn-secondary me-2" target="_blank">
                <i class="bi bi-printer"></i> Cetak Surat Jalan
            </a>
            <a href="{{ route('consignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Info Konsinyasi -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Informasi Surat Jalan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" width="40%">No Surat Jalan</td>
                            <td class="fw-bold">{{ $consignment->surat_jalan_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($consignment->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Customer</td>
                            <td class="fw-bold">{{ $consignment->customer_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="fw-bold">
                                @if($consignment->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat Oleh</td>
                            <td class="fw-bold">{{ $consignment->user->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Barang & Input Pemakaian -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Barang Titipan & Input Pemakaian</h5>
                    @if($consignment->status === 'selesai')
                        <span class="badge bg-light text-success fw-bold"><i class="bi bi-check-circle"></i> Selesai Terpakai</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('consignments.usage', $consignment->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start">Produk / Varian</th>
                                        <th>Harga (Rp)</th>
                                        <th>Total Dititip</th>
                                        <th>Sdh Terpakai</th>
                                        <th>Sisa Di Cust</th>
                                        @if($consignment->status !== 'selesai')
                                        <th width="15%" class="bg-warning bg-opacity-25">Input Pakai</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consignment->items as $item)
                                        @php
                                            $sisa = $item->qty_total - $item->qty_terpakai;
                                            $productName = $item->variant ? $item->product->name . ' - ' . $item->variant->name : $item->product->name;
                                        @endphp
                                        <tr>
                                            <td class="text-start fw-bold">{{ $productName }}</td>
                                            <td>{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="table-primary fw-bold">{{ $item->qty_total }}</td>
                                            <td class="table-success fw-bold">{{ $item->qty_terpakai }}</td>
                                            <td class="table-danger fw-bold fs-5">{{ $sisa }}</td>
                                            
                                            @if($consignment->status !== 'selesai')
                                            <td class="bg-warning bg-opacity-25">
                                                @if($sisa > 0)
                                                    <input type="number" name="usage[{{ $item->id }}]" class="form-control form-control-sm text-center fw-bold" min="0" max="{{ $sisa }}" placeholder="0">
                                                @else
                                                    <span class="badge bg-success">Habis</span>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($consignment->status !== 'selesai')
                            <div class="card-footer bg-white text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-plus"></i> Submit Pemakaian & Buat Invoice
                                </button>
                                <div class="form-text text-muted mt-1">Sistem akan membuat Invoice otomatis hanya untuk barang yang angkanya Anda isi di kolom "Input Pakai".</div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
