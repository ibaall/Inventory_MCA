@extends('layouts.app')

@section('title', 'Buat Konsinyasi Baru')

@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-arrow-left-right text-primary"></i> Buat Konsinyasi Baru</h2>
        <a href="{{ route('consignments.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Gagal menyimpan!</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('consignments.store') }}" method="POST" id="consignmentForm">
        @csrf
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Data Customer (Konsinyasi)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Customer (Luar Kota) <span class="text-danger">*</span></label>
                        <select name="customer_name" class="form-select customer-select" required>
                            <option value="" disabled selected>-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->name }}" {{ old('customer_name') == $customer->name ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Surat Jalan akan ditujukan ke customer ini. Invoice baru diterbitkan saat barang dipakai.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Daftar Barang Konsinyasi</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">Produk / Varian</th>
                            <th style="width: 20%;">Qty</th>
                            <th style="width: 25%;">Harga Satuan Kesepakatan (Rp)</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="" disabled selected>-- Pilih Produk --</option>
                                    @foreach($products as $p)
                                        @if($p->variants->count() > 0)
                                            @foreach($p->variants as $v)
                                                <option value="{{ $p->id }}-{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">
                                                    {{ $p->name }} - {{ $v->name }} (Stok: {{ $v->stock }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock }}">
                                                {{ $p->name }} (Stok: {{ $p->stock }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][qty_total]" class="form-control qty-input" min="1" required placeholder="Qty">
                                <small class="text-danger stock-warning d-none">Melebihi stok gudang!</small>
                            </td>
                            <td>
                                <input type="number" name="items[0][harga_satuan]" class="form-control price-input" min="0" required placeholder="Harga untuk nanti Invoice">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-sm btn-success" id="addRow">
                                    <i class="bi bi-plus"></i> Tambah Baris
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-lg btn-primary" id="btnSubmit">
                <i class="bi bi-save"></i> Simpan & Kurangi Stok Gudang
            </button>
        </div>
    </form>
</div>

<!-- Select2 setup similar to existing forms -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let rowIdx = 1;

        function initSelect2() {
            $('.product-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            $('.customer-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        initSelect2();

        // Add row
        $('#addRow').click(function() {
            let tr = `
                <tr>
                    <td>
                        <select name="items[${rowIdx}][product_id]" class="form-select product-select" required>
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            @foreach($products as $p)
                                @if($p->variants->count() > 0)
                                    @foreach($p->variants as $v)
                                        <option value="{{ $p->id }}-{{ $v->id }}" data-price="{{ $v->price }}" data-stock="{{ $v->stock }}">
                                            {{ $p->name }} - {{ $v->name }} (Stok: {{ $v->stock }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock }}">
                                        {{ $p->name }} (Stok: {{ $p->stock }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][qty_total]" class="form-control qty-input" min="1" required placeholder="Qty">
                        <small class="text-danger stock-warning d-none">Melebihi stok gudang!</small>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][harga_satuan]" class="form-control price-input" min="0" required placeholder="Harga untuk nanti Invoice">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#itemsBody').append(tr);
            initSelect2();
            updateRemoveButtons();
            rowIdx++;
        });

        // Remove row
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateRemoveButtons();
        });

        function updateRemoveButtons() {
            let rowCount = $('#itemsBody tr').length;
            if (rowCount > 1) {
                $('.remove-row').prop('disabled', false);
            } else {
                $('.remove-row').first().prop('disabled', true);
            }
        }

        // Auto-fill price and check max stock on select change
        $(document).on('change', '.product-select', function() {
            let selected = $(this).find(':selected');
            let price = selected.data('price');
            let stock = selected.data('stock');
            let row = $(this).closest('tr');
            
            row.find('.price-input').val(price);
            let qtyInput = row.find('.qty-input');
            qtyInput.attr('max', stock);
            
            checkStock(qtyInput);
        });

        $(document).on('input', '.qty-input', function() {
            checkStock($(this));
        });

        function checkStock(qtyInput) {
            let row = qtyInput.closest('tr');
            let selected = row.find('.product-select :selected');
            if(!selected.val()) return;

            let stock = parseInt(selected.data('stock'));
            let val = parseInt(qtyInput.val());
            let warning = row.find('.stock-warning');
            
            if(val > stock) {
                warning.removeClass('d-none');
            } else {
                warning.addClass('d-none');
            }
        }
    });
</script>
@endsection
