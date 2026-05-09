@extends('layouts.app')

@section('title', 'Invoice')

@section('content')
<div class="container">
    <h2>Invoice #{{ $order->id }}</h2>
    <p>Tanggal: {{ $order->ordered_at }}</p>
    <p>Total Harga: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali Belanja</a>
</div>
@endsection
