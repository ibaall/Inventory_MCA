@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Detail Pesanan</h2>
        <div class="d-flex gap-2 flex-wrap">
            {{-- Invoice --}}
            <button onclick="cetakInvoice({{ $order->id }})" class="btn btn-success">
                🖨️ Cetak Invoice
            </button>
            <a href="{{ route('orders.invoice.pdf', $order->id) }}" class="btn btn-danger" target="_blank">
                ⬇️ Download PDF
            </a>

            {{-- Surat Jalan --}}
            <button onclick="cetakSuratJalan({{ $order->id }})" class="btn btn-warning">
                📄 Cetak Surat Jalan
            </button>
            <a href="{{ route('orders.sj.pdf', $order->id) }}" class="btn btn-outline-warning" target="_blank">
                ⬇️ Download SJ
            </a>

            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">← Kembali</a>
        </div>
    </div>

    {{-- Info Pesanan --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white fw-semibold">Info Pelanggan</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">ID Pesanan</td>
                            <td><strong>#{{ $order->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Pelanggan</td>
                            <td><strong>{{ $order->customer_name ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Penjual</td>
                            <td>{{ $order->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Pesanan</td>
                            <td>{{ \Carbon\Carbon::parse($order->ordered_at)->translatedFormat('d F Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Invoice</td>
                            <td>
                                @if($order->invoice_number)
                                    <span class="badge bg-dark">{{ $order->invoice_number }}</span>
                                @else
                                    <span class="text-secondary">- (belum dicetak)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Surat Jalan</td>
                            <td>
                                @if($order->surat_jalan_number)
                                    <span class="badge bg-warning text-dark">{{ $order->surat_jalan_number }}</span>
                                @else
                                    <span class="text-secondary">- (belum dicetak)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Jatuh Tempo</td>
                            <td>
                                @if($order->tanggal_jatuh_tempo)
                                    {{ \Carbon\Carbon::parse($order->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Operasi</td>
                            <td>
                                @if($order->tanggal_operasi)
                                    {{ \Carbon\Carbon::parse($order->tanggal_operasi)->translatedFormat('d F Y') }}
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Pasien</td>
                            <td>{{ $order->nama_pasien ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Operator</td>
                            <td>{{ $order->operator ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ $order->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white fw-semibold">Info Pembayaran</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Status</td>
                            <td>
                                @if($order->status_pembayaran === 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Dibayar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($order->metode_pembayaran) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">DPP</td>
                            <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->use_ppn ?? true)
                        <tr>
                            <td class="text-muted">PPN 11%</td>
                            <td>Rp {{ number_format(round($order->total_price * 0.11), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="fw-bold">
                            <td class="text-muted">Total Bayar</td>
                            <td class="text-danger fs-5">
                                @php
                                    $ppnAmount = ($order->use_ppn ?? true) ? round($order->total_price * 0.11) : 0;
                                @endphp
                                Rp {{ number_format($order->total_price + $ppnAmount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Item --}}
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">Produk yang Dibeli</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">SAT.</th>
                            <th>Nama Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">Pcs</td>
                            <td>
                                {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                                @if(!empty($item->nama_varian))
                                    <br><span class="badge bg-primary">{{ $item->nama_varian }}</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-semibold">DPP</td>
                            <td class="text-end">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->use_ppn ?? true)
                        <tr>
                            <td colspan="5" class="text-end fw-semibold">PPN 11%</td>
                            <td class="text-end">Rp {{ number_format(round($order->total_price * 0.11), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="fw-bold table-warning">
                            <td colspan="5" class="text-end">JUMLAH</td>
                            <td class="text-end text-danger">
                                Rp {{ number_format($order->total_price + $ppnAmount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
    function cetakInvoice(orderId) {
        window.open(`/orders/${orderId}/invoice/print`, '_blank');
    }
    function cetakSuratJalan(orderId) {
        window.open(`/orders/${orderId}/surat-jalan/print`, '_blank');
    }
</script>
@endsection
