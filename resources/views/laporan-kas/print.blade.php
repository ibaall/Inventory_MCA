<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Kas - {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 12mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #222;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 12px;
        }

        .header h2 {
            font-size: 16pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .header h3 {
            font-size: 13pt;
            font-weight: 600;
            color: #444;
        }

        .header p {
            font-size: 10pt;
            color: #666;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead th {
            background-color: #2c3e50;
            color: white;
            padding: 8px 10px;
            font-size: 10pt;
            font-weight: 600;
            text-align: center;
            border: 1px solid #2c3e50;
        }

        tbody td {
            padding: 6px 10px;
            border: 1px solid #ccc;
            font-size: 10pt;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: 700; }

        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }

        tfoot td {
            background-color: #2c3e50;
            color: white;
            padding: 8px 10px;
            font-size: 10pt;
            font-weight: 700;
            border: 1px solid #2c3e50;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: 600;
            border-radius: 3px;
            color: white;
            margin-right: 4px;
        }

        .badge-bkm { background-color: #28a745; }
        .badge-bkk { background-color: #dc3545; }

        .sub-text {
            font-size: 9pt;
            color: #777;
        }

        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 15px;
        }

        .summary-item {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: center;
        }

        .summary-item.debit { border-color: #28a745; background-color: #f0fff4; }
        .summary-item.kredit { border-color: #dc3545; background-color: #fff5f5; }
        .summary-item.saldo { border-color: #0d6efd; background-color: #f0f4ff; }

        .summary-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 13pt;
            font-weight: 700;
            margin-top: 4px;
        }

        .summary-item.debit .summary-value { color: #28a745; }
        .summary-item.kredit .summary-value { color: #dc3545; }
        .summary-item.saldo .summary-value { color: #0d6efd; }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .footer-col {
            text-align: center;
            width: 200px;
        }

        .footer-col .title {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 60px;
        }

        .footer-col .line {
            border-top: 1px solid #333;
            display: inline-block;
            width: 160px;
            margin-bottom: 4px;
        }

        .footer-col .name {
            font-size: 10pt;
            font-weight: 600;
        }

        .print-date {
            text-align: right;
            font-size: 9pt;
            color: #888;
            margin-top: 10px;
        }

        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
        }

        .btn-print {
            display: inline-block;
            padding: 10px 24px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 11pt;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .btn-print:hover { background-color: #1a252f; }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; padding: 15px;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Laporan</button>
        <button class="btn-print" onclick="window.close()" style="background-color: #6c757d;">✕ Tutup</button>
    </div>

    <div class="header">
        <h2>PT MCA</h2>
        <h3>Laporan Kas</h3>
        <p>Periode: {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:12%">Tanggal</th>
                <th style="width:28%">Keterangan</th>
                <th style="width:18%">Debit (Masuk)</th>
                <th style="width:18%">Kredit (Keluar)</th>
                <th style="width:19%">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $i => $entry)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $entry['tanggal'] instanceof \Carbon\Carbon ? $entry['tanggal']->format('d/m/Y') : \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $entry['jenis'] === 'BKM' ? 'badge-bkm' : 'badge-bkk' }}">
                            {{ $entry['jenis'] }}
                        </span>
                        {{ $entry['keterangan'] ?: '-' }}
                        @if($entry['pihak'])
                            <br><span class="sub-text">{{ $entry['pihak'] }}</span>
                        @endif
                    </td>
                    <td class="text-end {{ $entry['debit'] > 0 ? 'text-success fw-bold' : '' }}">
                        {{ $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end {{ $entry['kredit'] > 0 ? 'text-danger fw-bold' : '' }}">
                        {{ $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end fw-bold">
                        Rp {{ number_format($entry['saldo'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #999;">
                        Tidak ada transaksi kas pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($entries->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="3" class="text-center">TOTAL</td>
                    <td class="text-end">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if($entries->count() > 0)
        <div class="summary-box">
            <div class="summary-item debit">
                <div class="summary-label">Total Kas Masuk (Debit)</div>
                <div class="summary-value">Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item kredit">
                <div class="summary-label">Total Kas Keluar (Kredit)</div>
                <div class="summary-value">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item saldo">
                <div class="summary-label">Saldo Akhir</div>
                <div class="summary-value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
            </div>
        </div>
    @endif

    <div class="footer">
        <div class="footer-col">
            <div class="title">Dibuat Oleh,</div>
            <div class="line"></div>
            <div class="name">( ________________ )</div>
        </div>
        <div class="footer-col">
            <div class="title">Mengetahui,</div>
            <div class="line"></div>
            <div class="name">( ________________ )</div>
        </div>
    </div>

    <div class="print-date">
        Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

</body>
</html>
