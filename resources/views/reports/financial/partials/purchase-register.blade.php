{{-- Register Pembelian - Detail barang per PO --}}
@php
    $data = $reportData;
    $items = $data['items'] ?? collect();
    $grandTotal = $data['grand_total'] ?? 0;
@endphp

@if($items->isEmpty())
    <div class="alert alert-warning m-3">
        <i class="bi bi-exclamation-triangle"></i>
        Tidak ada data pembelian pada periode ini.
        @if($filters['supplier_name'] ?? null)
            <br>Supplier: <strong>{{ $filters['supplier_name'] }}</strong>
        @endif
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width:35px;">No</th>
                    <th style="width:85px;">Tanggal</th>
                    <th>Supplier</th>
                    <th style="width:140px;">No. PO / Invoice</th>
                    <th>Nama Barang</th>
                    <th class="text-center" style="width:45px;">Qty</th>
                    <th class="text-center" style="width:55px;">Satuan</th>
                    <th class="text-end" style="width:100px;">Harga Asli</th>
                    <th class="text-center" style="width:55px;">Diskon</th>
                    <th class="text-end" style="width:100px;">Harga</th>
                    <th class="text-end" style="width:90px;">PPN</th>
                    <th class="text-end" style="width:110px;">Total Barang</th>
                    <th class="text-end" style="width:120px;">Total Invoice</th>
                </tr>
            </thead>
            <tbody>
                @php $prevPoId = null; @endphp
                @foreach($items as $item)
                    @php
                        $isNewPo = $item['po_id'] !== $prevPoId;
                        $prevPoId = $item['po_id'];
                        $hasDiscount = ($item['discount_percent'] ?? 0) > 0;
                    @endphp
                    <tr class="{{ $isNewPo ? 'border-top border-2' : '' }}">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $isNewPo ? $item['date']->format('d/m/Y') : '' }}</td>
                        <td>{{ $isNewPo ? $item['supplier_name'] : '' }}</td>
                        <td>
                            @if($isNewPo)
                                <span class="badge bg-secondary">{{ $item['nomor'] }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $item['nama_barang'] }}
                            @if($item['nama_varian'] ?? null)
                                <small class="text-muted">({{ $item['nama_varian'] }})</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item['qty'] }}</td>
                        <td class="text-center">{{ $item['satuan'] }}</td>
                        <td class="text-end">Rp {{ number_format($item['harga_asli'] ?? $item['harga'], 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($hasDiscount)
                                <span class="badge bg-warning text-dark">{{ $item['discount_percent'] }}%</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="text-end">
                            @if($item['ppn'] > 0)
                                Rp {{ number_format($item['ppn'], 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($item['total_barang'], 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold">
                            @if($isNewPo)
                                Rp {{ number_format($item['total_invoice'], 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr class="fw-bold">
                    <td colspan="12" class="text-end">GRAND TOTAL:</td>
                    <td class="text-end text-danger">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif
