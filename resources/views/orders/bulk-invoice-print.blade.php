<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $nomorInvoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #f0f0f0;
        }

        /* ===== TOOLBAR (hanya tampil di layar, hilang saat print) ===== */
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
            background: #38a169;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #276749; }
        .btn-close-tab {
            background: #718096;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-close-tab:hover { background: #4a5568; }

        /* ===== HALAMAN INVOICE ===== */
        .page-wrapper {
            display: flex;
            justify-content: center;
            padding: 24px;
        }

        .invoice-paper {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 20px 28px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* ===== HEADER ===== */
        .header-box {
            border: 2px solid #555;
            display: flex;
            align-items: center;
            padding: 10px 14px;
            margin-bottom: 18px;
            gap: 16px;
        }

        .logo-wrap {
            flex-shrink: 0;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-diamond {
            width: 60px;
            height: 60px;
            border: 3px solid #333;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-diamond span {
            transform: rotate(-45deg);
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        .company-info { border-left: 1px solid #bbb; padding-left: 14px; }
        .company-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .company-detail { font-size: 9.5px; color: #444; line-height: 1.7; }

        /* ===== TITLE ===== */
        .invoice-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 3px;
            margin-bottom: 16px;
        }

        /* ===== INFO ===== */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .info-left { width: 52%; font-size: 11px; line-height: 1.8; }
        .info-left .recipient-name { font-weight: bold; font-size: 12px; }
        .info-right { width: 45%; font-size: 11px; }
        .info-right table { width: 100%; border-collapse: collapse; }
        .info-right td { padding: 2px 4px; line-height: 1.8; }

        .intro { font-size: 11px; margin-bottom: 10px; }

        /* ===== TABEL ITEM ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #e8e8e8;
            border: 1px solid #888;
            padding: 5px 6px;
            text-align: center;
            font-size: 11px;
        }
        .items-table td {
            border: 1px solid #888;
            padding: 5px 6px;
            font-size: 11px;
        }
        .col-no    { width: 5%;  text-align: center; }
        .col-qty   { width: 6%;  text-align: center; }
        .col-sat   { width: 8%;  text-align: center; }
        .col-desc  { width: 48%; }
        .col-price { width: 16%; text-align: right; }
        .col-total { width: 17%; text-align: right; }
        .empty-row td { height: 20px; }
        .dash { color: #ccc; text-align: right; }

        /* ===== FOOTER TABEL ===== */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            border: 1px solid #888;
            padding: 5px 8px;
            font-size: 11px;
        }
        .footer-px   { width: 55%; vertical-align: middle; }
        .label-col   { width: 22%; font-weight: bold; }
        .value-col   { width: 23%; text-align: right; }
        .total-row   { background: #e8e8e8; font-weight: bold; }

        /* ===== BAGIAN BAWAH ===== */
        .bottom { margin-top: 18px; font-size: 11px; }
        .bottom-note { margin-bottom: 12px; }
        .bottom-flex { display: flex; justify-content: space-between; align-items: flex-start; }

        .signature-table { border-collapse: collapse; }
        .signature-table td {
            border: 1px solid #666;
            width: 120px;
            height: 72px;
            text-align: center;
            vertical-align: top;
            padding: 5px 8px;
            font-size: 11px;
        }

        .bank-info { padding-left: 24px; line-height: 2; }
        .bank-name { font-weight: bold; font-size: 12px; }

        /* ===== PRINT MEDIA ===== */
       /* ===== HILANGKAN HEADER FOOTER BROWSER SAAT PRINT ===== */
@page {
    size: A4 portrait;
    margin: 0;          /* ← ini yang menghilangkan URL, tanggal, nomor halaman */
}

@media print {
    body { background: white; }

    .toolbar { display: none !important; }

    .page-wrapper {
        padding: 0;
        display: block;
    }

    .invoice-paper {
        width: 100%;
        min-height: auto;
        box-shadow: none;
        padding: 10mm 14mm;  /* ← ganti margin halaman dari sini */
    }

    table { page-break-inside: auto; }
    tr    { page-break-inside: avoid; }

    .items-table th,
    .total-row,
    .footer-table .label-col {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

    </style>
</head>
<body>

    {{-- ===== TOOLBAR (hilang saat print) ===== --}}
    <div class="toolbar">
        <span>🧾 Preview Invoice Gabungan — {{ $nomorInvoice }}</span>
        <div class="btn-group">
            <button class="btn-print" onclick="window.print()">
                🖨️ Cetak Sekarang
            </button>
            <button class="btn-close-tab" onclick="window.close()">✕ Tutup</button>
        </div>
    </div>

    {{-- ===== HALAMAN INVOICE ===== --}}
    <div class="page-wrapper">
        <div class="invoice-paper">

            {{-- Header --}}
            <div class="header-box">
                <div class="logo-wrap">
                    <div class="logo-diamond">
                        <span>MCA</span>
                    </div>
                </div>
                <div class="company-info">
                    <div class="company-name">PT MEGAH CATUR ABADI</div>
                    <div class="company-detail">
                        Jl. Kedung Cowek Gg. Elberkah No. 2, Tanah Kali Kedinding,<br>
                        Kec. Kenjeran Surabaya, Jawa Timur 60125<br>
                        Telp. (031) 99925160 &nbsp;&nbsp; E-mail : megahcaturabadi@gmail.com
                    </div>
                </div>
            </div>

            {{-- Judul --}}
            <div class="invoice-title">INVOICE</div>

            {{-- Info Penerima & Nomor --}}
            <div class="info-section">
                <div class="info-left">
                    <div>Kepada yth,</div>
                    <div class="recipient-name">{{ $order->customer_name }}</div>
                </div>
                <div class="info-right">
                    <table>
                        <tr>
                            <td style="width:45%">Tanggal</td>
                            <td style="width:5%">:</td>
                            <td>{{ \Carbon\Carbon::parse($order->ordered_at)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Invoice</td>
                            <td>:</td>
                            <td>{{ $nomorInvoice }}</td>
                        </tr>
                        <tr>
                            <td>Metode Bayar</td>
                            <td>:</td>
                            <td>{{ ucfirst($order->metode_pembayaran) }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td>{{ ucfirst($order->status_pembayaran) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="intro">Bersama ini kami kirimkan sejumlah barang berikut ini :</div>

            {{-- Tabel Item --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-qty">QTY</th>
                        <th class="col-sat">SAT.</th>
                        <th class="col-desc">DESCRIPTION</th>
                        <th class="col-price">PRICE</th>
                        <th class="col-total">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allItems as $i => $item)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td class="col-qty">{{ $item->quantity }}</td>
                        <td class="col-sat">{{ $item->product->satuan ?? 'Pcs' }}</td>
                        <td class="col-desc">
                            {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                            @if(!empty($item->nama_varian))
                                <br><small style="color:#555;">— {{ $item->nama_varian }}</small>
                            @endif
                        </td>
                        <td class="col-price">{{ number_format($item->price, 0, '.', ',') }}</td>
                        <td class="col-total">{{ number_format($item->subtotal, 0, '.', ',') }}</td>
                    </tr>
                    @endforeach

                    {{-- Baris kosong --}}
                    @for($e = $allItems->count(); $e < 12; $e++)
                    <tr class="empty-row">
                        <td class="col-no"></td>
                        <td class="col-qty"></td>
                        <td class="col-sat"></td>
                        <td class="col-desc"></td>
                        <td class="dash">-</td>
                        <td class="dash">-</td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            {{-- Footer tabel DPP/PPN/Jumlah --}}
            <table class="footer-table">
                <tr>
                    <td class="footer-px" rowspan="3">
                        {{ \Carbon\Carbon::parse($order->ordered_at)->translatedFormat('d F Y') }}
                        / PX : {{ $order->user->name ?? '-' }}
                    </td>
                    <td class="label-col">DPP</td>
                    <td class="value-col">{{ number_format($dpp, 0, '.', ',') }}</td>
                </tr>
                <tr>
                    <td class="label-col">PPN 11%</td>
                    <td class="value-col">{{ number_format($ppn, 0, '.', ',') }}</td>
                </tr>
                <tr>
                    <td class="label-col total-row">JUMLAH</td>
                    <td class="value-col total-row">{{ number_format($jumlah, 0, '.', ',') }}</td>
                </tr>
            </table>

            {{-- Tanda tangan & bank --}}
            <div class="bottom">
                <div class="bottom-note">Mohon untuk dicek dan diterima,</div>
                <div class="bottom-flex">
                    <table class="signature-table">
                        <tr>
                            <td>Mengetahui</td>
                            <td>Penerima</td>
                        </tr>
                    </table>
                    <div class="bank-info">
                        <div class="bank-name">BANK MANDIRI</div>
                        <div>A/C ( IDR ) : 1420025406918</div>
                        <div>a/n PT. MEGAH CATUR ABADI</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Auto trigger print saat halaman dibuka --}}
    <script>
        window.addEventListener('load', function () {
            // Delay sedikit agar render selesai dulu
            setTimeout(function () {
                window.print();
            }, 800);
        });
    </script>

</body>
</html>
