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
        @if (in_array($user->role, ['owner', 'admin', 'karyawan']))
        <div class="col-md-4 col-6">
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
        @if (in_array($user->role, ['owner', 'admin']))
        <div class="col-md-4 col-6">
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
        @if (in_array($user->role, ['owner', 'admin']))
        <div class="col-md-4 col-6">
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

        {{-- Modul Purchase Order --}}
        @if (in_array($user->role, ['owner', 'admin', 'karyawan']))
        <div class="col-md-4 col-6">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-info"><i class="bi bi-truck"></i> Purchase Order</h5>
                    <p class="card-text">Kelola pembelian dan pesanan ke supplier.</p>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-info btn-sm mb-2">
                        <i class="bi bi-truck"></i> Daftar PO
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Modul Kelola Akun (Hanya Owner & Admin) --}}
        @if (in_array($user->role, ['owner', 'admin']))
        <div class="col-md-4 col-6">
            <div class="card shadow border-0 mb-4 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-warning"><i class="bi bi-people-fill"></i> Kelola Akun</h5>
                    <p class="card-text">Kelola pengguna dan hak akses aplikasi.</p>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-warning btn-sm mb-2">
                        <i class="bi bi-people"></i> Daftar User
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
