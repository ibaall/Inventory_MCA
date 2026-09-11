@extends('layouts.app')

@section('title', 'Master Data - Supplier, Customer & Produk')

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
                                    <th style="width:20%">Nama</th>
                                    <th style="width:25%">Alamat</th>
                                    <th style="width:12%">Telepon</th>
                                    <th style="width:18%">Rekening</th>
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
                                        <td><small>{{ $supplier->rekening ?? '-' }}</small></td>
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
                                                        <div class="mb-3">
                                                            <label class="form-label">No. Rekening</label>
                                                            <input type="text" name="rekening" class="form-control" value="{{ $supplier->rekening }}" placeholder="No. rekening bank (opsional)">
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
                                        <td colspan="6" class="text-center text-muted py-3">Belum ada data supplier.</td>
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

    {{-- ===== PRODUK (FULL WIDTH) ===== --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-box-seam"></i> Daftar Produk</span>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="width:4%">No</th>
                                    <th style="width:6%">Gambar</th>
                                    <th style="width:10%">Kode</th>
                                    <th style="width:18%">Nama Produk</th>
                                    <th style="width:12%">Vendor</th>
                                    <th style="width:10%">Kategori</th>
                                    <th style="width:8%">Stok</th>
                                    <th style="width:6%">Satuan</th>
                                    <th style="width:12%">Harga</th>
                                    <th style="width:14%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $i => $product)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="text-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="img-thumbnail"
                                                     loading="lazy"
                                                     style="width:50px; height:50px; object-fit:cover;">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->kode_barang)
                                                <span class="badge bg-secondary font-monospace">{{ $product->kode_barang }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $product->name }}</td>
                                        <td><small>{{ $product->vendor ?? '-' }}</small></td>
                                        <td>
                                            @if($product->category)
                                                <span class="badge bg-info text-dark">{{ $product->category }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_null($product->stock))
                                                <span class="text-muted">—</span>
                                            @elseif($product->stock == 0)
                                                <span class="badge bg-danger">Habis</span>
                                            @elseif($product->stock < 6)
                                                <span class="badge bg-warning text-dark">{{ $product->stock }} (Rendah)</span>
                                            @else
                                                <span class="badge bg-success">{{ $product->stock }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $product->satuan ?? 'Pcs' }}</span>
                                        </td>
                                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editProductModal-{{ $product->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('master-data.product.destroy', $product) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin hapus produk {{ addslashes($product->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    {{-- Modal Edit Produk --}}
                                    <div class="modal fade" id="editProductModal-{{ $product->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('master-data.product.update', $product) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Edit Produk</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Kode Barang</label>
                                                            <input type="text" name="kode_barang" class="form-control" value="{{ $product->kode_barang }}" placeholder="Contoh: ALK-001">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Vendor</label>
                                                            <select class="form-select" name="vendor">
                                                                <option value="">-- Pilih Vendor --</option>
                                                                @foreach($suppliers as $supplier)
                                                                    <option value="{{ $supplier->name }}" {{ $product->vendor == $supplier->name ? 'selected' : '' }}>
                                                                        {{ $supplier->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Kategori</label>
                                                            <input type="text" name="category" class="form-control" value="{{ $product->category }}" placeholder="Contoh: Alat Diagnostik">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Stok</label>
                                                            <select class="form-select mb-2 edit-stock-type" data-product-id="{{ $product->id }}">
                                                                <option value="none" {{ is_null($product->stock) ? 'selected' : '' }}>NONE</option>
                                                                <option value="input" {{ !is_null($product->stock) ? 'selected' : '' }}>Isi Stok</option>
                                                            </select>
                                                            <input type="number" name="stock" class="form-control edit-stock-input-{{ $product->id }}" value="{{ $product->stock ?? '' }}" min="0" placeholder="Masukkan jumlah stok" style="{{ is_null($product->stock) ? 'display:none' : '' }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="satuan" required>
                                                                @foreach($satuanList as $sat)
                                                                    <option value="{{ $sat }}"
                                                                        {{ ($product->satuan ?? 'Pcs') === $sat ? 'selected' : '' }}>
                                                                        {{ $sat }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="number" name="price" class="form-control" value="{{ $product->price }}" min="0" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Gambar Produk</label>
                                                            @if($product->image)
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                                         class="img-thumbnail" style="height:80px; object-fit:cover;">
                                                                    <small class="text-muted d-block">Gambar saat ini</small>
                                                                </div>
                                                            @endif
                                                            <input type="file" name="image" class="form-control" accept="image/*">
                                                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
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
                                        <td colspan="10" class="text-center text-muted py-3">Belum ada data produk.</td>
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
                    <div class="mb-3">
                        <label class="form-label">No. Rekening</label>
                        <input type="text" name="rekening" class="form-control" placeholder="No. rekening bank (opsional)">
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

{{-- Modal Tambah Produk --}}
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('master-data.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Produk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: ALK-001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama produk">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vendor</label>
                            <select class="form-select" name="vendor">
                                <option value="">-- Pilih Vendor --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" class="form-control" placeholder="Contoh: Alat Diagnostik">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok</label>
                            <select class="form-select mb-2" id="addStockType">
                                <option value="none" selected>NONE</option>
                                <option value="input">Isi Stok</option>
                            </select>
                            <input type="number" name="stock" id="addStockInput" class="form-control" min="0" placeholder="Masukkan jumlah stok" style="display:none">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select class="form-select" name="satuan" required>
                                @foreach($satuanList as $sat)
                                    <option value="{{ $sat }}" {{ $sat === 'Pcs' ? 'selected' : '' }}>{{ $sat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price" class="form-control" min="0" required placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Toggle stock input visibility for Tambah Produk modal
    document.getElementById('addStockType').addEventListener('change', function() {
        const stockInput = document.getElementById('addStockInput');
        if (this.value === 'none') {
            stockInput.style.display = 'none';
            stockInput.value = '';
            stockInput.removeAttribute('name');
        } else {
            stockInput.style.display = '';
            stockInput.setAttribute('name', 'stock');
            stockInput.value = '0';
            stockInput.focus();
        }
    });
    // Initialize: remove name attribute when NONE is default
    (function() {
        const sel = document.getElementById('addStockType');
        const inp = document.getElementById('addStockInput');
        if (sel.value === 'none') {
            inp.removeAttribute('name');
        }
    })();

    // Toggle stock input visibility for Edit Produk modals
    document.querySelectorAll('.edit-stock-type').forEach(function(select) {
        select.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const stockInput = document.querySelector('.edit-stock-input-' + productId);
            if (this.value === 'none') {
                stockInput.style.display = 'none';
                stockInput.value = '';
                stockInput.removeAttribute('name');
            } else {
                stockInput.style.display = '';
                stockInput.setAttribute('name', 'stock');
                if (stockInput.value === '') stockInput.value = '0';
                stockInput.focus();
            }
        });
        // Initialize: remove name attribute if NONE is selected
        if (select.value === 'none') {
            const productId = select.getAttribute('data-product-id');
            const stockInput = document.querySelector('.edit-stock-input-' + productId);
            stockInput.removeAttribute('name');
        }
    });
</script>
@endsection
