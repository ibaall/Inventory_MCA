@extends('layouts.app')

@section('title', 'Master Data - Supplier & Customer')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-database"></i> Master Data</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- ===== SUPPLIER ===== --}}
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-truck"></i> Daftar Supplier</span>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:25%">Nama</th>
                                    <th style="width:35%">Alamat</th>
                                    <th style="width:15%">Telepon</th>
                                    <th style="width:20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $i => $supplier)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $supplier->name }}</td>
                                        <td><small>{{ $supplier->alamat ?? '-' }}</small></td>
                                        <td><small>{{ $supplier->telepon ?? '-' }}</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editSupplierModal-{{ $supplier->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('master-data.supplier.destroy', $supplier) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin hapus supplier {{ $supplier->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    {{-- Modal Edit Supplier --}}
                                    <div class="modal fade" id="editSupplierModal-{{ $supplier->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('master-data.supplier.update', $supplier) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Edit Supplier</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Alamat</label>
                                                            <textarea name="alamat" class="form-control" rows="2">{{ $supplier->alamat }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Telepon</label>
                                                            <input type="text" name="telepon" class="form-control" value="{{ $supplier->telepon }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada data supplier.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== CUSTOMER ===== --}}
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people"></i> Daftar Customer</span>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        <i class="bi bi-plus-circle"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:25%">Nama</th>
                                    <th style="width:35%">Alamat</th>
                                    <th style="width:15%">Telepon</th>
                                    <th style="width:20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $i => $customer)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $customer->name }}</td>
                                        <td><small>{{ $customer->alamat ?? '-' }}</small></td>
                                        <td><small>{{ $customer->telepon ?? '-' }}</small></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editCustomerModal-{{ $customer->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('master-data.customer.destroy', $customer) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin hapus customer {{ $customer->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    {{-- Modal Edit Customer --}}
                                    <div class="modal fade" id="editCustomerModal-{{ $customer->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('master-data.customer.update', $customer) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Edit Customer</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Alamat</label>
                                                            <textarea name="alamat" class="form-control" rows="2">{{ $customer->alamat }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Telepon</label>
                                                            <input type="text" name="telepon" class="form-control" value="{{ $customer->telepon }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada data customer.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Supplier --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('master-data.supplier.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: AMSA">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat supplier (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" placeholder="No. telepon (opsional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Customer --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('master-data.customer.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: RS Santosa Bandung">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat customer (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" placeholder="No. telepon (opsional)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
