<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN RINGKASAN BULANAN - PT MCA</th>
        </tr>
        @if(($filters['bulan_dari'] ?? null) || ($filters['bulan_sampai'] ?? null) || ($filters['tahun'] ?? null))
        <tr>
            <th colspan="7" style="text-align: center; font-style: italic;">
                Filter: 
                @if(($filters['bulan_dari'] ?? null) && ($filters['bulan_sampai'] ?? null))
                    Bulan {{ $filters['bulan_dari'] }} - {{ $filters['bulan_sampai'] }}
                @elseif($filters['bulan_dari'] ?? null)
                    Dari Bulan {{ $filters['bulan_dari'] }}
                @elseif($filters['bulan_sampai'] ?? null)
                    Sampai Bulan {{ $filters['bulan_sampai'] }}
                @endif
                @if($filters['tahun'] ?? null) Tahun {{ $filters['tahun'] }} @endif
            </th>
        </tr>
        @endif
        <tr></tr> {{-- Baris Kosong --}}
        
        {{-- Tabel Penjualan per Bulan --}}
        <tr style="background-color: #198754; color: #ffffff; font-weight: bold;">
            <th colspan="3" style="border: 1px solid #000000; text-align: center;">PENJUALAN PER BULAN</th>
            <th></th> {{-- Spacer --}}
            <th colspan="3" style="background-color: #dc3545; color: #ffffff; border: 1px solid #000000; text-align: center;">PEMBELIAN (PO) PER BULAN</th>
        </tr>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <th style="border: 1px solid #000000;">Bulan</th>
            <th style="border: 1px solid #000000; text-align: center;">Jumlah Transaksi</th>
            <th style="border: 1px solid #000000; text-align: right;">Total Penjualan</th>
            <th></th> {{-- Spacer --}}
            <th style="border: 1px solid #000000;">Bulan</th>
            <th style="border: 1px solid #000000; text-align: center;">Jumlah Transaksi</th>
            <th style="border: 1px solid #000000; text-align: right;">Total Pembelian</th>
        </tr>
    </thead>
    <tbody>
        @php
            $maxRows = max(count($laporanPenjualan), count($laporanPembelian));
        @endphp
        @for($i = 0; $i < $maxRows; $i++)
        @php
            $sale = $laporanPenjualan[$i] ?? null;
            $buy = $laporanPembelian[$i] ?? null;
        @endphp
        <tr>
            {{-- Penjualan --}}
            @if($sale)
                <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::create()->month($sale->bulan)->translatedFormat('F') }} {{ $sale->tahun }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $sale->jumlah_transaksi }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $sale->total }}</td>
            @else
                <td style="border: 1px solid #000000; text-align: center;">-</td>
                <td style="border: 1px solid #000000; text-align: center;">-</td>
                <td style="border: 1px solid #000000; text-align: center;">-</td>
            @endif

            {{-- Spacer --}}
            <td></td>

            {{-- Pembelian --}}
            @if($buy)
                <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::create()->month($buy->bulan)->translatedFormat('F') }} {{ $buy->tahun }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $buy->jumlah_transaksi }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $buy->total }}</td>
            @else
                <td style="border: 1px solid #000000; text-align: center;">-</td>
                <td style="border: 1px solid #000000; text-align: center;">-</td>
                <td style="border: 1px solid #000000; text-align: center;">-</td>
            @endif
        </tr>
        @endfor
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td style="border: 1px solid #000000;">TOTAL:</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $laporanPenjualan->sum('jumlah_transaksi') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $laporanPenjualan->sum('total') }}</td>
            <td></td>
            <td style="border: 1px solid #000000;">TOTAL:</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $laporanPembelian->sum('jumlah_transaksi') }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $laporanPembelian->sum('total') }}</td>
        </tr>
    </tfoot>
</table>
