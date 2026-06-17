@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Produk</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(auth()->check() && in_array(auth()->user()->role, ['owner', 'admin', 'marketing']))
        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Tambah Produk</a>
    @endif

    {{-- ===== PANEL PENCARIAN & FILTER ===== --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light fw-semibold">
            🔍 Pencarian & Filter Produk
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label class="form-label">Cari Nama Produk</label>
                        <input type="text" name="search" id="searchInput"
                               class="form-control" placeholder="Ketik nama produk..."
                               value="{{ request('search') }}" autocomplete="off">
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control"
                               placeholder="Contoh: ALK-001"
                               value="{{ request('kode_barang') }}" autocomplete="off">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label">Vendor</label>
                        <select name="vendor" class="form-select filter-select">
                            <option value="">Semua Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor }}"
                                    {{ request('vendor') === $vendor ? 'selected' : '' }}>
                                    {{ $vendor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select filter-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}"
                                    {{ request('category') === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label">Status Stok</label>
                        <select name="stock_status" class="form-select filter-select">
                            <option value="">Semua</option>
                            <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="low"       {{ request('stock_status') === 'low'       ? 'selected' : '' }}>Stok Rendah (&lt;6)</option>
                            <option value="empty"     {{ request('stock_status') === 'empty'     ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2 text-muted small">
        Menampilkan <strong>{{ $products->firstItem() ?? 0 }}</strong>–<strong>{{ $products->lastItem() ?? 0 }}</strong> dari <strong>{{ $products->total() }}</strong> produk
        @if(request()->hasAny(['search','kode_barang','vendor','category','stock_status']))
            &nbsp;–&nbsp;
            <a href="{{ route('products.index') }}" class="text-danger text-decoration-none">✕ Hapus semua filter</a>
        @endif
    </div>

    {{-- ===== TABEL PRODUK ===== --}}
    @if($products->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Kode Barang</th>
                    <th>Nama Produk</th>
                    <th>Vendor</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $index => $product)
                <tr>
                    <td>{{ $products->firstItem() + $index }}</td>

                    <td class="text-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="img-thumbnail"
                                 loading="lazy"
                                 style="width:70px; height:70px; object-fit:cover;">
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

                    <td>
                        @if(request('search'))
                            {!! preg_replace(
                                '/(' . preg_quote(request('search'), '/') . ')/i',
                                '<mark>$1</mark>',
                                e($product->name)
                            ) !!}
                        @else
                            {{ $product->name }}
                        @endif
                    </td>

                    <td>{{ $product->vendor ?? '-' }}</td>

                    <td>
                        @if($product->category)
                            <span class="badge bg-info text-dark">{{ $product->category }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>
                        @if($product->stock == 0)
                            <span class="badge bg-danger">Habis</span>
                        @elseif($product->stock < 6)
                            <span class="badge bg-warning text-dark">{{ $product->stock }} (Rendah)</span>
                        @else
                            <span class="badge bg-success">{{ $product->stock }}</span>
                        @endif
                    </td>
                    {{-- Setelah kolom stok --}}
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $product->satuan ?? 'Pcs' }}</span>
                    </td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>

                    <td>
                        {{-- Tombol Edit (owner/admin/marketing) --}}
                        @if(auth()->check() && in_array(auth()->user()->role, ['owner', 'admin', 'marketing']))
                        <button class="btn btn-warning btn-sm mb-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $product->id }}">
                            Edit
                        </button>
                        @endif

                        {{-- Tambah ke Keranjang (tidak untuk marketing) --}}
                        @if(auth()->user()->role !== 'marketing')
                        @if($product->stock > 0)
                            @if($product->variants->count() > 0)
                                <button type="button" class="btn btn-success btn-sm mt-1"
                                        onclick="openVariantModal({{ $product->id }})">
                                    + Keranjang
                                </button>
                            @else
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="input-group input-group-sm mt-1" style="max-width:160px;">
                                        <input type="number" name="quantity" class="form-control"
                                               value="1" min="1" max="{{ $product->stock }}" required>
                                        <button type="submit" class="btn btn-success btn-sm">+ Keranjang</button>
                                    </div>
                                </form>
                            @endif
                        @else
                            <span class="badge bg-danger mt-1 d-block">Stok Habis</span>
                        @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $products->links() }}
    </div>

    @else
        <div class="alert alert-warning text-center">
            <h5>Tidak ada produk yang ditemukan.</h5>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mt-2">Reset Filter</a>
        </div>
    @endif

    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary mt-2">Lihat Keranjang</a>
</div>

{{-- ===== MODAL EDIT — DI LUAR TABEL ===== --}}
@if(auth()->check() && in_array(auth()->user()->role, ['owner', 'admin', 'marketing']))
    @foreach ($products as $product)
    <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('products.update', $product->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" name="kode_barang"
                                   value="{{ $product->kode_barang }}" placeholder="Contoh: ALK-001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="name"
                                   value="{{ $product->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Vendor</label>
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
                            <input type="text" class="form-control" name="category"
                                   value="{{ $product->category }}" placeholder="Contoh: Alat Diagnostik">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" name="stock"
                                   value="{{ $product->stock }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan</label>
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
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" name="price"
                                   value="{{ $product->price }}" required>
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
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </form>

                    {{-- Tombol Hapus Barang --}}
                    <hr class="my-3">
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus produk {{ addslashes($product->name) }}? Data tidak dapat dikembalikan!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            🗑️ Hapus Barang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- ===== MODAL VARIAN — DI LUAR TABEL & FOREACH UTAMA ===== --}}
@foreach($products as $product)
    @if($product->variants->count() > 0)
    <div class="modal fade" id="variantModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0">{{ $product->name }}</h5>
                        <small class="text-muted">
                            @if($product->kode_barang)
                                <span class="badge bg-secondary font-monospace">{{ $product->kode_barang }}</span>
                            @endif
                            @if($product->vendor) &nbsp;{{ $product->vendor }} @endif
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3 mb-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="img-thumbnail" loading="lazy" style="width:80px; height:80px; object-fit:cover;">
                        @endif
                        <div>
                            <div class="text-danger fw-bold fs-5">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>
                            <div class="text-muted small" id="totalStokLabel{{ $product->id }}">Total Stok: {{ $product->stock }}</div>
                            @if($product->category)
                                <span class="badge bg-info text-dark">{{ $product->category }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#tabPilih{{ $product->id }}" type="button">🛒 Pilih Varian</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#tabEdit{{ $product->id }}" type="button">✏️ Edit Varian</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- TAB 1: Pilih Varian (original) --}}
                        <div class="tab-pane fade show active" id="tabPilih{{ $product->id }}">
                            <p class="fw-semibold mb-2">Pilih Varian:</p>
                            <div class="row g-2" id="variantOptions{{ $product->id }}">
                                @foreach($product->variants as $variant)
                                    <div class="col-6">
                                        <div class="variant-option border rounded p-2 d-flex align-items-center gap-2
                                                    {{ $variant->stock == 0 ? 'opacity-50' : '' }}"
                                             style="cursor:{{ $variant->stock > 0 ? 'pointer' : 'not-allowed' }};"
                                             onclick="{{ $variant->stock > 0 ? "selectVariant(this, {$variant->id}, {$product->id}, {$variant->stock})" : '' }}">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                     style="width:36px; height:36px; object-fit:cover; border-radius:4px;">
                                            @endif
                                            <div class="flex-grow-1">
                                                <div class="small fw-semibold">{{ $variant->nama_varian }}</div>
                                                <div class="small text-muted">
                                                    Stok: {{ $variant->stock }}
                                                    @if($variant->stock == 0)
                                                        <span class="badge bg-danger">Habis</span>
                                                    @endif
                                                </div>
                                                @if($variant->price)
                                                    <div class="small text-danger">
                                                        Rp {{ number_format($variant->price, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="check-icon d-none text-success fw-bold">✓</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 d-none" id="qtySection{{ $product->id }}">
                                <label class="form-label fw-semibold">Jumlah:</label>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="changeQty({{ $product->id }}, -1)">−</button>
                                    <input type="number" id="qtyInput{{ $product->id }}"
                                           class="form-control form-control-sm text-center"
                                           style="width:70px;" value="1" min="1" readonly>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="changeQty({{ $product->id }}, 1)">+</button>
                                    <span class="text-muted small" id="maxStockLabel{{ $product->id }}"></span>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: Edit Varian --}}
                        <div class="tab-pane fade" id="tabEdit{{ $product->id }}">
                            <div id="variantEditAlert{{ $product->id }}"></div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Varian</th>
                                            <th style="width:100px">Stok</th>
                                            <th style="width:130px">Harga</th>
                                            <th style="width:130px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantEditBody{{ $product->id }}">
                                        @foreach($product->variants as $variant)
                                        <tr id="variantRow{{ $variant->id }}">
                                            <td><input type="text" class="form-control form-control-sm" id="vName{{ $variant->id }}" value="{{ $variant->nama_varian }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" id="vStock{{ $variant->id }}" value="{{ $variant->stock }}" min="0"></td>
                                            <td><input type="number" class="form-control form-control-sm" id="vPrice{{ $variant->id }}" value="{{ $variant->price }}" min="0" placeholder="Opsional"></td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm" onclick="saveVariant({{ $variant->id }}, {{ $product->id }})" title="Simpan">💾</button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteVariant({{ $variant->id }}, {{ $product->id }})" title="Hapus">🗑️</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Tambah varian baru --}}
                            <div class="card card-body bg-light p-2 mt-2">
                                <p class="small fw-semibold mb-2">+ Tambah Varian Baru</p>
                                <div class="row g-2 align-items-end">
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm" id="newVName{{ $product->id }}" placeholder="Nama varian">
                                    </div>
                                    <div class="col-auto" style="width:90px">
                                        <input type="number" class="form-control form-control-sm" id="newVStock{{ $product->id }}" placeholder="Stok" min="0" value="0">
                                    </div>
                                    <div class="col-auto" style="width:120px">
                                        <input type="number" class="form-control form-control-sm" id="newVPrice{{ $product->id }}" placeholder="Harga" min="0">
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-success btn-sm" onclick="addNewVariant({{ $product->id }})">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- end tab-content --}}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="variantForm{{ $product->id }}"
                          action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="variant_id" id="selectedVariantId{{ $product->id }}" value="">
                        <input type="hidden" name="quantity"   id="selectedQty{{ $product->id }}" value="1">
                        <button type="submit" class="btn btn-success"
                                id="btnAddCart{{ $product->id }}" disabled>
                            Masukkan Keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
{{-- ===== END MODAL VARIAN ===== --}}

{{-- ===== SATU BLOK SCRIPT ===== --}}
<script>
    const variantState = {};

    function openVariantModal(productId) {
        variantState[productId] = { variantId: null, maxStock: 1 };
        document.querySelectorAll(`#variantOptions${productId} .variant-option`).forEach(el => {
            el.classList.remove('border-success', 'bg-light');
            el.querySelector('.check-icon')?.classList.add('d-none');
        });
        document.getElementById(`qtySection${productId}`).classList.add('d-none');
        document.getElementById(`qtyInput${productId}`).value = 1;
        document.getElementById(`btnAddCart${productId}`).disabled = true;
        document.getElementById(`selectedVariantId${productId}`).value = '';
        new bootstrap.Modal(document.getElementById(`variantModal${productId}`)).show();
    }

    function selectVariant(el, variantId, productId, maxStock) {
        document.querySelectorAll(`#variantOptions${productId} .variant-option`).forEach(opt => {
            opt.classList.remove('border-success', 'bg-light');
            opt.querySelector('.check-icon')?.classList.add('d-none');
        });
        el.classList.add('border-success', 'bg-light');
        el.querySelector('.check-icon')?.classList.remove('d-none');
        variantState[productId] = { variantId, maxStock };
        document.getElementById(`qtySection${productId}`).classList.remove('d-none');
        const qtyInput = document.getElementById(`qtyInput${productId}`);
        qtyInput.max   = maxStock;
        qtyInput.value = 1;
        document.getElementById(`maxStockLabel${productId}`).textContent     = `Maks: ${maxStock}`;
        document.getElementById(`selectedVariantId${productId}`).value       = variantId;
        document.getElementById(`selectedQty${productId}`).value             = 1;
        document.getElementById(`btnAddCart${productId}`).disabled           = false;
    }

    function changeQty(productId, delta) {
        const input    = document.getElementById(`qtyInput${productId}`);
        const maxStock = variantState[productId]?.maxStock ?? 1;
        let   val      = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        input.value = val;
        document.getElementById(`selectedQty${productId}`).value = val;
    }

    // ===== VARIANT EDIT FUNCTIONS =====
    const csrfToken = '{{ csrf_token() }}';

    function showVariantAlert(productId, message, type = 'success') {
        const el = document.getElementById(`variantEditAlert${productId}`);
        el.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show py-2 small" role="alert">
            ${message}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>`;
        setTimeout(() => { el.innerHTML = ''; }, 3000);
    }

    function saveVariant(variantId, productId) {
        const nama  = document.getElementById(`vName${variantId}`).value.trim();
        const stock = document.getElementById(`vStock${variantId}`).value;
        const price = document.getElementById(`vPrice${variantId}`).value;

        if (!nama) { alert('Nama varian harus diisi!'); return; }

        fetch(`/products/variants/${variantId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ nama_varian: nama, stock: parseInt(stock) || 0, price: price ? parseFloat(price) : null })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showVariantAlert(productId, data.message);
                document.getElementById(`totalStokLabel${productId}`).textContent = 'Total Stok: ' + data.total_stock;
            } else {
                showVariantAlert(productId, 'Gagal menyimpan varian.', 'danger');
            }
        })
        .catch(() => showVariantAlert(productId, 'Terjadi kesalahan.', 'danger'));
    }

    function deleteVariant(variantId, productId) {
        if (!confirm('Yakin ingin menghapus varian ini?')) return;

        fetch(`/products/variants/${variantId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`variantRow${variantId}`)?.remove();
                showVariantAlert(productId, data.message);
                document.getElementById(`totalStokLabel${productId}`).textContent = 'Total Stok: ' + data.total_stock;
            } else {
                showVariantAlert(productId, 'Gagal menghapus varian.', 'danger');
            }
        })
        .catch(() => showVariantAlert(productId, 'Terjadi kesalahan.', 'danger'));
    }

    function addNewVariant(productId) {
        const nama  = document.getElementById(`newVName${productId}`).value.trim();
        const stock = document.getElementById(`newVStock${productId}`).value;
        const price = document.getElementById(`newVPrice${productId}`).value;

        if (!nama) { alert('Nama varian harus diisi!'); return; }

        fetch(`/products/${productId}/variants`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ nama_varian: nama, stock: parseInt(stock) || 0, price: price ? parseFloat(price) : null })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const v = data.variant;
                const tbody = document.getElementById(`variantEditBody${productId}`);
                const tr = document.createElement('tr');
                tr.id = `variantRow${v.id}`;
                tr.innerHTML = `
                    <td><input type="text" class="form-control form-control-sm" id="vName${v.id}" value="${v.nama_varian}"></td>
                    <td><input type="number" class="form-control form-control-sm" id="vStock${v.id}" value="${v.stock}" min="0"></td>
                    <td><input type="number" class="form-control form-control-sm" id="vPrice${v.id}" value="${v.price || ''}" min="0" placeholder="Opsional"></td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm" onclick="saveVariant(${v.id}, ${productId})" title="Simpan">💾</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteVariant(${v.id}, ${productId})" title="Hapus">🗑️</button>
                    </td>`;
                tbody.appendChild(tr);

                // Clear inputs
                document.getElementById(`newVName${productId}`).value = '';
                document.getElementById(`newVStock${productId}`).value = '0';
                document.getElementById(`newVPrice${productId}`).value = '';

                showVariantAlert(productId, data.message);
                document.getElementById(`totalStokLabel${productId}`).textContent = 'Total Stok: ' + data.total_stock;
            } else {
                showVariantAlert(productId, 'Gagal menambah varian.', 'danger');
            }
        })
        .catch(() => showVariantAlert(productId, 'Terjadi kesalahan.', 'danger'));
    }

    // Filter hanya diterapkan saat klik tombol "Terapkan Filter"
    // (tidak ada auto-submit pada dropdown atau input pencarian)
</script>

@endsection
