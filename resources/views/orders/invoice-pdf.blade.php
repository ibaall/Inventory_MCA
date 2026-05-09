<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $nomorInvoice }}</title>
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
            border: 2px solid #555;
            margin-bottom: 18px;
        }
        .header-table td { vertical-align: middle; padding: 8px 12px; }

        .logo-box {
            width: 90px;
            text-align: center;
        }
        .logo-diamond {
            width: 70px;
            height: 70px;
            border: 3px solid #333;
            transform: rotate(45deg);
            margin: 0 auto;
            position: relative;
            display: inline-block;
        }
        .logo-text {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            white-space: nowrap;
        }

        .company-info { padding-left: 10px; border-left: 1px solid #aaa; }
        .company-name { font-size: 18px; font-weight: bold; color: #000; margin-bottom: 4px; }
        .company-detail { font-size: 9.5px; color: #333; line-height: 1.6; }

        /* ===== TITLE ===== */
        .invoice-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 14px;
            letter-spacing: 2px;
        }

        /* ===== INFO SECTION ===== */
        .info-table { width: 100%; margin-bottom: 12px; }
        .info-table td { vertical-align: top; font-size: 11px; padding: 2px 0; }
        .info-left { width: 55%; }
        .info-right { width: 45%; }
        .info-right table { width: 100%; }
        .info-right td { padding: 2px 4px; }

        .intro-text { margin-bottom: 10px; font-size: 11px; }

        /* ===== TABEL ITEM ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .items-table th {
            background-color: #e8e8e8;
            border: 1px solid #888;
            text-align: center;
            padding: 5px 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #888;
            padding: 5px 6px;
            font-size: 11px;
        }
        .items-table .col-no       { width: 5%;  text-align: center; }
        .items-table .col-qty      { width: 6%;  text-align: center; }
        .items-table .col-sat      { width: 8%;  text-align: center; }
        .items-table .col-desc     { width: 48%; }
        .items-table .col-price    { width: 16%; text-align: right; }
        .items-table .col-total    { width: 17%; text-align: right; }

        .items-table .empty-row td { height: 20px; }

        /* ===== FOOTER TABEL ===== */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border-top: none;
        }
        .footer-table td {
            border: 1px solid #888;
            padding: 5px 8px;
            font-size: 11px;
        }
        .footer-left  { width: 55%; vertical-align: middle; }
        .footer-right { width: 45%; }
        .footer-right table { width: 100%; border-collapse: collapse; }
        .footer-right td { padding: 4px 8px; border: 1px solid #888; }
        .footer-label { width: 50%; font-weight: bold; }
        .footer-value { width: 50%; text-align: right; }
        .footer-total { font-weight: bold; background-color: #e8e8e8; }

        /* ===== BOTTOM ===== */
        .bottom-section { margin-top: 16px; }
        .bottom-note { font-size: 11px; margin-bottom: 10px; }

        .bottom-table { width: 100%; }
        .bottom-table td { vertical-align: top; font-size: 11px; }

        .signature-table { border-collapse: collapse; }
        .signature-table td {
            border: 1px solid #666;
            width: 120px;
            height: 70px;
            text-align: center;
            vertical-align: top;
            padding: 4px 8px;
            font-size: 11px;
        }

        .bank-info { padding-left: 20px; line-height: 1.8; }
        .bank-name { font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>

    {{-- ===== HEADER ===== --}}
    <table class="header-table">
        <tr>
            {{-- Logo MCA --}}
            <td class="logo-box" style="width:90px; text-align:center;">
                <div style="
                    width:65px; height:65px;
                    border:3px solid #333;
                    transform:rotate(45deg);
                    margin:0 auto;
                    position:relative;
                ">
                    <span style="
                        position:absolute; top:50%; left:50%;
                        transform:translate(-50%,-50%) rotate(-45deg);
                        font-weight:bold; font-size:13px; color:#333;
                        white-space:nowrap;
                    ">MCA</span>
                </div>
            </td>

            {{-- Info Perusahaan --}}
            <td class="company-info">
                <div class="company-name">PT MEGAH CATUR ABADI</div>
                <div class="company-detail">
                    Jl. Kedung Cowek Gg. Elberkah No. 2, Tanah Kali Kedinding,<br>
                    Kec. Kenjeran Surabaya, Jawa Timur 60125<br>
                    Telp. (031) 99925160 &nbsp; E-mail : megahcaturabadi@gmail.com
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== JUDUL ===== --}}
    <div class="invoice-title">INVOICE</div>

    {{-- ===== INFO PENERIMA & NOMOR ===== --}}
    <table class="info-table">
        <tr>
            <td class="info-left">
                <div>Kepada yth,</div>
                <div style="font-weight:bold; margin: 2px 0;">{{ $order->customer_name }}</div>
                <div style="font-size:10.5px; color:#333; margin-top:2px;">
                    {{-- Jika ada alamat, tampilkan di sini --}}
                </div>
            </td>
            <td class="info-right">
                <table>
                    <tr>
                        <td style="width:45%;">Tanggal</td>
                        <td style="width:5%;">:</td>
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
            </td>
        </tr>
    </table>

    <div class="intro-text">Bersama ini kami kirimkan sejumlah barang berikut ini :</div>

    {{-- ===== TABEL ITEM ===== --}}
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
            @foreach($order->items as $i => $item)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td class="col-qty" style="text-align:center;">{{ $item->quantity }}</td>
                <td class="col-sat" style="text-align:center;">
                    {{ $item->product->satuan ?? 'Pcs' }}
                </td>
                <td class="col-desc">
                    {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                    @if(!empty($item->nama_varian))
                        <br><small style="color:#555;">{{ $item->nama_varian }}</small>
                    @endif
                </td>
                <td class="col-price">{{ number_format($item->price, 0, '.', ',') }}</td>
                <td class="col-total">{{ number_format($item->subtotal, 0, '.', ',') }}</td>
            </tr>
            @endforeach

            {{-- Baris kosong agar tabel terlihat seperti contoh --}}
            @for($e = $order->items->count(); $e < 12; $e++)
            <tr class="empty-row">
                <td class="col-no"></td>
                <td class="col-qty"></td>
                <td class="col-sat"></td>
                <td class="col-desc"></td>
                <td class="col-price" style="text-align:right; color:#ccc;">-</td>
                <td class="col-total" style="text-align:right; color:#ccc;">-</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- ===== FOOTER TABEL (DPP, PPN, JUMLAH) ===== --}}
    <table class="footer-table">
        <tr>
            <td class="footer-left" rowspan="3" style="vertical-align:middle; font-size:11px;">
                {{ \Carbon\Carbon::parse($order->ordered_at)->format('d F Y') }} / PX : {{ $order->user->name ?? '-' }}
            </td>
            <td class="footer-label">DPP</td>
            <td class="footer-value">{{ number_format($dpp, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <td class="footer-label">PPN 11%</td>
            <td class="footer-value">{{ number_format($ppn, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <td class="footer-label footer-total">JUMLAH</td>
            <td class="footer-value footer-total">{{ number_format($jumlah, 0, '.', ',') }}</td>
        </tr>
    </table>

    {{-- ===== BAGIAN BAWAH ===== --}}
    <div class="bottom-section">
        <div class="bottom-note">Mohon untuk dicek dan diterima,</div>

        <table class="bottom-table">
            <tr>
                {{-- Kotak Tanda Tangan --}}
                <td style="width:50%;">
                    <table class="signature-table">
                        <tr>
                            <td style="width:120px; height:70px;">Mengetahui</td>
                            <td style="width:120px; height:70px;">Penerima</td>
                        </tr>
                    </table>
                </td>

                {{-- Info Bank --}}
                <td style="width:50%; vertical-align:top;">
                    <div class="bank-info">
                        <div class="bank-name">BANK MANDIRI</div>
                        <div>A/C ( IDR ) : 1420025406918</div>
                        <div>a/n PT. MEGAH CATUR ABADI</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
