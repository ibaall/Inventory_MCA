<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $bukti->jenis === 'BBM' ? 'Bukti Bank Masuk' : 'Bukti Bank Keluar' }} - {{ $bukti->no_bukti ?: 'Cetak' }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        @page{size:A4;margin:15mm}
        body{font-family:'Times New Roman',Times,serif;font-size:12px;color:#000;background:#fff}
        .page{max-width:700px;margin:0 auto;padding:10px}
        .company-name{font-size:18px;font-weight:bold;color:#006}
        .doc-title{font-size:14px;font-weight:bold;color:#006;margin-top:2px}
        .doc-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
        .doc-meta td{font-size:12px;padding:1px 4px;vertical-align:top}
        .doc-info{font-size:12px;margin-top:4px}
        .doc-info-label{color:#006;font-weight:bold}
        .items-table{width:100%;border-collapse:collapse;margin:8px 0;font-size:11px}
        .items-table th,.items-table td{border:1px solid #666;padding:4px 6px}
        .items-table th{background:#e8e8e8;font-weight:bold;text-align:center;color:#006}
        .items-table .right{text-align:right}
        .items-table .bold{font-weight:bold}
        .terbilang-box{border:1px solid #666;padding:4px 8px;margin:8px 0;font-size:11px}
        .terbilang-label{color:#006;font-weight:bold}
        .terbilang-total{text-align:right;font-weight:bold;font-size:13px;margin-top:3px}
        .sig-table{width:100%;text-align:center;font-size:11px;margin-top:30px}
        .sig-table td{vertical-align:top;padding:2px 4px}
        .sig-space{padding-bottom:50px !important}
        .sig-title{font-weight:bold;font-size:10px}
        @media print{body{background:#fff}.page{padding:0}.no-print{display:none !important}}
        @media screen{.print-bar{text-align:center;margin:15px 0}.print-bar button{padding:8px 30px;font-size:14px;cursor:pointer;background:#212529;color:#fff;border:none;border-radius:6px}.print-bar button:hover{background:#343a40}}
    </style>
</head>
<body>
<div class="no-print print-bar"><button onclick="window.print()">🖨️ Cetak Halaman</button></div>
<div class="page">
    <div class="doc-header">
        <div>
            <div class="company-name">PT. MEGAH CATUR ABADI</div>
            <div class="doc-title">{{ $bukti->jenis === 'BBM' ? 'BUKTI BANK MASUK' : 'BUKTI BANK KELUAR' }}</div>
            <div class="doc-info">
                <span class="doc-info-label">{{ $bukti->jenis === 'BBM' ? 'Diterima Dari' : 'Dibayarkan Kepada' }} :</span> <strong>{{ $bukti->pihak }}</strong>
            </div>
            <div class="doc-info">
                <span class="doc-info-label">{{ $bukti->jenis === 'BBM' ? 'Bank Tujuan' : 'Bank Sumber' }} :</span> {{ $bukti->bankAccount ? $bukti->bankAccount->kode_perkiraan . ' - ' . $bukti->bankAccount->nama_perkiraan : '-' }}
            </div>
            @if($bukti->jenis === 'BBM' && $bukti->no_invoice)
            <div class="doc-info"><span class="doc-info-label">No. Invoice :</span> {{ $bukti->no_invoice }}</div>
            @endif
            @if($bukti->jenis === 'BBK' && $bukti->no_po)
            <div class="doc-info"><span class="doc-info-label">No. PO :</span> {{ $bukti->no_po }}</div>
            @endif
            @if($bukti->jenis === 'BBK' && $bukti->bg_cheque_no)
            <div class="doc-info"><span class="doc-info-label">BG / Cheque No. :</span> {{ $bukti->bg_cheque_no }}</div>
            @endif
        </div>
        <div>
            <table class="doc-meta">
                <tr><td>No. Bukti</td><td>:</td><td>{{ $bukti->no_bukti ?: '' }}</td></tr>
                <tr><td>Tanggal</td><td>:</td><td><strong>{{ $bukti->tanggal->format('d/m/Y') }}</strong></td></tr>
            </table>
        </div>
    </div>

    <table class="items-table">
        <thead><tr><th style="width:10%">No. Account</th><th style="width:20%">Nama Perkiraan</th><th style="width:40%">Keterangan</th><th style="width:30%">Jumlah</th></tr></thead>
        <tbody>
            @if($bukti->keterangan_utama)<tr><td></td><td></td><td class="bold">{{ $bukti->keterangan_utama }}</td><td></td></tr>@endif
            @foreach($bukti->details as $d)
            <tr><td>{{ $d->kode_perkiraan }}</td><td>{{ $d->nama_perkiraan }}</td><td>{{ $d->keterangan }}</td><td class="right">{{ number_format($d->jumlah,0,',','.') }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="terbilang-box">
        <span class="terbilang-label">Terbilang :</span> {{ $bukti->terbilang ?: '...' }}
        <div class="terbilang-total">{{ number_format($bukti->total,0,',','.') }}</div>
    </div>

    @if($bukti->jenis === 'BBK')
    <table class="sig-table">
        <tr><td class="sig-space">Dibuat Oleh :</td><td class="sig-space">Diperiksa Oleh :</td><td class="sig-space">Disetujui Oleh :</td><td class="sig-space">Dibayar Oleh :</td><td class="sig-space">Penerima :</td></tr>
        <tr><td>(....................)</td><td>(....................)</td><td>(....................)</td><td>(....................)</td><td>(....................)</td></tr>
    </table>
    @else
    <table class="sig-table">
        <tr><td class="sig-space">Dibuat Oleh :</td><td class="sig-space">Diperiksa Oleh :</td><td class="sig-space">Disetujui Oleh :</td><td class="sig-space">Penerima :</td></tr>
        <tr><td>(....................)</td><td>(....................)</td><td>(....................)</td><td>(....................)</td></tr>
    </table>
    @endif
</div>
</body>
</html>
