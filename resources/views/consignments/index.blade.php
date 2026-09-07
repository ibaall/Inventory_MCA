@extends('layouts.app')

@section('title', 'Daftar Konsinyasi')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Konsinyasi</h2>
        <a href="{{ route('consignments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Konsinyasi Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($consignments->isEmpty())
                <div class="alert alert-info text-center mb-0">Belum ada data konsinyasi.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Tanggal</th>
                                <th>No. Surat Jalan</th>
                                <th>Customer</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consignments as $c)
                            <tr>
                                <td class="text-center">{{ $consignments->firstItem() + $loop->index }}</td>
                                <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y') }}</td>
                                <td><span class="badge bg-secondary">{{ $c->surat_jalan_number }}</span></td>
                                <td>{{ $c->customer_name }}</td>
                                <td class="text-center">
                                    @if($c->status === 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('consignments.show', $c->id) }}" class="btn btn-sm btn-info text-white" title="Detail & Input Pemakaian">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('consignments.print', $c->id) }}" class="btn btn-sm btn-secondary" target="_blank" title="Cetak Surat Jalan">
                                        <i class="bi bi-printer"></i> SJ
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $consignments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
