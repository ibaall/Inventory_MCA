@extends('layouts.app')

@section('title', 'Purchase Order')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Daftar Purchase Order</h2>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat PO Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($purchaseOrders->isEmpty())
        <div class="alert alert-info text-center">
            <h5>Belum ada Purchase Order.</h5>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">No</th>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Dibuat oleh</th>
                        <th class="text-end">Total (DPP)</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrders as $po)
                    <tr>
                        <td class="text-center">{{ $purchaseOrders->firstItem() + $loop->index }}</td>
                        <td>
                            @if($po->po_number)
                                <span class="badge bg-secondary">{{ $po->po_number }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $po->supplier_name }}</td>
                        <td>{{ $po->user->name ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($po->ordered_at)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($po->status === 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('purchase-orders.pdf', $po->id) }}" class="btn btn-danger btn-sm">PDF</a>
                            @if($po->status === 'pending')
                            <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus PO ini?')">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Total Halaman Ini:</td>
                        <td class="text-end">Rp {{ number_format($purchaseOrders->sum('total_price'), 0, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $purchaseOrders->links() }}
        </div>
    @endif
</div>
@endsection
