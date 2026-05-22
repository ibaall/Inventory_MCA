@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Buat Purchase Order</h2>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
        @csrf

        <div class="card mb-3">
            <div class="card-header bg-dark text-white fw-semibold">Info Supplier</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="supplier_name" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_name" id="supplier_name" class="form-control"
                               required placeholder="Masukkan nama supplier" value="{{ old('supplier_name') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <input type="text" name="catatan" id="catatan" class="form-control"
                               placeholder="Catatan tambahan (opsional)" value="{{ old('catatan') }}">
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ppnToggle" name="use_ppn" value="1" checked
                           style="width: 42px; height: 22px; cursor: pointer;">
                    <label class="form-check-label fw-semibold" for="ppnToggle" style="cursor: pointer; margin-left: 8px;">
                        Gunakan PPN 11%
                    </label>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-dark text-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Item Produk</span>
                <button type="button" class="btn btn-success btn-sm" onclick="addRow()">
                    <i class="bi bi-plus-circle"></i> Tambah Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="itemsTable">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:4%" class="text-center">No</th>
                                <th style="width:25%">Produk</th>
                                <th style="width:20%">Varian</th>
                                <th style="width:8%" class="text-center">Kode</th>
                                <th style="width:8%" class="text-center">QTY</th>
                                <th style="width:14%" class="text-end">Harga Beli</th>
                                <th style="width:14%" class="text-end">Subtotal</th>
                                <th style="width:7%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-end">Total DPP:</td>
                                <td class="text-end" id="totalDpp">Rp 0</td>
                                <td></td>
                            </tr>
                            <tr id="ppnRow">
                                <td colspan="6" class="text-end">PPN 11%:</td>
                                <td class="text-end" id="totalPpn">Rp 0</td>
                                <td></td>
                            </tr>
                            <tr class="table-warning">
                                <td colspan="6" class="text-end">TOTAL:</td>
                                <td class="text-end text-danger" id="grandTotal">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle"></i> Simpan Purchase Order
        </button>
    </form>
</div>

<script>
    // Products with variants data from server
    const products = @json($products);
    let rowCount = 0;

    function addRow() {
        rowCount++;
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowCount}`;

        let productOptions = '<option value="">-- Pilih Produk --</option>';
        products.forEach(p => {
            const hasVariants = p.variants && p.variants.length > 0;
            const label = hasVariants
                ? `${p.name} (${p.variants.length} varian)`
                : `${p.name} (Stok: ${p.stock})`;
            productOptions += `<option value="${p.id}">${label}</option>`;
        });

        tr.innerHTML = `
            <td class="text-center">${rowCount}</td>
            <td>
                <select name="items[${rowCount}][product_id]" class="form-select form-select-sm"
                        required onchange="onProductChange(this, ${rowCount})" id="product-${rowCount}">
                    ${productOptions}
                </select>
            </td>
            <td>
                <select name="items[${rowCount}][variant_id]" class="form-select form-select-sm"
                        onchange="onVariantChange(this, ${rowCount})" id="variant-${rowCount}" disabled>
                    <option value="">— Tanpa Varian —</option>
                </select>
            </td>
            <td class="text-center" id="kode-${rowCount}" style="font-size:10px;">-</td>
            <td>
                <input type="number" name="items[${rowCount}][quantity]" class="form-control form-control-sm text-center"
                       min="1" value="1" required onchange="calcRow(${rowCount})" onkeyup="calcRow(${rowCount})">
            </td>
            <td>
                <input type="number" name="items[${rowCount}][price]" class="form-control form-control-sm text-end"
                       min="0" value="0" required onchange="calcRow(${rowCount})" onkeyup="calcRow(${rowCount})" id="price-${rowCount}">
            </td>
            <td class="text-end" id="subtotal-${rowCount}">Rp 0</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    }

    function onProductChange(select, idx) {
        const productId = parseInt(select.value);
        const variantSelect = document.getElementById(`variant-${idx}`);
        const kodeEl = document.getElementById(`kode-${idx}`);
        const priceEl = document.getElementById(`price-${idx}`);

        // Reset variant
        variantSelect.innerHTML = '<option value="">— Tanpa Varian —</option>';
        variantSelect.disabled = true;

        if (!productId) {
            kodeEl.textContent = '-';
            priceEl.value = 0;
            calcRow(idx);
            return;
        }

        const product = products.find(p => p.id === productId);
        if (!product) return;

        kodeEl.textContent = product.kode_barang || '-';
        priceEl.value = product.price || 0;

        // If product has variants, populate variant dropdown
        if (product.variants && product.variants.length > 0) {
            variantSelect.disabled = false;
            product.variants.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = `${v.nama_varian} (Stok: ${v.stock})`;
                opt.setAttribute('data-price', v.price);
                opt.setAttribute('data-stock', v.stock);
                variantSelect.appendChild(opt);
            });

            // Auto-select first variant
            if (product.variants.length > 0) {
                variantSelect.value = product.variants[0].id;
                priceEl.value = product.variants[0].price || product.price;
            }
        }

        calcRow(idx);
    }

    function onVariantChange(select, idx) {
        const priceEl = document.getElementById(`price-${idx}`);
        const option = select.options[select.selectedIndex];

        if (option && option.value) {
            const variantPrice = option.getAttribute('data-price');
            if (variantPrice) {
                priceEl.value = variantPrice;
            }
        }

        calcRow(idx);
    }

    function removeRow(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (row) row.remove();
        calcTotal();
    }

    function calcRow(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;
        const qty = parseInt(row.querySelector(`[name="items[${idx}][quantity]"]`).value) || 0;
        const price = parseFloat(row.querySelector(`[name="items[${idx}][price]"]`).value) || 0;
        const subtotal = qty * price;
        document.getElementById(`subtotal-${idx}`).textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        calcTotal();
    }

    function calcTotal() {
        let total = 0;
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const qtyInput = tr.querySelector('input[name*="[quantity]"]');
            const priceInput = tr.querySelector('input[name*="[price]"]');
            if (qtyInput && priceInput) {
                total += (parseInt(qtyInput.value) || 0) * (parseFloat(priceInput.value) || 0);
            }
        });

        const usePpn = document.getElementById('ppnToggle').checked;
        const ppn = usePpn ? Math.round(total * 0.11) : 0;

        document.getElementById('totalDpp').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('totalPpn').textContent = 'Rp ' + ppn.toLocaleString('id-ID');
        document.getElementById('grandTotal').textContent = 'Rp ' + (total + ppn).toLocaleString('id-ID');
        document.getElementById('ppnRow').style.display = usePpn ? '' : 'none';
    }

    document.getElementById('ppnToggle').addEventListener('change', calcTotal);

    // Start with one row
    addRow();
</script>
@endsection
