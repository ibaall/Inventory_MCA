@extends('layouts.app')
@section('title', 'Master Data - No. Perkiraan')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-journal-bookmark"></i> Daftar No. Perkiraan</h2>
        @if(auth()->user()->role === 'owner')
        <a href="{{ route('no-perkiraan.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><small>{{ session('success') }}</small><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Cari kode atau nama perkiraan...">
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-search"></i> Cari</button></div>
                <div class="col-md-2"><a href="{{ route('no-perkiraan.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x-circle"></i> Reset</a></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold"><i class="bi bi-journal-bookmark"></i> No. Perkiraan</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:15%">Kode</th>
                            <th>Nama Perkiraan</th>
                            @if(auth()->user()->role === 'owner')
                            <th style="width:15%" class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $p)
                            <tr>
                                <td>{{ $data->firstItem() + $i }}</td>
                                <td><span class="badge bg-primary">{{ $p->kode_perkiraan }}</span></td>
                                <td>{{ $p->nama_perkiraan }}</td>
                                @if(auth()->user()->role === 'owner')
                                <td class="text-center">
                                    <a href="{{ route('no-perkiraan.edit', $p->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('no-perkiraan.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus No. Perkiraan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ auth()->user()->role === 'owner' ? 4 : 3 }}" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($data->hasPages())<div class="card-footer">{{ $data->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
