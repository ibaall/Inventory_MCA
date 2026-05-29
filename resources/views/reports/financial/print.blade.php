<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} - {{ $periodLabel }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #333;
            padding-bottom: 12px;
        }
        .header h1 {
            font-size: 20px;
            color: #1a1a2e;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 16px;
            color: #16213e;
            margin-bottom: 5px;
        }
        .header .period {
            font-size: 13px;
            color: #555;
        }
        .header .party-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f3460;
            margin-top: 5px;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 11px;
            color: #777;
        }
        table th {
            font-size: 11px;
        }
        table td {
            font-size: 11px;
        }
        .saldo-awal-row {
            background-color: #f8f9fa !important;
            font-style: italic;
        }
        .payment-row {
            background-color: #e8f5e9 !important;
        }
        .summary-box {
            margin-top: 20px;
            padding: 15px;
            border: 2px solid #333;
            border-radius: 5px;
        }
        .btn-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
        }
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-print" onclick="window.print()">
        🖨️ Cetak Sekarang
    </button>

    {{-- HEADER --}}
    <div class="header">
        <h1>PT MCA</h1>
        <h2>{{ $reportTitle }}</h2>
        <div class="period">Periode: {{ $periodLabel }}</div>
        @if($supplierName ?? null)
            <div class="party-name">Supplier: {{ $supplierName }}</div>
        @endif
        @if($customerName ?? null)
            <div class="party-name">Customer: {{ $customerName }}</div>
        @endif
    </div>

    <div class="meta-info">
        Tanggal Cetak: {{ $tanggalCetak }}
    </div>

    {{-- CONTENT --}}
    @if($jenisLaporan == 'purchase_ledger')
        @php
            $data = $reportData;
            $entries = $data['entries'] ?? collect();
            $saldoAwal = $data['saldo_awal'] ?? 0;
            $totalPembelian = $data['total_pembelian'] ?? 0;
            $totalPembayaran = $data['total_pembayaran'] ?? 0;
            $saldoAkhir = $data['saldo_akhir'] ?? 0;
        @endphp

        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>No. PO / Invoice</th>
                    <th>Keterangan</th>
                    <th class="text-end">Saldo Awal</th>
                    <th class="text-end">Pembelian</th>
                    <th class="text-end">Pembayaran</th>
                    <th class="text-end">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr class="saldo-awal-row">
                    <td class="text-center">-</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="fw-bold">Saldo Awal Periode</td>
                    <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    <td class="text-end">-</td>
                    <td class="text-end">-</td>
                    <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>
                @foreach($entries as $entry)
                @php
                    $subPayments = $entry['payments'] ?? [];
                    $subPayCount = count($subPayments);
                    $rowspan = ($entry['type'] === 'purchase' && $subPayCount > 0) ? 1 + $subPayCount : 1;
                @endphp
                <tr class="{{ $entry['type'] === 'payment_standalone' ? 'payment-row' : '' }}">
                    <td class="text-center" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $loop->iteration }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ in_array($entry['type'], ['purchase', 'payment_standalone']) ? $entry['nomor'] : '-' }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $entry['keterangan'] }}</td>
                    <td class="text-end" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>Rp {{ number_format($entry['saldo_awal_baris'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ $entry['pembelian'] > 0 ? 'Rp ' . number_format($entry['pembelian'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end">
                        @if($entry['type'] === 'purchase' && $subPayCount === 0)
                            -
                        @elseif($entry['type'] === 'payment_standalone')
                            Rp {{ number_format($entry['pembayaran'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-bold" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
                @if($entry['type'] === 'purchase' && $subPayCount > 0)
                    @foreach($subPayments as $sp)
                    <tr class="payment-row">
                        <td class="text-end" style="font-size: 10px;">↳ {{ \Carbon\Carbon::parse($sp['date'])->format('d/m/Y') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($sp['amount'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif
                @endforeach
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">-</td>
                    <td class="text-end text-danger">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                    <td class="text-end text-primary">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-box">
            <strong>Ringkasan:</strong>
            <table class="table table-sm table-borderless mb-0 mt-2" style="width:300px;">
                <tr><td>Saldo Awal</td><td class="fw-bold text-end">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td></tr>
                <tr><td>Total Pembelian</td><td class="fw-bold text-end text-danger">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td></tr>
                <tr><td>Total Pembayaran</td><td class="fw-bold text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td></tr>
                <tr class="border-top"><td>Saldo Akhir (Hutang)</td><td class="fw-bold text-end">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td></tr>
            </table>
        </div>

    @elseif($jenisLaporan == 'sales_ledger')
        @php
            $data = $reportData;
            $entries = $data['entries'] ?? collect();
            $saldoAwal = $data['saldo_awal'] ?? 0;
            $totalPenjualan = $data['total_penjualan'] ?? 0;
            $totalPembayaran = $data['total_pembayaran'] ?? 0;
            $saldoAkhir = $data['saldo_akhir'] ?? 0;
        @endphp

        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>No. Invoice</th>
                    <th>Keterangan</th>
                    <th class="text-end">Saldo Awal</th>
                    <th class="text-end">Penjualan</th>
                    <th class="text-end">Pembayaran</th>
                    <th class="text-end">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr class="saldo-awal-row">
                    <td class="text-center">-</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="fw-bold">Saldo Awal Periode</td>
                    <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    <td class="text-end">-</td>
                    <td class="text-end">-</td>
                    <td class="text-end fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>
                @foreach($entries as $entry)
                @php
                    $subPayments = $entry['payments'] ?? [];
                    $subPayCount = count($subPayments);
                    $rowspan = ($entry['type'] === 'sale' && $subPayCount > 0) ? 1 + $subPayCount : 1;
                @endphp
                <tr class="{{ $entry['type'] === 'payment_standalone' ? 'payment-row' : '' }}">
                    <td class="text-center" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $loop->iteration }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ in_array($entry['type'], ['sale', 'payment_standalone']) ? $entry['nomor'] : '-' }}</td>
                    <td @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>{{ $entry['keterangan'] }}</td>
                    <td class="text-end" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>Rp {{ number_format($entry['saldo_awal_baris'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ ($entry['penjualan'] ?? 0) > 0 ? 'Rp ' . number_format($entry['penjualan'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end">
                        @if($entry['type'] === 'sale' && $subPayCount === 0)
                            -
                        @elseif($entry['type'] === 'payment_standalone')
                            Rp {{ number_format($entry['pembayaran'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-bold" @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif>Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
                @if($entry['type'] === 'sale' && $subPayCount > 0)
                    @foreach($subPayments as $sp)
                    <tr class="payment-row">
                        <td class="text-end" style="font-size: 10px;">↳ {{ \Carbon\Carbon::parse($sp['date'])->format('d/m/Y') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($sp['amount'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif
                @endforeach
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">-</td>
                    <td class="text-end text-primary">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-box">
            <strong>Ringkasan:</strong>
            <table class="table table-sm table-borderless mb-0 mt-2" style="width:300px;">
                <tr><td>Saldo Awal</td><td class="fw-bold text-end">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td></tr>
                <tr><td>Total Penjualan</td><td class="fw-bold text-end text-primary">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr>
                <tr><td>Total Pembayaran</td><td class="fw-bold text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td></tr>
                <tr class="border-top"><td>Saldo Akhir (Piutang)</td><td class="fw-bold text-end">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td></tr>
            </table>
        </div>

    @elseif($jenisLaporan == 'purchase_register')
        @php
            $data = $reportData;
            $items = $data['items'] ?? collect();
            $grandTotal = $data['grand_total'] ?? 0;
        @endphp

        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>No. PO</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-end">Harga Asli</th>
                    <th class="text-center">Diskon</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">PPN</th>
                    <th class="text-end">Total Brg</th>
                    <th class="text-end">Total PO</th>
                </tr>
            </thead>
            <tbody>
                @php $prevPoId = null; @endphp
                @foreach($items as $item)
                    @php $isNewPo = $item['po_id'] !== $prevPoId; $prevPoId = $item['po_id']; $hasDiscount = ($item['discount_percent'] ?? 0) > 0; @endphp
                    <tr class="{{ $isNewPo ? 'border-top border-2' : '' }}">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $isNewPo ? $item['date']->format('d/m/Y') : '' }}</td>
                        <td>{{ $isNewPo ? $item['supplier_name'] : '' }}</td>
                        <td>{{ $isNewPo ? $item['nomor'] : '' }}</td>
                        <td>{{ $item['nama_barang'] }}@if($item['nama_varian'] ?? null) ({{ $item['nama_varian'] }})@endif</td>
                        <td class="text-center">{{ $item['qty'] }}</td>
                        <td class="text-center">{{ $item['satuan'] }}</td>
                        <td class="text-end">Rp {{ number_format($item['harga_asli'] ?? $item['harga'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $hasDiscount ? $item['discount_percent'] . '%' : '-' }}</td>
                        <td class="text-end">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ $item['ppn'] > 0 ? 'Rp ' . number_format($item['ppn'], 0, ',', '.') : '-' }}</td>
                        <td class="text-end">Rp {{ number_format($item['total_barang'], 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ $isNewPo ? 'Rp ' . number_format($item['total_invoice'], 0, ',', '.') : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="12" class="text-end">GRAND TOTAL:</td>
                    <td class="text-end text-danger">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

    @elseif($jenisLaporan == 'sales_register')
        @php
            $data = $reportData;
            $items = $data['items'] ?? collect();
            $grandTotal = $data['grand_total'] ?? 0;
        @endphp

        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>Customer/PT</th>
                    <th>No. Invoice</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-end">Harga Asli</th>
                    <th class="text-center">Diskon</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">PPN</th>
                    <th class="text-end">Total Brg</th>
                    <th class="text-end">Total Inv</th>
                </tr>
            </thead>
            <tbody>
                @php $prevOrderId = null; @endphp
                @foreach($items as $item)
                    @php $isNewOrder = $item['order_id'] !== $prevOrderId; $prevOrderId = $item['order_id']; $hasDiscount = ($item['discount_percent'] ?? 0) > 0; @endphp
                    <tr class="{{ $isNewOrder ? 'border-top border-2' : '' }}">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $isNewOrder ? $item['date']->format('d/m/Y') : '' }}</td>
                        <td>{{ $isNewOrder ? $item['customer_name'] : '' }}</td>
                        <td>{{ $isNewOrder ? $item['nomor'] : '' }}</td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td class="text-center">{{ $item['qty'] }}</td>
                        <td class="text-center">{{ $item['satuan'] }}</td>
                        <td class="text-end">Rp {{ number_format($item['harga_asli'] ?? $item['harga'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $hasDiscount ? $item['discount_percent'] . '%' : '-' }}</td>
                        <td class="text-end">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="text-end">{{ $item['ppn'] > 0 ? 'Rp ' . number_format($item['ppn'], 0, ',', '.') : '-' }}</td>
                        <td class="text-end">Rp {{ number_format($item['total_barang'], 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ $isNewOrder ? 'Rp ' . number_format($item['total_invoice'], 0, ',', '.') : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td colspan="12" class="text-end">GRAND TOTAL:</td>
                    <td class="text-end text-success">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="mt-4 text-muted" style="font-size:10px;">
        <hr>
        Dicetak pada: {{ $tanggalCetak }} | PT MCA - Sistem Laporan Keuangan
    </div>
</body>
</html>
