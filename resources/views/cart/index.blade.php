@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container">
    <h2 class="mb-4">Keranjang Belanja</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(!empty($cart) && count($cart) > 0)
        @if(auth()->user()->role !== 'marketing')
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
                <strong id="selectedCount">0</strong> item dipilih
            </span>
            <button type="button" onclick="bulkRemoveSelected()" class="btn btn-danger btn-sm" style="
                padding: 8px 18px;
                border-radius: 10px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            ">
                🗑️ Hapus Terpilih
            </button>
            <button type="button" onclick="clearSelection()" class="btn btn-outline-light btn-sm" style="
                padding: 8px 16px;
                border-radius: 10px;
                font-weight: 500;
            ">
                ✕ Batal
            </button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        @if(auth()->user()->role !== 'marketing')
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input"
                                   style="cursor: pointer; width: 18px; height: 18px;"
                                   title="Pilih semua">
                        </th>
                        @endif
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kode Barang</th>
                        <th>Nama Produk</th>
                        <th>Varian</th>
                        <th>Vendor</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Total</th>
                        @if(auth()->user()->role !== 'marketing')
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($cart as $index => $item)
                        @php
                            $discountPct = $item['discount_percent'] ?? 0;
                            $originalPrice = $item['price'];
                            $discountedPrice = $discountPct > 0 ? round($originalPrice * (1 - $discountPct / 100)) : $originalPrice;
                            $subtotal = $discountedPrice * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr id="row-{{ $index }}" class="cart-row">
                            @if(auth()->user()->role !== 'marketing')
                            <td class="text-center">
                                <input type="checkbox" data-cart-key="{{ $index }}"
                                       class="form-check-input cart-checkbox"
                                       style="cursor: pointer; width: 18px; height: 18px;">
                            </td>
                            @endif
                            <td>{{ $loop->iteration }}</td>

                            {{-- Gambar --}}
                            <td class="text-center">
                                @if(!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                         alt="{{ $item['name'] }}"
                                         class="img-thumbnail"
                                         style="width:60px; height:60px; object-fit:cover;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Kode Barang --}}
                            <td>
                                @if(!empty($item['kode_barang']))
                                    <span class="badge bg-secondary font-monospace">{{ $item['kode_barang'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Nama Produk --}}
                            <td>{{ $item['name'] }}</td>

                            {{-- Varian --}}
                            <td>
                                @if(!empty($item['nama_varian']))
                                    <span class="badge bg-primary">{{ $item['nama_varian'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Vendor --}}
                            <td>
                                @if(!empty($item['vendor']))
                                    {{ $item['vendor'] }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Kategori --}}
                            <td>
                                @if(!empty($item['category']))
                                    <span class="badge bg-info text-dark">{{ $item['category'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>{{ $item['quantity'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $item['satuan'] ?? 'Pcs' }}</span>
                            </td>
                            <td>
                                @if($discountPct > 0)
                                    <small class="text-decoration-line-through text-muted">Rp {{ number_format($originalPrice, 0, ',', '.') }}</small><br>
                                    <span class="text-success fw-bold">Rp {{ number_format($discountedPrice, 0, ',', '.') }}</span>
                                @else
                                    Rp {{ number_format($originalPrice, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            @if(auth()->user()->role !== 'marketing')
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <form action="{{ route('cart.setDiscount', $index) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="discount_percent" class="form-select form-select-sm" style="width: 85px;" onchange="this.form.submit()">
                                            <option value="0" {{ $discountPct == 0 ? 'selected' : '' }}>0%</option>
                                            <option value="5" {{ $discountPct == 5 ? 'selected' : '' }}>5%</option>
                                            <option value="10" {{ $discountPct == 10 ? 'selected' : '' }}>10%</option>
                                            <option value="15" {{ $discountPct == 15 ? 'selected' : '' }}>15%</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('cart.remove', $index) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach

                    {{-- Baris Total -- colspan harus = jumlah kolom sebelum kolom Total --}}
                    <tr>
                        <td colspan="11" class="text-end"><strong>Total Keseluruhan</strong></td>
                        <td><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                        @if(auth()->user()->role !== 'marketing')
                        <td></td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>

        @if(auth()->user()->role !== 'marketing')
        <form action="{{ route('cart.clear') }}" method="POST" class="mb-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-warning">Kosongkan Keranjang</button>
        </form>

        <a href="{{ route('products.index') }}" class="btn btn-secondary mb-3">Kembali Belanja</a>

        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
            @csrf
            <input type="hidden" name="selected_items" id="checkoutSelectedItems" value="">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Nama Customer</label>
                <select name="customer_name" id="customer_name" class="form-select" required onchange="onCustomerChange(this)">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->name }}" data-alamat="{{ $customer->alamat }}">
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Kelola customer di <a href="{{ route('master-data.index') }}">Master Data</a></small>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Alamat otomatis terisi saat pilih customer" readonly style="background-color: #e9ecef;"></textarea>
            </div>
            <div class="mb-3">
                <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                <select name="status_pembayaran" id="status_pembayaran" class="form-select" required>
                    <option value="belum dibayar" selected>Belum Dibayar</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                    <option value="qris">QRIS</option>
                    <option value="tunai">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="ewallet">E-Wallet</option>
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ppnToggle" name="use_ppn" value="1" checked
                           style="width: 42px; height: 22px; cursor: pointer;">
                    <label class="form-check-label fw-semibold" for="ppnToggle" style="cursor: pointer; margin-left: 8px;">
                        Gunakan PPN 11%
                    </label>
                </div>
                <small class="text-muted">Centang untuk menambahkan PPN 11% pada invoice.</small>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return prepareCheckout()">Checkout</button>
        </form>
        @else
        <a href="{{ route('products.index') }}" class="btn btn-secondary mb-3">Kembali ke Produk</a>
        @endif

    @else
        <div class="alert alert-info text-center">
            <h4>Keranjang masih kosong.</h4>
            <p>Silakan tambahkan produk ke keranjang untuk melanjutkan.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Cari Produk</a>
        </div>
    @endif
</div>

{{-- Form tersembunyi untuk bulk remove --}}
<form id="bulkRemoveForm" action="{{ route('cart.bulkRemove') }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="cart_keys" id="bulkRemoveKeys" value="">
</form>

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

    .cart-row.selected {
        background-color: rgba(13, 110, 253, 0.08) !important;
    }

    .cart-checkbox:checked {
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
        const checkboxes = document.querySelectorAll('.cart-checkbox');
        const bulkBar = document.getElementById('bulkActionBar');
        const countEl = document.getElementById('selectedCount');

        function updateBulkBar() {
            const checked = document.querySelectorAll('.cart-checkbox:checked');
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

    function getSelectedKeys() {
        const checked = document.querySelectorAll('.cart-checkbox:checked');
        const keys = [];
        checked.forEach(cb => {
            keys.push(cb.getAttribute('data-cart-key'));
        });
        return keys;
    }

    function prepareCheckout() {
        const selectedKeys = getSelectedKeys();
        // If some items are selected, only checkout those items
        // If no items are selected, checkout all items (empty string = all)
        document.getElementById('checkoutSelectedItems').value = selectedKeys.join(',');
        return true;
    }

    function bulkRemoveSelected() {
        const keys = getSelectedKeys();
        if (keys.length === 0) {
            alert('Pilih minimal 1 item!');
            return;
        }
        if (!confirm('Yakin hapus ' + keys.length + ' item dari keranjang?')) return;

        document.getElementById('bulkRemoveKeys').value = keys.join(',');
        document.getElementById('bulkRemoveForm').submit();
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.cart-checkbox');
        const selectAll = document.getElementById('selectAll');
        checkboxes.forEach(cb => {
            cb.checked = false;
            cb.closest('tr').classList.remove('selected');
        });
        selectAll.checked = false;
        selectAll.indeterminate = false;
        document.getElementById('bulkActionBar').style.display = 'none';
    }

    function onCustomerChange(select) {
        const option = select.options[select.selectedIndex];
        const alamat = option ? (option.getAttribute('data-alamat') || '') : '';
        document.getElementById('alamat').value = alamat;
    }
</script>
@endsection
