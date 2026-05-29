@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Detail Purchase Order</h2>
        <div class="d-flex gap-2 flex-wrap">
            <button onclick="window.open('/purchase-orders/{{ $po->id }}/print', '_blank')" class="btn btn-success">
                🖨️ Cetak PO
            </button>
            <a href="{{ route('purchase-orders.pdf', $po->id) }}" class="btn btn-danger" target="_blank">
                ⬇️ Download PDF
            </a>

            @if($po->status === 'pending')
            <form action="{{ route('purchase-orders.terima', $po->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi barang diterima? Stok akan diperbarui.')">
                    ✅ Terima Barang
                </button>
            </form>
            @endif

            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">← Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Info PO --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white fw-semibold">Info PO</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">No. PO</td>
                            <td>
                                @if($po->po_number)
                                    <span class="badge bg-dark">{{ $po->po_number }}</span>
                                @else
                                    <span class="text-secondary">- (belum dicetak)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Supplier</td>
                            <td><strong>{{ $po->supplier_name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ $po->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat oleh</td>
                            <td>{{ $po->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>{{ \Carbon\Carbon::parse($po->ordered_at)->translatedFormat('d F Y, H:i') }}</td>
                        </tr>
                        @if($po->catatan)
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td>{{ $po->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white fw-semibold">Info Pembayaran</div>
                <div class="card-body">
                    @php
                        $ppnAmount = ($po->use_ppn ?? true) ? round($po->total_price * 0.11) : 0;
                    @endphp
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Status</td>
                            <td>
                                @if($po->status === 'diterima')
                                    <span class="badge bg-success fs-6">✅ Diterima</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">⏳ Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">PPN</td>
                            <td>{{ ($po->use_ppn ?? true) ? 'Ya (11%)' : 'Tidak' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">DPP</td>
                            <td>Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @if($po->use_ppn ?? true)
                        <tr>
                            <td class="text-muted">PPN 11%</td>
                            <td>Rp {{ number_format($ppnAmount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="fw-bold">
                            <td class="text-muted">Total</td>
                            <td class="text-danger fs-5">Rp {{ number_format($po->total_price + $ppnAmount, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Items --}}
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">Item yang Dipesan</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Kode Barang</th>
                            <th>Nama Produk</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">SAT.</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($po->items as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="text-center"><code>{{ $item->product->kode_barang ?? '-' }}</code></td>
                            <td>
                                {{ $item->product->name ?? 'Produk tidak ditemukan' }}
                                @if($item->nama_varian)
                                    <br><span class="badge bg-primary">{{ $item->nama_varian }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->product->satuan ?? 'Pcs' }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-semibold">DPP</td>
                            <td class="text-end">Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @if($po->use_ppn ?? true)
                        <tr>
                            <td colspan="6" class="text-end fw-semibold">PPN 11%</td>
                            <td class="text-end">Rp {{ number_format($ppnAmount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="fw-bold table-warning">
                            <td colspan="6" class="text-end">TOTAL</td>
                            <td class="text-end text-danger">Rp {{ number_format($po->total_price + $ppnAmount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
