@extends('layouts.app')

@section('title', 'Edit Pesanan')

@section('content')
<div class="container">
    <h2>Edit Pesanan - ID: {{ $order->id }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
            <select name="status_pembayaran" id="status_pembayaran" class="form-select" required>
                <option value="lunas" {{ $order->status_pembayaran == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="belum dibayar" {{ $order->status_pembayaran == 'belum dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                <option value="qris" {{ $order->metode_pembayaran == 'qris' ? 'selected' : '' }}>QRIS</option>
                <option value="tunai" {{ $order->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="transfer" {{ $order->metode_pembayaran == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="ewallet" {{ $order->metode_pembayaran == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
            </select>
        </div>

        <hr>
        <h4>Data Pasien & Pengiriman</h4>

        <div class="mb-3">
            <label for="nama_pasien" class="form-label">Nama Pasien</label>
            <input type="text" name="nama_pasien" id="nama_pasien" class="form-control"
                   value="{{ old('nama_pasien', $order->nama_pasien) }}" placeholder="Contoh: Budi Santoso">
        </div>

        <div class="mb-3">
            <label for="operator" class="form-label">Operator (Dokter)</label>
            <input type="text" name="operator" id="operator" class="form-control"
                   value="{{ old('operator', $order->operator) }}" placeholder="Contoh: dr. Setiawan, Sp.B">
        </div>

        <div class="mb-3">
            <label for="tanggal_operasi" class="form-label">Tanggal Operasi</label>
            <input type="date" name="tanggal_operasi" id="tanggal_operasi" class="form-control"
                   value="{{ old('tanggal_operasi', $order->tanggal_operasi ? \Carbon\Carbon::parse($order->tanggal_operasi)->format('Y-m-d') : '') }}">
        </div>

        <div class="mb-3">
            <label for="surat_jalan_number" class="form-label">Nomer Surat Jalan</label>
            <input type="text" name="surat_jalan_number" id="surat_jalan_number" class="form-control"
                   value="{{ old('surat_jalan_number', $order->surat_jalan_number) }}" placeholder="Contoh: 01/SJ/MCA/V/2026">
        </div>

        <div class="mb-3">
            <label for="tanggal_jatuh_tempo" class="form-label">Tanggal Jatuh Tempo</label>
            <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="form-control"
                   value="{{ old('tanggal_jatuh_tempo', $order->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($order->tanggal_jatuh_tempo)->format('Y-m-d') : '') }}">
        </div>

        <div class="mb-3">
            <label for="alamat_customer" class="form-label">Pilih Alamat dari Customer (Opsional)</label>
            <select id="alamat_customer" class="form-select" onchange="onCustomerAddressChange(this)">
                <option value="">-- Pilih Customer untuk Isi Alamat --</option>
                @foreach($customers as $customer)
                    @if($customer->alamat)
                        <option value="{{ $customer->alamat }}">{{ $customer->name }} — {{ $customer->alamat }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Alamat otomatis terisi dari customer" readonly style="background-color: #e9ecef;">{{ old('alamat', $order->alamat) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Pesanan</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    function onCustomerAddressChange(select) {
        document.getElementById('alamat').value = select.value;
    }
</script>
@endsection
