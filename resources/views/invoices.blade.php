@extends('layouts.app')

@section('title', 'Invoice')

@section('content')
<div class="container">
    <h2>Invoice</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalHarga = 0; @endphp
            @foreach ($cartItems as $index => $item)
                @php
                    $subtotal = $item->product->price * $item->quantity;
                    $totalHarga += $subtotal;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('cart.remove', $item->id) }}" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total Harga</th>
                <th colspan="2">Rp {{ number_format($totalHarga, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <button onclick="window.print()" class="btn btn-primary">Cetak Invoice</button>
</div>
@endsection
