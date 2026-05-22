<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $nomorPO }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; background: #f0f0f0; }

        .toolbar { background: #2d3748; color: white; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 999; }
        .toolbar span { font-size: 14px; font-weight: bold; }
        .toolbar .btn-group { display: flex; gap: 10px; }
        .btn-print { background: #38a169; color: white; border: none; padding: 8px 20px; border-radius: 6px; font-size: 13px; cursor: pointer; font-weight: bold; }
        .btn-print:hover { background: #276749; }
        .btn-close-tab { background: #718096; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; cursor: pointer; }

        .page-wrapper { display: flex; justify-content: center; padding: 24px; }
        .po-paper { background: white; width: 210mm; min-height: 297mm; padding: 20px 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }

        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .company-name { font-size: 22px; font-weight: bold; }
        .doc-title { font-size: 18px; font-weight: bold; text-decoration: underline; }

        .info-section { display: flex; border: 1px solid #888; margin-bottom: 14px; }
        .info-left { width: 50%; padding: 8px 12px; border-right: 1px solid #888; }
        .info-right { width: 50%; padding: 0; }
        .info-right .info-row { display: flex; padding: 6px 12px; border-bottom: 1px solid #888; font-size: 11px; }
        .info-right .info-row:last-child { border-bottom: none; }
        .info-right .info-label { width: 45%; font-weight: bold; }
        .info-right .info-sep { width: 5%; }
        .info-right .info-val { width: 50%; }
        .recipient-name { font-weight: bold; font-size: 13px; margin-top: 4px; }

        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background: #d9d9d9; border: 1px solid #888; padding: 5px 6px; text-align: center; font-size: 11px; }
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
        .footer-px { width: 55%; vertical-align: middle; }
        .label-col { font-weight: bold; text-align: right; }
        .value-col { text-align: right; }
        .total-row { background: #d9d9d9; font-weight: bold; }

        .bottom { margin-top: 14px; font-size: 11px; }
        .bottom-note { margin-bottom: 12px; }
        .bottom-flex { display: flex; justify-content: space-between; align-items: flex-start; }
        .signature-table { border-collapse: collapse; }
        .signature-table td { border: 1px solid #666; width: 130px; height: 72px; text-align: center; vertical-align: top; padding: 5px 8px; font-size: 11px; }
        .bank-info { padding-left: 24px; line-height: 2; }
        .bank-name { font-weight: bold; font-size: 12px; }

        @page { size: A4 portrait; margin: 0; }
        @media print {
            body { background: white; }
            .toolbar { display: none !important; }
            .page-wrapper { padding: 0; display: block; }
            .po-paper { width: 100%; min-height: auto; box-shadow: none; padding: 10mm 14mm; }
            .items-table th, .total-row { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <span>📋 Preview Purchase Order — {{ $nomorPO }}</span>
        <div class="btn-group">
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Sekarang</button>
            <button class="btn-close-tab" onclick="window.close()">✕ Tutup</button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="po-paper">
            <div class="header-row">
                <div class="company-name">PT. MEGAH CATUR ABADI</div>
                <div class="doc-title">PURCHASE ORDER</div>
            </div>

            <div class="info-section">
                <div class="info-left">
                    <div><strong>Kepada yth,</strong></div>
                    <div class="recipient-name">{{ $po->supplier_name }}</div>
                </div>
                <div class="info-right">
                    <div class="info-row">
                        <span class="info-label">Tanggal</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $tanggal->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. Purchase Order</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $nomorPO }}</span>
                    </div>
                </div>
            </div>

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
                        <td class="col-desc">{{ $item->product->name ?? 'Produk tidak ditemukan' }}@if($item->nama_varian)<br><small style="color:#555;">— {{ $item->nama_varian }}</small>@endif</td>
                        <td class="col-qty">{{ $item->quantity }}</td>
                        <td class="col-sat">{{ $item->product->satuan ?? 'Pcs' }}</td>
                        <td class="col-price">{{ number_format($item->price, 0, '.', ',') }}</td>
                        <td class="col-total">{{ number_format($item->subtotal, 0, '.', ',') }}</td>
                    </tr>
                    @endforeach
                    @for($e = $po->items->count(); $e < 12; $e++)
                    <tr class="empty-row"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    @endfor
                </tbody>
            </table>

            <table class="footer-table">
                <tr>
                    <td class="footer-px" rowspan="{{ $usePpn ? 3 : 2 }}">
                        {{ $tanggal->translatedFormat('d F Y') }} / PX : {{ $po->user->name ?? '-' }}
                    </td>
                    <td class="label-col">DPP</td>
                    <td class="value-col">{{ number_format($dpp, 0, '.', ',') }}</td>
                </tr>
                @if($usePpn)
                <tr><td class="label-col">PPN 11%</td><td class="value-col">{{ number_format($ppn, 0, '.', ',') }}</td></tr>
                @endif
                <tr><td class="label-col total-row">TOTAL</td><td class="value-col total-row">{{ number_format($jumlah, 0, '.', ',') }}</td></tr>
            </table>

            <div class="bottom">
                <div class="bottom-note">Mohon untuk dicek dan diterima,</div>
                <div class="bottom-flex">
                    <table class="signature-table"><tr><td>Mengetahui</td><td>Penerima</td></tr></table>
                    <div class="bank-info">
                        <div class="bank-name">BANK MANDIRI</div>
                        <div>A/C ( IDR ) : 1420025406918</div>
                        <div>a/n PT. MEGAH CATUR ABADI</div>
                    </div>
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
