@extends('layouts.app')

@section('title', 'Laporan Keuangan Detail')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-graph-up-arrow text-primary"></i> Laporan Keuangan
            </h2>
            <p class="text-muted mb-0">Laporan hutang, piutang, dan register transaksi</p>
        </div>
        <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Ringkasan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter Section --}}
    @include('reports.financial.partials.filter')

    {{-- Report Content --}}
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-file-earmark-text text-primary"></i>
                {{ $reportTitle }}
                @if($periodLabel)
                    <small class="text-muted ms-2">— {{ $periodLabel }}</small>
                @endif
            </h5>
            <div class="d-flex gap-2">
                @php
                    $queryString = http_build_query($filters);
                @endphp
                <a href="{{ route('financial-reports.cetak') }}?{{ $queryString }}" target="_blank"
                   class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </a>
                <a href="{{ route('financial-reports.pdf') }}?{{ $queryString }}"
                   class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @switch($jenisLaporan)
                @case('purchase_ledger')
                    @include('reports.financial.partials.purchase-ledger')
                    @break
                @case('sales_ledger')
                    @include('reports.financial.partials.sales-ledger')
                    @break
                @case('purchase_register')
                    @include('reports.financial.partials.purchase-register')
                    @break
                @case('sales_register')
                    @include('reports.financial.partials.sales-register')
                    @break
            @endswitch
        </div>
    </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle filter fields berdasarkan jenis laporan
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis_laporan');
        const supplierGroup = document.getElementById('supplier-group');
        const customerGroup = document.getElementById('customer-group');

        function toggleFields() {
            const val = jenisSelect.value;
            if (val === 'purchase_ledger' || val === 'purchase_register') {
                supplierGroup.style.display = 'block';
                customerGroup.style.display = 'none';
            } else if (val === 'sales_ledger' || val === 'sales_register') {
                supplierGroup.style.display = 'none';
                customerGroup.style.display = 'block';
            }
        }

        jenisSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection
