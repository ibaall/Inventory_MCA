<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order {{ $nomorPO }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; padding: 20px 30px; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: middle; padding: 0; }
        .company-name { font-size: 20px; font-weight: bold; }
        .doc-title { font-size: 16px; font-weight: bold; text-decoration: underline; text-align: right; }

        .info-section { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .info-section td { vertical-align: top; padding: 3px 6px; font-size: 11px; border: 1px solid #888; }
        .info-left { width: 50%; }
        .info-right { width: 50%; }
        .info-right table { width: 100%; border-collapse: collapse; }
        .info-right table td { border: none; padding: 2px 4px; font-size: 11px; }

        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background-color: #d9d9d9; border: 1px solid #888; text-align: center; padding: 5px 4px; font-size: 11px; font-weight: bold; }
        .items-table td { border: 1px solid #888; padding: 4px 6px; font-size: 11px; }
        .col-no { width: 4%; text-align: center; }
        .col-kode { width: 12%; text-align: center; }
        .col-desc { width: 34%; }
        .col-qty { width: 6%; text-align: center; }
        .col-sat { width: 7%; text-align: center; }
        .col-price { width: 18%; text-align: right; }
        .col-total { width: 19%; text-align: right; }
        .empty-row td { height: 18px; }

        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { border: 1px solid #888; padding: 4px 8px; font-size: 11px; }
        .footer-left { width: 55%; vertical-align: middle; }
        .footer-label { font-weight: bold; text-align: right; }
        .footer-value { text-align: right; }
        .footer-total { font-weight: bold; background-color: #d9d9d9; }

        .bottom-section { margin-top: 14px; }
        .bottom-note { font-size: 11px; margin-bottom: 10px; }
        .bottom-table { width: 100%; }
        .bottom-table td { vertical-align: top; font-size: 11px; }
        .signature-table { border-collapse: collapse; }
        .signature-table td { border: 1px solid #666; width: 130px; height: 70px; text-align: center; vertical-align: top; padding: 4px 8px; font-size: 11px; }
        .bank-info { padding-left: 20px; line-height: 1.8; }
        .bank-name { font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="company-name">PT. MEGAH CATUR ABADI</td>
            <td class="doc-title">PURCHASE ORDER</td>
        </tr>
    </table>

    <table class="info-section">
        <tr>
            <td class="info-left" rowspan="2">
                <div><strong>Kepada yth,</strong></div>
                <div style="font-size: 12px; font-weight: bold; margin-top: 4px;">{{ $po->supplier_name }}</div>
                @if($po->alamat)
                    <div style="margin-top: 2px;">{{ $po->alamat }}</div>
                @endif
            </td>
            <td class="info-right">
                <table><tr><td style="width:42%; font-weight:bold;">Tanggal</td><td style="width:3%;">:</td><td>{{ $tanggal->translatedFormat('d F Y') }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="info-right">
                <table><tr><td style="width:42%; font-weight:bold;">No. Purchase Order</td><td style="width:3%;">:</td><td>{{ $nomorPO }}</td></tr></table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-kode">KODE BARANG</th>
                <th class="col-desc">KETERANGAN</th>
                <th class="col-qty">QTY</th>
                <th class="col-sat">SAT.</th>
                <th class="col-price">HARGA</th>
                <th class="col-total">SUB. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $i => $item)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td class="col-kode" style="font-size:10px;">{{ $item->product->kode_barang ?? '-' }}</td>
                <td class="col-desc">{{ $item->product->name ?? 'Produk tidak ditemukan' }}@if($item->nama_varian)<br><small style="color:#555;">{{ $item->nama_varian }}</small>@endif</td>
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-sat">{{ $item->product->satuan ?? 'Pcs' }}</td>
                <td class="col-price">{{ number_format($item->price, 0, '.', ',') }}</td>
                <td class="col-total">{{ number_format($item->subtotal, 0, '.', ',') }}</td>
            </tr>
            @endforeach
            @for($e = $po->items->count(); $e < 12; $e++)
            <tr class="empty-row"><td class="col-no"></td><td class="col-kode"></td><td class="col-desc"></td><td class="col-qty"></td><td class="col-sat"></td><td class="col-price"></td><td class="col-total"></td></tr>
            @endfor

            {{-- DPP / PPN / TOTAL rows merged into items table --}}
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold; padding: 4px 8px; border: 1px solid #888;">DPP</td>
                <td class="col-total" style="text-align: right; padding: 4px 8px; border: 1px solid #888;">{{ number_format($dpp, 0, '.', ',') }}</td>
            </tr>
            @if($usePpn)
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold; padding: 4px 8px; border: 1px solid #888;">PPN 11%</td>
                <td class="col-total" style="text-align: right; padding: 4px 8px; border: 1px solid #888;">{{ number_format($ppn, 0, '.', ',') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold; padding: 4px 8px; background-color: #d9d9d9; border: 1px solid #888;">TOTAL</td>
                <td class="col-total" style="text-align: right; font-weight: bold; padding: 4px 8px; background-color: #d9d9d9; border: 1px solid #888;">{{ number_format($jumlah, 0, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="bottom-section">
        <div class="bottom-note">Mohon untuk dicek dan diterima,</div>
        <table class="bottom-table">
            <tr>
                <td style="width:50%;">
                    <div style="margin-bottom: 6px; font-size: 11px;">
                        {{ $tanggal->translatedFormat('d F Y') }} / PX : {{ $po->user->name ?? '-' }}
                    </div>
                    <table class="signature-table"><tr><td>Mengetahui</td><td>Penerima</td></tr></table>
                </td>
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
