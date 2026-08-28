<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar - {{ $selectedPerkiraan ? $selectedPerkiraan->kode_perkiraan . ' - ' . $selectedPerkiraan->nama_perkiraan : '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #333; }
        @page { size: A4 landscape; margin: 15mm; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #212529;
            padding-bottom: 15px;
        }
        .print-header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 1px; }
        .print-header h2 { font-size: 16px; font-weight: bold; margin-bottom: 8px; }
        .print-header .info { font-size: 12px; color: #555; }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }
        .meta-info strong { color: #212529; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table th, table td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            font-size: 10px;
        }
        table th {
            background-color: #212529;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        table tbody tr:hover { background-color: #e9ecef; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }

        .total-row { background-color: #ffc107 !important; font-weight: bold; }
        .total-row td { border-top: 2px solid #212529; }

        .print-btn {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 999;
            background: #212529;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }
        .print-btn:hover { background: #ffc107; color: #212529; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak</button>

    <div class="print-header">
        <h1>PT MEGAH CATUR ABADI</h1>
        <h2>BUKU BESAR</h2>
        @if($selectedPerkiraan)
            <div class="info">
                <strong>Nomor Perkiraan:</strong> {{ $selectedPerkiraan->kode_perkiraan }} &nbsp;|&nbsp;
                <strong>Nama Perkiraan:</strong> {{ $selectedPerkiraan->nama_perkiraan }}
                &nbsp;|&nbsp; <strong>Periode:</strong>
                {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            </div>
        @endif
    </div>

    @if($selectedPerkiraan && $entries->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:4%" class="text-center">No</th>
                    <th style="width:12%">Tanggal</th>
                    <th style="width:8%">Sumber</th>
                    <th style="width:12%">No. Bukti</th>
                    <th style="width:34%">Keterangan</th>
                    <th style="width:15%" class="text-end">Debit (Rp)</th>
                    <th style="width:15%" class="text-end">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $i => $entry)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($entry['tanggal'])->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $entry['sumber'] }}</td>
                        <td>{{ $entry['no_bukti'] }}</td>
                        <td>{{ $entry['keterangan'] }}</td>
                        <td class="text-end">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                        <td class="text-end">{{ $entry['kredit'] > 0 ? number_format($entry['kredit'], 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-end fw-bold">TOTAL</td>
                    <td class="text-end fw-bold">{{ number_format($entries->sum('debit'), 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($entries->sum('kredit'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p style="text-align:center; padding: 30px; color: #888;">Tidak ada data untuk ditampilkan.</p>
    @endif

    <div style="margin-top: 20px; font-size: 9px; color: #888; text-align: right;">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
