<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan {{ $nomorSJ }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            padding: 20px 30px;
        }

        /* ===== HEADER ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #888;
            margin-bottom: 4px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 8px 12px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            text-align: right;
        }

        /* ===== INFO ===== */
        .info-section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-section td {
            vertical-align: top;
            padding: 4px 6px;
            font-size: 11px;
            border: 1px solid #888;
        }
        .info-left { width: 50%; }
        .info-right { width: 50%; }
        .info-right table { width: 100%; border-collapse: collapse; }
        .info-right table td {
            border: none;
            padding: 2px 4px;
            font-size: 11px;
        }

        .intro-text { margin-bottom: 8px; font-size: 11px; }

        /* ===== TABLE ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .items-table th {
            background-color: #d9d9d9;
            border: 1px solid #888;
            text-align: center;
            padding: 5px 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #888;
            padding: 4px 6px;
            font-size: 11px;
        }
        .items-table .col-no   { width: 5%;  text-align: center; }
        .items-table .col-qty  { width: 7%;  text-align: center; }
        .items-table .col-sat  { width: 7%;  text-align: center; }
        .items-table .col-kode { width: 18%; text-align: center; }
        .items-table .col-desc { width: 63%; }
        .items-table .empty-row td { height: 18px; }

        /* ===== SIGNATURE ===== */
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 30px;
            font-size: 11px;
        }
        .sig-label {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .sig-line {
            margin-top: 60px;
            border-bottom: 1px dotted #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 4px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="company-name">PT. MEGAH CATUR ABADI</td>
            <td class="doc-title">SURAT JALAN</td>
        </tr>
    </table>

    {{-- INFO --}}
    <table class="info-section">
        <tr>
            <td class="info-left" rowspan="5">
                <div><strong>Kepada yth,</strong></div>
                <div style="font-size: 12px; font-weight: bold; margin-top: 4px;">
                    {{ $order->customer_name }}
                </div>
                @if($order->alamat)
                    <div style="margin-top: 2px;">{{ $order->alamat }}</div>
                @endif
            </td>
            <td class="info-right">
                <table><tr><td style="width:45%; font-weight:bold;">Tanggal</td><td style="width:5%;">:</td><td>{{ $tanggal->translatedFormat('d F Y') }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="info-right">
                <table><tr><td style="width:45%; font-weight:bold;">Nomer Surat Jalan</td><td style="width:5%;">:</td><td>{{ $nomorSJ }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="info-right">
                <table><tr><td style="width:45%; font-weight:bold;">Tanggal Operasi</td><td style="width:5%;">:</td><td>{{ $order->tanggal_operasi ? \Carbon\Carbon::parse($order->tanggal_operasi)->translatedFormat('d F Y') : '-' }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="info-right">
                <table><tr><td style="width:45%; font-weight:bold;">Nama Pasien</td><td style="width:5%;">:</td><td>{{ $order->nama_pasien ?? '-' }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="info-right">
                <table><tr><td style="width:45%; font-weight:bold;">Operator</td><td style="width:5%;">:</td><td>{{ $order->operator ?? '-' }}</td></tr></table>
            </td>
        </tr>
    </table>

    <div class="intro-text">Bersama ini kami kirimkan sejumlah barang berikut ini :</div>

    {{-- TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-qty">QTY</th>
                <th class="col-sat">SAT.</th>
                <th class="col-kode">KODE BARANG</th>
                <th class="col-desc">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-sat">{{ $item->product->satuan ?? 'Pcs' }}</td>
                <td class="col-kode" style="font-size:10px;">{{ $item->product->kode_barang ?? '-' }}</td>
                <td class="col-desc">
                    {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                    @if(!empty($item->nama_varian))
                        <br><small style="color:#555;">{{ $item->nama_varian }}</small>
                    @endif
                </td>
            </tr>
            @endforeach

            @for($e = $order->items->count(); $e < 12; $e++)
            <tr class="empty-row">
                <td class="col-no"></td>
                <td class="col-qty"></td>
                <td class="col-sat"></td>
                <td class="col-kode"></td>
                <td class="col-desc"></td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- SIGNATURE --}}
    <div style="margin-top: 14px; font-size: 11px;">
        {{ $tanggal->translatedFormat('d F Y') }} / PX : {{ $order->user->name ?? '-' }}
    </div>
    <table class="signature-section">
        <tr>
            <td>
                <div class="sig-label">Menyerahkan,</div>
                <div class="sig-line">(.........................................)</div>
            </td>
            <td>
                <div class="sig-label">Menerima,</div>
                <div class="sig-line">(.........................................)</div>
            </td>
        </tr>
    </table>

</body>
</html>
