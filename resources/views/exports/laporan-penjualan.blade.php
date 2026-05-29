<table>
    <thead>
        <tr>
            <th colspan="10" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN PENJUALAN - PT MCA</th>
        </tr>
        @if(($filters['bulan_dari'] ?? null) || ($filters['bulan_sampai'] ?? null) || ($filters['tahun'] ?? null) || ($filters['customer_name'] ?? null))
        <tr>
            <th colspan="10" style="text-align: center; font-style: italic;">
                Filter: 
                @if(($filters['bulan_dari'] ?? null) && ($filters['bulan_sampai'] ?? null))
                    Bulan {{ $filters['bulan_dari'] }} - {{ $filters['bulan_sampai'] }}
                @elseif($filters['bulan_dari'] ?? null)
                    Dari Bulan {{ $filters['bulan_dari'] }}
                @elseif($filters['bulan_sampai'] ?? null)
                    Sampai Bulan {{ $filters['bulan_sampai'] }}
                @endif
                @if($filters['tahun'] ?? null) Tahun {{ $filters['tahun'] }} @endif
                @if($filters['customer_name'] ?? null) Pelanggan: {{ $filters['customer_name'] }} @endif
            </th>
        </tr>
        @endif
        <tr></tr> {{-- Baris Kosong --}}
        <tr style="background-color: #1a1a2e; color: #ffffff; font-weight: bold;">
            <th style="border: 1px solid #000000; text-align: center;">No</th>
            <th style="border: 1px solid #000000; text-align: center;">ID Pesanan</th>
            <th style="border: 1px solid #000000;">Pelanggan</th>
            <th style="border: 1px solid #000000;">Penjual / Operator</th>
            <th style="border: 1px solid #000000; text-align: right;">DPP (Harga Asli)</th>
            <th style="border: 1px solid #000000; text-align: right;">PPN (11%)</th>
            <th style="border: 1px solid #000000; text-align: right;">Total</th>
            <th style="border: 1px solid #000000; text-align: center;">Tanggal</th>
            <th style="border: 1px solid #000000; text-align: center;">Status Pembayaran</th>
            <th style="border: 1px solid #000000; text-align: center;">No. Invoice</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        @php
            $ppn = ($order->use_ppn ?? true) ? round($order->total_price * 0.11) : 0;
        @endphp
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #000000; text-align: center;">#{{ $order->id }}</td>
            <td style="border: 1px solid #000000;">{{ $order->customer_name ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $order->user->name ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $order->total_price }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $ppn }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $order->total_price + $ppn }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ \Carbon\Carbon::parse($order->ordered_at)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ ucfirst($order->status_pembayaran) }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $order->invoice_number ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td colspan="4" style="border: 1px solid #000000; text-align: right;">TOTAL:</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $totalPrice }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $totalPpn }}</td>
            <td style="border: 1px solid #000000; text-align: right;">{{ $grandTotal }}</td>
            <td colspan="3" style="border: 1px solid #000000;"></td>
        </tr>
    </tfoot>
</table>
