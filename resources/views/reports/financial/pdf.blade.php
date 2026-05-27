<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }} - {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            color: #1a1a2e;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 13px;
            color: #16213e;
            margin-bottom: 3px;
        }
        .header .period {
            font-size: 11px;
            color: #555;
        }
        .header .party-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f3460;
            margin-top: 5px;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 9px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px 7px;
            text-align: left;
        }
        th {
            background-color: #1a1a2e;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        td {
            font-size: 9px;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .saldo-awal-row {
            background-color: #f0f0f0;
            font-style: italic;
        }
        .payment-row {
            background-color: #e8f5e9;
        }
        .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .text-primary { color: #0d6efd; }

        .summary-box {
            margin-top: 15px;
            border: 1px solid #333;
            padding: 10px;
        }
        .summary-box table {
            border: none;
            margin: 0;
        }
        .summary-box td {
            border: none;
            padding: 3px 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #999;
            text-align: right;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
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

    {{-- CONTENT BASED ON REPORT TYPE --}}
    @if($jenisLaporan == 'purchase_ledger')
        {{-- ========= LAPORAN HUTANG SUPPLIER ========= --}}
        @php
            $data = $reportData;
            $entries = $data['entries'] ?? collect();
            $saldoAwal = $data['saldo_awal'] ?? 0;
            $totalPembelian = $data['total_pembelian'] ?? 0;
            $totalPembayaran = $data['total_pembayaran'] ?? 0;
            $saldoAkhir = $data['saldo_akhir'] ?? 0;
        @endphp

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width:25px;">No</th>
                    <th style="width:70px;">Tanggal</th>
                    <th style="width:110px;">No. PO / Invoice</th>
                    <th>Keterangan</th>
                    <th class="text-end" style="width:90px;">Saldo Awal</th>
                    <th class="text-end" style="width:90px;">Pembelian</th>
                    <th class="text-end" style="width:90px;">Pembayaran</th>
                    <th class="text-end" style="width:90px;">Saldo Akhir</th>
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
                <tr class="{{ $entry['type'] == 'payment' ? 'payment-row' : '' }}">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td>{{ $entry['type'] == 'purchase' ? $entry['nomor'] : '-' }}</td>
                    <td>{{ $entry['keterangan'] }}</td>
                    <td class="text-end">Rp {{ number_format($entry['saldo_awal_baris'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ $entry['pembelian'] > 0 ? 'Rp ' . number_format($entry['pembelian'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $entry['pembayaran'] > 0 ? 'Rp ' . number_format($entry['pembayaran'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">-</td>
                    <td class="text-end text-danger">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                    <td class="text-end text-primary">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-box">
            <table>
                <tr>
                    <td style="width:150px;">Saldo Awal Periode</td>
                    <td class="fw-bold">: Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pembelian</td>
                    <td class="fw-bold text-danger">: Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pembayaran</td>
                    <td class="fw-bold text-success">: Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Saldo Akhir (Hutang)</td>
                    <td class="fw-bold text-primary">: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

    @elseif($jenisLaporan == 'sales_ledger')
        {{-- ========= LAPORAN PIUTANG CUSTOMER ========= --}}
        @php
            $data = $reportData;
            $entries = $data['entries'] ?? collect();
            $saldoAwal = $data['saldo_awal'] ?? 0;
            $totalPenjualan = $data['total_penjualan'] ?? 0;
            $totalPembayaran = $data['total_pembayaran'] ?? 0;
            $saldoAkhir = $data['saldo_akhir'] ?? 0;
        @endphp

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width:25px;">No</th>
                    <th style="width:70px;">Tanggal</th>
                    <th style="width:110px;">No. Invoice</th>
                    <th>Keterangan</th>
                    <th class="text-end" style="width:90px;">Saldo Awal</th>
                    <th class="text-end" style="width:90px;">Penjualan</th>
                    <th class="text-end" style="width:90px;">Pembayaran</th>
                    <th class="text-end" style="width:90px;">Saldo Akhir</th>
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
                <tr class="{{ $entry['type'] == 'payment' ? 'payment-row' : '' }}">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td>{{ $entry['type'] == 'sale' ? $entry['nomor'] : '-' }}</td>
                    <td>{{ $entry['keterangan'] }}</td>
                    <td class="text-end">Rp {{ number_format($entry['saldo_awal_baris'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ $entry['penjualan'] > 0 ? 'Rp ' . number_format($entry['penjualan'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $entry['pembayaran'] > 0 ? 'Rp ' . number_format($entry['pembayaran'], 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-end">TOTAL:</td>
                    <td class="text-end">-</td>
                    <td class="text-end text-primary">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    <td class="text-end text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-box">
            <table>
                <tr>
                    <td style="width:150px;">Saldo Awal Periode</td>
                    <td class="fw-bold">: Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Penjualan</td>
                    <td class="fw-bold text-primary">: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pembayaran</td>
                    <td class="fw-bold text-success">: Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Saldo Akhir (Piutang)</td>
                    <td class="fw-bold text-danger">: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

    @elseif($jenisLaporan == 'purchase_register')
        {{-- ========= REGISTER PEMBELIAN ========= --}}
        @php
            $data = $reportData;
            $items = $data['items'] ?? collect();
            $grandTotal = $data['grand_total'] ?? 0;
        @endphp

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width:20px;">No</th>
                    <th style="width:55px;">Tanggal</th>
                    <th>Supplier</th>
                    <th style="width:90px;">No. PO</th>
                    <th>Nama Barang</th>
                    <th class="text-center" style="width:30px;">Qty</th>
                    <th class="text-center" style="width:35px;">Sat</th>
                    <th class="text-end" style="width:65px;">Hrg Asli</th>
                    <th class="text-center" style="width:35px;">Disk</th>
                    <th class="text-end" style="width:65px;">Harga</th>
                    <th class="text-end" style="width:55px;">PPN</th>
                    <th class="text-end" style="width:70px;">Total Brg</th>
                    <th class="text-end" style="width:75px;">Total PO</th>
                </tr>
            </thead>
            <tbody>
                @php $prevPoId = null; @endphp
                @foreach($items as $item)
                    @php $isNewPo = $item['po_id'] !== $prevPoId; $prevPoId = $item['po_id']; $hasDiscount = ($item['discount_percent'] ?? 0) > 0; @endphp
                    <tr>
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
            <tfoot>
                <tr class="total-row">
                    <td colspan="12" class="text-end">GRAND TOTAL:</td>
                    <td class="text-end text-danger">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

    @elseif($jenisLaporan == 'sales_register')
        {{-- ========= REGISTER PENJUALAN ========= --}}
        @php
            $data = $reportData;
            $items = $data['items'] ?? collect();
            $grandTotal = $data['grand_total'] ?? 0;
        @endphp

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width:20px;">No</th>
                    <th style="width:55px;">Tanggal</th>
                    <th>Customer/PT</th>
                    <th style="width:90px;">No. Invoice</th>
                    <th>Nama Barang</th>
                    <th class="text-center" style="width:30px;">Qty</th>
                    <th class="text-center" style="width:35px;">Sat</th>
                    <th class="text-end" style="width:65px;">Hrg Asli</th>
                    <th class="text-center" style="width:35px;">Disk</th>
                    <th class="text-end" style="width:65px;">Harga</th>
                    <th class="text-end" style="width:55px;">PPN</th>
                    <th class="text-end" style="width:70px;">Total Brg</th>
                    <th class="text-end" style="width:75px;">Total Inv</th>
                </tr>
            </thead>
            <tbody>
                @php $prevOrderId = null; @endphp
                @foreach($items as $item)
                    @php $isNewOrder = $item['order_id'] !== $prevOrderId; $prevOrderId = $item['order_id']; $hasDiscount = ($item['discount_percent'] ?? 0) > 0; @endphp
                    <tr>
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
            <tfoot>
                <tr class="total-row">
                    <td colspan="12" class="text-end">GRAND TOTAL:</td>
                    <td class="text-end text-success">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        Dicetak pada: {{ $tanggalCetak }} | PT MCA - Sistem Laporan Keuangan
    </div>
</body>
</html>
