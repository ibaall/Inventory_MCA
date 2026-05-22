<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan {{ $nomorSJ }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #f0f0f0;
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            background: #2d3748;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .toolbar span { font-size: 14px; font-weight: bold; }
        .toolbar .btn-group { display: flex; gap: 10px; }
        .btn-print {
            background: #38a169; color: white; border: none;
            padding: 8px 20px; border-radius: 6px; font-size: 13px;
            cursor: pointer; font-weight: bold;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-print:hover { background: #276749; }
        .btn-close-tab {
            background: #718096; color: white; border: none;
            padding: 8px 16px; border-radius: 6px; font-size: 13px; cursor: pointer;
        }
        .btn-close-tab:hover { background: #4a5568; }

        /* ===== PAGE ===== */
        .page-wrapper {
            display: flex;
            justify-content: center;
            padding: 24px;
        }
        .sj-paper {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 20px 28px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* ===== HEADER ===== */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #888;
            padding: 10px 14px;
            margin-bottom: 4px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
        }
        .doc-title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* ===== INFO ===== */
        .info-section {
            display: flex;
            border: 1px solid #888;
            margin-bottom: 10px;
        }
        .info-left {
            width: 50%;
            padding: 8px 12px;
            border-right: 1px solid #888;
        }
        .info-right {
            width: 50%;
            padding: 0;
        }
        .info-right .info-row {
            display: flex;
            padding: 6px 12px;
            border-bottom: 1px solid #888;
            font-size: 11px;
        }
        .info-right .info-row:last-child { border-bottom: none; }
        .info-right .info-label { width: 45%; font-weight: bold; }
        .info-right .info-sep { width: 5%; }
        .info-right .info-val { width: 50%; }

        .recipient-name { font-weight: bold; font-size: 13px; margin-top: 4px; }

        .intro { font-size: 11px; margin-bottom: 8px; }

        /* ===== TABLE ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #d9d9d9;
            border: 1px solid #888;
            padding: 5px 6px;
            text-align: center;
            font-size: 11px;
        }
        .items-table td {
            border: 1px solid #888;
            padding: 4px 6px;
            font-size: 11px;
        }
        .col-no   { width: 5%;  text-align: center; }
        .col-qty  { width: 7%;  text-align: center; }
        .col-sat  { width: 7%;  text-align: center; }
        .col-kode { width: 18%; text-align: center; }
        .col-desc { width: 63%; }
        .empty-row td { height: 18px; }

        /* ===== SIGNATURE ===== */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }
        .sig-box {
            text-align: center;
            width: 40%;
        }
        .sig-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 70px;
        }
        .sig-line {
            border-bottom: 1px dotted #000;
            padding-bottom: 4px;
            font-size: 11px;
        }

        /* ===== PRINT ===== */
        @page {
            size: A4 portrait;
            margin: 0;
        }
        @media print {
            body { background: white; }
            .toolbar { display: none !important; }
            .page-wrapper { padding: 0; display: block; }
            .sj-paper {
                width: 100%; min-height: auto;
                box-shadow: none; padding: 10mm 14mm;
            }
            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; }
            .items-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <span>📄 Preview Surat Jalan — {{ $nomorSJ }}</span>
        <div class="btn-group">
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Sekarang</button>
            <button class="btn-close-tab" onclick="window.close()">✕ Tutup</button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="sj-paper">

            {{-- Header --}}
            <div class="header-row">
                <div class="company-name">PT. MEGAH CATUR ABADI</div>
                <div class="doc-title">SURAT JALAN</div>
            </div>

            {{-- Info --}}
            <div class="info-section">
                <div class="info-left">
                    <div><strong>Kepada yth,</strong></div>
                    <div class="recipient-name">{{ $order->customer_name }}</div>
                </div>
                <div class="info-right">
                    <div class="info-row">
                        <span class="info-label">Tanggal</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $tanggal->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nomer Surat Jalan</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $nomorSJ }}</span>
                    </div>
                </div>
            </div>

            <div class="intro">Bersama ini kami kirimkan sejumlah barang berikut ini :</div>

            {{-- Table --}}
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
                                <br><small style="color:#555;">— {{ $item->nama_varian }}</small>
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

            {{-- Signature --}}
            <div class="signature-section">
                <div class="sig-box">
                    <div class="sig-label">Menyerahkan,</div>
                    <div class="sig-line">(.........................................)</div>
                </div>
                <div class="sig-box">
                    <div class="sig-label">Menerima,</div>
                    <div class="sig-line">(.........................................)</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 800);
        });
    </script>

</body>
</html>
