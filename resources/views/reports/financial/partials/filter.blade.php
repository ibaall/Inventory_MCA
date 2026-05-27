{{-- Filter Form --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary bg-gradient text-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-funnel"></i> Filter Laporan</h6>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" action="{{ route('financial-reports.index') }}" id="filterForm">
            <div class="row g-3">
                {{-- Jenis Laporan --}}
                <div class="col-md-3">
                    <label for="jenis_laporan" class="form-label fw-semibold">Jenis Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="form-select">
                        <option value="purchase_ledger" {{ ($filters['jenisLaporan'] ?? '') == 'purchase_ledger' ? 'selected' : '' }}>
                            📋 Laporan Pembelian / Hutang
                        </option>
                        <option value="sales_ledger" {{ ($filters['jenisLaporan'] ?? '') == 'sales_ledger' ? 'selected' : '' }}>
                            📋 Laporan Penjualan / Piutang
                        </option>
                        <option value="purchase_register" {{ ($filters['jenisLaporan'] ?? '') == 'purchase_register' ? 'selected' : '' }}>
                            📝 Register Pembelian
                        </option>
                        <option value="sales_register" {{ ($filters['jenisLaporan'] ?? '') == 'sales_register' ? 'selected' : '' }}>
                            📝 Register Penjualan
                        </option>
                    </select>
                </div>

                {{-- Bulan --}}
                <div class="col-md-2">
                    <label for="bulan" class="form-label fw-semibold">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($filters['bulan'] ?? now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="col-md-2">
                    <label for="tahun" class="form-label fw-semibold">Tahun</label>
                    <select name="tahun" id="tahun" class="form-select">
                        @for($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ ($filters['tahun'] ?? now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tanggal Awal (Opsional) --}}
                <div class="col-md-2">
                    <label for="tanggal_awal" class="form-label fw-semibold">Tanggal Awal <small class="text-muted">(opsional)</small></label>
                    <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control"
                           value="{{ $filters['tanggalAwal'] ?? '' }}">
                </div>

                {{-- Tanggal Akhir (Opsional) --}}
                <div class="col-md-2">
                    <label for="tanggal_akhir" class="form-label fw-semibold">Tanggal Akhir <small class="text-muted">(opsional)</small></label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                           value="{{ $filters['tanggalAkhir'] ?? '' }}">
                </div>
            </div>

            <div class="row g-3 mt-1">
                {{-- Supplier (untuk laporan pembelian) --}}
                <div class="col-md-3" id="supplier-group">
                    <label for="supplier_name" class="form-label fw-semibold">Nama Supplier</label>
                    <select name="supplier_name" id="supplier_name" class="form-select">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s }}" {{ ($filters['supplierName'] ?? '') == $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Customer (untuk laporan penjualan) --}}
                <div class="col-md-3" id="customer-group" style="display:none;">
                    <label for="customer_name" class="form-label fw-semibold">Nama Customer / PT</label>
                    <select name="customer_name" id="customer_name" class="form-select">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c }}" {{ ($filters['customerName'] ?? '') == $c ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
