@extends('layouts.app')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Pesanan</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="alert alert-info text-center">
            <h5>Belum ada pesanan.</h5>
        </div>
    @else
        {{-- Floating action bar for bulk actions --}}
        <div id="bulkActionBar" style="
            display: none;
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 14px 28px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
            align-items: center;
            gap: 14px;
            font-size: 14px;
            animation: slideUp 0.3s ease-out;
        ">
            <span>
                <strong id="selectedCount">0</strong> pesanan dipilih
            </span>
            <button type="button" onclick="openBulkDetail()" class="btn btn-info btn-sm" style="
                padding: 8px 18px;
                border-radius: 10px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            ">
                📋 Detail
            </button>
            <button type="button" onclick="printBulkInvoice()" class="btn btn-success btn-sm" style="
                padding: 8px 18px;
                border-radius: 10px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            ">
                🖨️ Cetak Invoice
            </button>
            <button type="button" onclick="downloadBulkPdf()" class="btn btn-danger btn-sm" style="
                padding: 8px 18px;
                border-radius: 10px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            ">
                ⬇️ Download PDF
            </button>
            <button type="button" onclick="clearSelection()" class="btn btn-outline-light btn-sm" style="
                padding: 8px 16px;
                border-radius: 10px;
                font-weight: 500;
            ">
                ✕ Batal
            </button>
        </div>



        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input"
                                   style="cursor: pointer; width: 18px; height: 18px;"
                                   title="Pilih semua">
                        </th>
                        <th class="text-center">No</th>
                        <th>ID Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Penjual</th>
                        <th class="text-end">Total (DPP)</th>
                        <th class="text-end">Total + PPN</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Metode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $index => $order)
                    @php
                        $ppn   = round($order->total_price * 0.11);
                        $total = $order->total_price + $ppn;
                    @endphp
                    <tr id="row-{{ $order->id }}" class="order-row">
                        <td class="text-center">
                            <input type="checkbox" data-order-id="{{ $order->id }}"
                                   class="form-check-input order-checkbox"
                                   style="cursor: pointer; width: 18px; height: 18px;">
                        </td>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
                        <td>{{ $order->customer_name ?? '-' }}</td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold text-danger">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->ordered_at)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($order->status_pembayaran === 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Dibayar</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">{{ ucfirst($order->metode_pembayaran) }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('orders.show', $order->id) }}"
                               class="btn btn-info btn-sm mb-1">Detail</a>
                            <a href="{{ route('orders.invoice.pdf', $order->id) }}"
                               class="btn btn-danger btn-sm mb-1">PDF</a>
                            <a href="{{ route('orders.edit', $order->id) }}"
                               class="btn btn-warning btn-sm mb-1">Edit</a>
                            <form action="{{ route('orders.destroy', $order->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-1"
                                        onclick="return confirm('Yakin hapus pesanan ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td></td>
                        <td colspan="4" class="text-end">Total Semua Pesanan:</td>
                        <td class="text-end">Rp {{ number_format($orders->sum('total_price'), 0, ',', '.') }}</td>
                        <td class="text-end text-danger">
                            Rp {{ number_format($orders->sum(fn($o) => $o->total_price + round($o->total_price * 0.11)), 0, ',', '.') }}
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    .order-row.selected {
        background-color: rgba(13, 110, 253, 0.08) !important;
    }

    .order-checkbox:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    #selectAll:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const bulkBar = document.getElementById('bulkActionBar');
        const countEl = document.getElementById('selectedCount');

        function updateBulkBar() {
            const checked = document.querySelectorAll('.order-checkbox:checked');
            const count = checked.length;
            countEl.textContent = count;

            if (count > 0) {
                bulkBar.style.display = 'flex';
            } else {
                bulkBar.style.display = 'none';
            }

            // Update row highlight
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                if (cb.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            });

            // Update select all state
            if (count === checkboxes.length && checkboxes.length > 0) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (count > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });
    });

    function getSelectedIds() {
        const checked = document.querySelectorAll('.order-checkbox:checked');
        const ids = [];
        checked.forEach(cb => {
            ids.push(cb.getAttribute('data-order-id'));
        });
        return ids;
    }

    function openBulkDetail() {
        const ids = getSelectedIds();
        if (ids.length === 0) { alert('Pilih minimal 1 pesanan!'); return; }
        window.open('{{ route("orders.bulk.detail") }}?order_ids=' + ids.join(','), '_blank');
    }

    function printBulkInvoice() {
        const ids = getSelectedIds();
        if (ids.length === 0) { alert('Pilih minimal 1 pesanan!'); return; }
        window.open('{{ route("orders.bulk.print") }}?order_ids=' + ids.join(','), '_blank');
    }

    function downloadBulkPdf() {
        const ids = getSelectedIds();
        if (ids.length === 0) { alert('Pilih minimal 1 pesanan!'); return; }
        window.open('{{ route("orders.bulk.pdf") }}?order_ids=' + ids.join(','), '_blank');
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const selectAll = document.getElementById('selectAll');
        checkboxes.forEach(cb => {
            cb.checked = false;
            cb.closest('tr').classList.remove('selected');
        });
        selectAll.checked = false;
        selectAll.indeterminate = false;
        document.getElementById('bulkActionBar').style.display = 'none';
        document.getElementById('selectedCount').textContent = '0';
    }

    function cetakInvoice(orderId) {
        window.open(`/orders/${orderId}/invoice/print`, '_blank');
    }
</script>
@endsection
