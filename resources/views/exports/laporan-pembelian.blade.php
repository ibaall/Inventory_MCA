<table>
    <thead>
        <tr>
            <th colspan="9" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN PEMBELIAN (PURCHASE ORDER) - PT MCA</th>
        </tr>
        @if(($filters['bulan_dari'] ?? null) || ($filters['bulan_sampai'] ?? null) || ($filters['tahun'] ?? null) || ($filters['supplier_name'] ?? null))
        <tr>
            <th colspan="9" style="text-align: center; font-style: italic;">
                Filter: 
                @if(($filters['bulan_dari'] ?? null) && ($filters['bulan_sampai'] ?? null))
                    Bulan {{ $filters['bulan_dari'] }} - {{ $filters['bulan_sampai'] }}
                @elseif($filters['bulan_dari'] ?? null)
                    Dari Bulan {{ $filters['bulan_dari'] }}
                @elseif($filters['bulan_sampai'] ?? null)
                    Sampai Bulan {{ $filters['bulan_sampai'] }}
                @endif
                @if($filters['tahun'] ?? null) Tahun {{ $filters['tahun'] }} @endif
                @if($filters['supplier_name'] ?? null) Supplier: {{ $filters['supplier_name'] }} @endif
            </th>
        </tr>
        @endif
        <tr></tr> {{-- Baris Kosong --}}
        <tr style="background-color: #1a1a2e; color: #ffffff; font-weight: bold;">
            <th style="border: 1px solid #000000; text-align: center;">No</th>
            <th style="border: 1px solid #000000; text-align: center;">No. PO</th>
            <th style="border: 1px solid #000000;">Supplier</th>
            <th style="border: 1px solid #000000;">Dibuat Oleh</th>
            <th style="border: 1px solid #000000; text-align: right;">DPP</th>
            <th style="border: 1px solid #000000; text-align: right;">PPN (11%)</th>
            <th style="border: 1px solid #000000; text-align: right;">Total</th>
            <th style="border: 1px solid #000000; text-align: center;">Tanggal</th>
            <th style="border: 1px solid #000000; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pos as $po)
        @php
            $ppnPo = ($po->use_ppn ?? true) ? round($po->total_price * 0.11) : 0;
        @endphp
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $po->po_number ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $po->supplier_name }}</td>
            <td style="border: 1px solid #000000;">{{ $po->user->name ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $po->total_price }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $ppnPo }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $po->total_price + $ppnPo }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ \Carbon\Carbon::parse($po->ordered_at)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ ucfirst($po->status) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td colspan="4" style="border: 1px solid #000000; text-align: right;">TOTAL:</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $totalPrice }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $totalPpn }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $grandTotal }}</td>
            <td colspan="2" style="border: 1px solid #000000;"></td>
        </tr>
    </tfoot>
</table>
