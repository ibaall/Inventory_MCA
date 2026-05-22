<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PT MCA')</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f1f3f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #212529;
            padding-top: 20px;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: white !important;
            padding: 10px 15px;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: #ffc107;
            color: #212529 !important;
            font-weight: 500;
        }

        .sidebar .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #ffc107;
            text-align: center;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 250px;
        }

        .dropdown-menu {
            position: absolute !important;
            top: 60px;
            left: 10px;
            background-color: #343a40;
            width: 200px;
            border-radius: 5px;
        }

        .dropdown-menu .nav-link {
            display: block !important;
            padding: 10px;
            color: white !important;
        }

        .dropdown-menu .nav-link:hover {
            background-color: #f8d210;
            color: #212529 !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }
            .sidebar .nav-link {
                display: none;
            }
            .sidebar .logo-text {
                display: none;
            }
            .sidebar .logo {
                font-size: 30px;
                color: white;
            }
            .content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column align-items-center">
        <a href="#" class="logo d-lg-none" id="menu-icon"><i class="bi bi-list"></i></a>
        <div class="logo-text d-none d-lg-block">PT MCA</div>
        <hr class="text-white d-none d-lg-block w-75">
        <ul class="nav flex-column d-none d-lg-block">

            @auth
                @php $user = auth()->user(); @endphp

                {{-- Role MM --}}
                @if ($user->role == 'MM')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i> Tambah Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                @endif

                {{-- Role DD --}}
                @if ($user->role == 'DD')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i> Tambah Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                @endif

                {{-- Role SD --}}
                @if ($user->role == 'SD')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                @endif

                {{-- Role FI --}}
                @if ($user->role == 'FI')
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.keuangan') }}"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
                @endif

                {{-- Role Admin --}}
                @if ($user->role == 'admin')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i> Tambah Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Invoice</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchase-orders.index') }}"><i class="bi bi-truck"></i> Purchase Order</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.keuangan') }}"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
                @endif

                {{-- Logout --}}
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link text-white nav-link"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                    </form>
                </li>
            @endauth

        </ul>
    </div>

    <!-- Mobile Dropdown -->
    <div class="dropdown d-lg-none">
        <ul class="dropdown-menu" id="mobile-menu">
            <li><a class="nav-link" href="{{ route('products.create') }}">Tambah Produk</a></li>
            <li><a class="nav-link" href="{{ route('products.index') }}">Lihat Produk</a></li>
            <li><a class="nav-link" href="{{ route('cart.index') }}">Keranjang</a></li>
            <li><a class="nav-link" href="{{ route('orders.index') }}">Daftar Invoice</a></li>
            @auth
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Logout</button>
                    </form>
                </li>
            @endauth
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById("menu-icon").addEventListener("click", function () {
            document.getElementById("mobile-menu").classList.toggle("show");
        });
    </script>
</body>
</html>
