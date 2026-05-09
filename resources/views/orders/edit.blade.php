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

        <button type="submit" class="btn btn-success">Update Pesanan</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
