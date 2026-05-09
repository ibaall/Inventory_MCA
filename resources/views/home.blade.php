@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Selamat Datang, {{ auth()->user()->name }} 👋</h2>

    <div class="row">
        @php
            $user = auth()->user();
        @endphp

        {{-- Modul Penjualan dan Distribusi --}}
        @if ($user->role === 'SD' || $user->role === 'admin')
        <div class="col-md-4">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary"><i class="bi bi-bag-check-fill"></i> Penjualan & Distribusi</h5>
                    <p class="card-text">Mengelola daftar produk dan keranjang pelanggan.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                        <i class="bi bi-box-seam"></i> Lihat Produk
                    </a>
                    <br>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-cart"></i> Lihat Keranjang
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Manajemen Material --}}
        @if ($user->role === 'MM' || $user->role === 'admin')
        <div class="col-md-4">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-success"><i class="bi bi-boxes"></i> Manajemen Material</h5>
                    <p class="card-text">Menambah dan memantau stok produk yang tersedia.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-outline-success btn-sm mb-2">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                    <br>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-box-seam"></i> Lihat Produk
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Keuangan & Akuntansi --}}
        @if ($user->role === 'FI' || $user->role === 'admin')
        <div class="col-md-4">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger"><i class="bi bi-cash-stack"></i> Keuangan & Akuntansi</h5>
                    <p class="card-text">Kelola invoice dan laporan keuangan perusahaan.</p>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-danger btn-sm mb-2">
                        <i class="bi bi-list-check"></i> Daftar Invoice
                    </a>
                    <br>
                    <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
