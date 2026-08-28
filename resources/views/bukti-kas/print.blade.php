<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $bukti->jenis }} - {{ $bukti->no_bukti ?: 'Cetak' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 10mm 12mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; color: #000; background: #fff; }
        .page { max-width: 700px; margin: 0 auto; padding: 10px; }

        /* === BKM (MASUK) = BIRU === */
        .bkm .company-name { color: #0000cc; }
        .bkm .doc-title { color: #0000cc; text-decoration: underline; }
        .bkm .pihak-label { color: #0000cc; }
        .bkm .items-table th { color: #0000cc; border: 2px solid #0000cc; }
        .bkm .items-table td { border: 1px solid #0000cc; }
        .bkm .terbilang-box { border: 1px solid #0000cc; }
        .bkm .terbilang-label { color: #0000cc; }
        .bkm .total-box { border: 2px solid #c00; }
        .bkm .sig-label { font-weight: bold; }
        .bkm .border-color { border-color: #0000cc; }

        /* === BKK (KELUAR) = MERAH === */
        .bkk .company-name { color: #cc0000; }
        .bkk .doc-title { color: #cc0000; text-decoration: underline; }
        .bkk .pihak-label { color: #cc0000; }
        .bkk .items-table th { color: #cc0000; border: 2px solid #cc0000; }
        .bkk .items-table td { border: 1px solid #cc0000; }
        .bkk .terbilang-box { border: 1px solid #cc0000; }
        .bkk .terbilang-label { color: #cc0000; }
        .bkk .total-box { border: 2px solid #c00; }
        .bkk .sig-label { font-weight: bold; }
        .bkk .border-color { border-color: #cc0000; }

        .company-name { font-size: 18px; font-weight: bold; font-style: italic; }
        .doc-title { font-size: 14px; font-weight: bold; margin-top: 2px; letter-spacing: 1px; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
        .doc-header-left { flex: 1; }
        .doc-header-right { text-align: right; }
        .doc-meta { font-size: 12px; }
        .doc-meta td { padding: 1px 4px; vertical-align: top; }
        .doc-meta .meta-value { font-weight: bold; font-size: 14px; }

        .pihak-row { font-size: 12px; margin-top: 4px; margin-bottom: 8px; }
        .pihak-label { font-weight: bold; }
        .pihak-value { font-weight: bold; text-transform: uppercase; margin-left: 10px; }

        .items-table { width: 100%; border-collapse: collapse; margin: 0; font-size: 11px; }
        .items-table th { padding: 5px 8px; text-align: center; font-weight: bold; background: #f0f0f0; font-size: 11px; }
        .items-table td { padding: 4px 8px; vertical-align: top; }
        .items-table .right { text-align: right; }
        .items-table .bold { font-weight: bold; text-transform: uppercase; }
        .items-table .col-account { width: 12%; }
        .items-table .col-keterangan { width: 48%; }
        .items-table .col-jumlah-center { width: 20%; text-align: center; }
        .items-table .col-jumlah-right { width: 20%; text-align: right; }

        /* Empty rows to fill space */
        .items-table .empty-row td { height: 22px; }

        .terbilang-box { padding: 5px 8px; margin-top: 0; font-size: 11px; display: flex; justify-content: space-between; align-items: flex-start; }
        .terbilang-label { font-weight: bold; font-style: italic; }
        .terbilang-text { font-style: italic; }
        .total-box { text-align: right; font-weight: bold; font-size: 14px; padding: 4px 8px; min-width: 140px; }

        /* BKK extra info row */
        .extra-row { font-size: 11px; margin-top: 0; padding: 4px 8px; border: 1px solid; }

        /* Signature tables */
        .sig-table { width: 100%; text-align: center; font-size: 11px; margin-top: 20px; }
        .sig-table td { vertical-align: top; padding: 2px 4px; }
        .sig-space { padding-bottom: 50px !important; }
        .sig-label { font-weight: bold; }
        .sig-name { font-size: 11px; }
        .sig-title { font-weight: bold; font-size: 10px; font-style: italic; }

        @media print {
            body { background: #fff; }
            .page { padding: 0; }
            .no-print { display: none !important; }
        }
        @media screen {
            .print-bar { text-align: center; margin: 15px 0; }
            .print-bar button { padding: 8px 30px; font-size: 14px; cursor: pointer; background: #212529; color: #fff; border: none; border-radius: 6px; }
            .print-bar button:hover { background: #343a40; }
        }
    </style>
</head>
<body>
<div class="no-print print-bar"><button onclick="window.print()">🖨️ Cetak Halaman</button></div>
<div class="page {{ $bukti->jenis === 'BKK' ? 'bkk' : 'bkm' }}">

    {{-- HEADER --}}
    <div class="doc-header">
        <div class="doc-header-left">
            <div class="company-name">PT. MEGAH CATUR ABADI</div>
            <div class="doc-title">{{ $bukti->jenis === 'BKK' ? 'BUKTI KAS / BANK KELUAR' : 'BUKTI KAS / BANK MASUK' }}</div>
        </div>
        <div class="doc-header-right">
            <table class="doc-meta">
                <tr><td>No. Bukti</td><td>:</td><td></td></tr>
                <tr><td>Tanggal</td><td></td><td class="meta-value">{{ $bukti->tanggal->format('d/m/y') }}</td></tr>
            </table>
        </div>
    </div>

    {{-- PIHAK --}}
    <div class="pihak-row">
        <span class="pihak-label">{{ $bukti->jenis === 'BKK' ? 'Dibayarkan Kepada :' : 'Di Terima Dari :' }}</span>
        <span class="pihak-value">{{ $bukti->pihak }}</span>
    </div>

    {{-- ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-account">No. Account</th>
                <th class="col-keterangan">Keterangan</th>
                <th class="col-jumlah-center"></th>
                <th class="col-jumlah-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            {{-- Keterangan Utama --}}
            @if($bukti->keterangan_utama)
            <tr>
                <td></td>
                <td class="bold">{{ $bukti->keterangan_utama }}</td>
                <td></td>
                <td></td>
            </tr>
            @endif

            {{-- Detail items --}}
            @foreach($bukti->details as $detail)
            <tr>
                <td>{{ $detail->no_account }}</td>
                <td>{{ $detail->keterangan }}</td>
                <td class="right">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            {{-- Fill empty rows to give document consistent height --}}
            @for($r = count($bukti->details) + ($bukti->keterangan_utama ? 1 : 0); $r < 6; $r++)
            <tr class="empty-row">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- TERBILANG & TOTAL --}}
    <div class="terbilang-box border-color">
        <div>
            <span class="terbilang-label">Terbilang :</span>
            <span class="terbilang-text">{{ $bukti->terbilang ?: '...' }}</span>
        </div>
        <div class="total-box">
            {{ number_format($bukti->total, 0, ',', '.') }}
        </div>
    </div>

    {{-- BKK EXTRA ROW --}}
    @if($bukti->jenis === 'BKK')
    <div class="extra-row border-color">
        <strong>Bank / AC No. :</strong> .........
        &nbsp;&nbsp;&nbsp;&nbsp;
        <strong>BG/Cheque No. :</strong> .........
    </div>
    @endif

    {{-- SIGNATURES --}}
    @if($bukti->jenis === 'BKK')
    <table class="sig-table">
        <tr>
            <td class="sig-space sig-label">Disetor Oleh :</td>
            <td class="sig-space sig-label">Diperiksa oleh :</td>
            <td class="sig-space sig-label">Disetujui Oleh :</td>
            <td class="sig-space sig-label">Dibayar Oleh :</td>
            <td class="sig-space sig-label">Penerima :</td>
        </tr>
        <tr>
            <td class="sig-name">(....................)</td>
            <td class="sig-name">(....................)</td>
            <td class="sig-name">(....................)</td>
            <td class="sig-name">(....................)</td>
            <td class="sig-name">(....................)</td>
        </tr>
        <tr>
            <td class="sig-title">Kabag. F&amp;A</td>
            <td class="sig-title">Direktur</td>
            <td></td>
            <td class="sig-title">Finance</td>
            <td class="sig-title">Kasir</td>
        </tr>
    </table>
    @else
    <table class="sig-table">
        <tr>
            <td class="sig-space sig-label">Disetor Oleh :</td>
            <td class="sig-space sig-label">Mengetahui,</td>
            <td class="sig-space sig-label">Penerima :</td>
        </tr>
        <tr>
            <td class="sig-name">(....................)</td>
            <td class="sig-name">(....................)</td>
            <td class="sig-name">(....................)</td>
        </tr>
    </table>
    @endif
</div>
</body>
</html>
