@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Produk</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" class="form-control @error('kode_barang') is-invalid @enderror"
                   name="kode_barang" value="{{ old('kode_barang') }}" placeholder="Contoh: ALK-001">
            @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Vendor</label>
            <input type="text" class="form-control" name="vendor"
                   value="{{ old('vendor') }}" placeholder="Contoh: PT. Medika Utama">
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <input type="text" class="form-control" name="category" value="{{ old('category') }}" placeholder="Contoh: Alat Diagnostik">
        </div>

        <div class="mb-3">
            <label class="form-label">Harga Dasar</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" class="form-control" name="price"
                       value="{{ old('price') }}" min="0" required
                       placeholder="Harga default jika tidak ada varian">
            </div>
        </div>

        {{-- ===== SECTION VARIAN ===== --}}
        <div class="card mb-3 border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>📦 Varian Produk <small class="fw-normal">(opsional – contoh: ukuran, warna, tipe)</small></span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="toggleVariant" onchange="toggleVariantSection()">
                    <label class="form-check-label text-white" for="toggleVariant">Aktifkan Varian</label>
                </div>
            </div>
            <div class="card-body d-none" id="variantSection">

                <p class="text-muted small mb-3">
                    Jika produk memiliki varian (ukuran, warna, dll), stok di bawah akan menggunakan total stok varian.
                </p>

                <div id="variantList">
                    {{-- Baris varian akan di-generate JS --}}
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addVariant()">
                    + Tambah Varian
                </button>
            </div>
        </div>

        {{-- Stok manual (tampil jika tidak ada varian) --}}
        <div class="mb-3" id="stockManual">
            <label class="form-label">Stok</label>
            <input type="number" class="form-control" name="stock"
                   value="{{ old('stock') }}" min="0">
            <small class="text-muted">Isi jika tidak menggunakan varian.</small>
        </div>
        <div class="mb-3">
            <label for="satuan" class="form-label">Satuan</label>
            <select class="form-select" id="satuan" name="satuan" required>
                @foreach($satuanList as $sat)
                    <option value="{{ $sat }}" {{ old('satuan', 'Pcs') === $sat ? 'selected' : '' }}>
                        {{ $sat }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Gambar Produk</label>
            <input type="file" class="form-control" name="image"
                   accept="image/*" onchange="previewImage(event)">
            <div class="mt-2">
                <img id="imagePreview" src="#" alt="Preview"
                     class="img-thumbnail d-none" style="max-height:150px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Produk</button>
    </form>
</div>

<script>
    let variantIndex = 0;

    function toggleVariantSection() {
        const section  = document.getElementById('variantSection');
        const manual   = document.getElementById('stockManual');
        const checkbox = document.getElementById('toggleVariant');

        if (checkbox.checked) {
            section.classList.remove('d-none');
            manual.classList.add('d-none');
            if (variantIndex === 0) addVariant(); // otomatis tambah 1 baris
        } else {
            section.classList.add('d-none');
            manual.classList.remove('d-none');
        }
    }

    function addVariant() {
        const list = document.getElementById('variantList');
        const row  = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 variant-row';
        row.innerHTML = `
            <div class="col-12 col-md-5">
                <input type="text" class="form-control form-control-sm"
                       name="variants[${variantIndex}][nama_varian]"
                       placeholder="Nama varian (contoh: M3x10mm)" required>
            </div>
            <div class="col-6 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Stok</span>
                    <input type="number" class="form-control"
                           name="variants[${variantIndex}][stock]"
                           placeholder="0" min="0" required>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control"
                           name="variants[${variantIndex}][price]"
                           placeholder="Harga (opsional)" min="0">
                </div>
            </div>
            <div class="col-12 col-md-1 text-center mt-1 mt-md-0">
                <button type="button" class="btn btn-danger btn-sm"
                        onclick="this.closest('.variant-row').remove()">✕</button>
            </div>
        `;
        list.appendChild(row);
        variantIndex++;
    }

    function previewImage(event) {
        const preview = document.getElementById('imagePreview');
        const file    = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        }
    }
</script>
@endsection
