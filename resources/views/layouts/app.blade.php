<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'PT MCA')</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f1f3f5;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* ===== DESKTOP SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #212529;
            padding-top: 20px;
            z-index: 1000;
            overflow-y: auto;
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

        /* ===== MOBILE TOP BAR ===== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background-color: #212529;
            z-index: 1100;
            padding: 0 16px;
            align-items: center;
            justify-content: space-between;
        }

        .mobile-topbar .logo-text {
            font-size: 20px;
            font-weight: bold;
            color: #ffc107;
        }

        .mobile-topbar .menu-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            padding: 8px;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .mobile-topbar .menu-toggle:active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* ===== MOBILE SIDEBAR DRAWER ===== */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1200;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
        }

        .mobile-drawer {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100vh;
            background-color: #212529;
            z-index: 1300;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }

        .mobile-drawer.active {
            transform: translateX(280px);
        }

        .mobile-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-drawer-header .logo-text {
            font-size: 22px;
            font-weight: bold;
            color: #ffc107;
        }

        .mobile-drawer-header .close-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 22px;
            padding: 8px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .mobile-drawer-header .close-btn:active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Mobile User Info */
        .mobile-user-info {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mobile-user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #212529;
            flex-shrink: 0;
        }

        .mobile-user-details .user-name {
            color: white;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.3;
        }

        .mobile-user-details .user-role {
            color: #ffc107;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mobile Nav Items */
        .mobile-nav-section {
            padding: 8px 0;
        }

        .mobile-nav-section-title {
            padding: 8px 20px 4px;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.35);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .mobile-nav-item {
            display: flex;
            align-items: center;
            padding: 13px 20px;
            color: rgba(255, 255, 255, 0.85) !important;
            text-decoration: none !important;
            font-size: 15px;
            transition: all 0.2s;
            gap: 14px;
            border-left: 3px solid transparent;
        }

        .mobile-nav-item:active,
        .mobile-nav-item:hover {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107 !important;
            border-left-color: #ffc107;
        }

        .mobile-nav-item i {
            font-size: 18px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .mobile-nav-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin: 4px 20px;
        }

        .mobile-nav-item.logout-btn {
            color: #ff6b6b !important;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .mobile-nav-item.logout-btn:active {
            background-color: rgba(255, 107, 107, 0.1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none !important;
            }

            .mobile-topbar {
                display: flex;
            }

            .mobile-overlay {
                display: block;
                pointer-events: none;
            }

            .mobile-overlay.active {
                pointer-events: auto;
            }

            .content {
                margin-left: 0;
                padding: 16px;
                padding-top: 72px;
            }
        }

        @media (min-width: 992px) {
            .mobile-drawer,
            .mobile-overlay,
            .mobile-topbar {
                display: none !important;
            }
        }

        /* ===== iPhone safe area padding ===== */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .mobile-drawer {
                padding-bottom: calc(env(safe-area-inset-bottom) + 16px);
            }
            .mobile-topbar {
                padding-top: env(safe-area-inset-top);
                height: calc(56px + env(safe-area-inset-top));
            }
            @media (max-width: 991.98px) {
                .content {
                    padding-top: calc(72px + env(safe-area-inset-top));
                    padding-bottom: calc(16px + env(safe-area-inset-bottom));
                }
            }
        }
    </style>
</head>
<body>

    @auth
        @php $user = auth()->user(); @endphp
    @endauth

    <!-- ===== MOBILE TOP BAR ===== -->
    <div class="mobile-topbar">
        <button class="menu-toggle" id="mobile-menu-toggle" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="logo-text">PT MCA</span>
        <div style="width: 40px;"></div> {{-- Spacer for centering --}}
    </div>

    <!-- ===== MOBILE OVERLAY ===== -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

    <!-- ===== MOBILE DRAWER ===== -->
    <div class="mobile-drawer" id="mobile-drawer">
        <div class="mobile-drawer-header">
            <span class="logo-text">PT MCA</span>
            <button class="close-btn" id="mobile-drawer-close" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        @auth
            <!-- User Info -->
            <div class="mobile-user-info">
                <div class="mobile-user-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="mobile-user-details">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-role">{{ $user->role }}</div>
                </div>
            </div>

            {{-- Role Owner & Admin: Akses Penuh --}}
            @if (in_array($user->role, ['owner', 'admin']))
                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Produk</div>
                    <a class="mobile-nav-item" href="{{ route('products.create') }}">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                    <a class="mobile-nav-item" href="{{ route('products.index') }}">
                        <i class="bi bi-box-seam"></i> Lihat Produk
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Transaksi</div>
                    <a class="mobile-nav-item" href="{{ route('cart.index') }}">
                        <i class="bi bi-receipt"></i> Lihat Keranjang
                    </a>
                    <a class="mobile-nav-item" href="{{ route('orders.index') }}">
                        <i class="bi bi-list-check"></i> Daftar Invoice
                    </a>
                    <a class="mobile-nav-item" href="{{ route('purchase-orders.index') }}">
                        <i class="bi bi-truck"></i> Purchase Order
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Keuangan</div>
                    <a class="mobile-nav-item" href="{{ route('laporan.keuangan') }}">
                        <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
                    </a>
                    <a class="mobile-nav-item" href="{{ route('financial-reports.index') }}">
                        <i class="bi bi-journal-text"></i> Laporan Detail
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Administrasi</div>
                    <a class="mobile-nav-item" href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i> Kelola Akun
                    </a>
                </div>
            @endif

            {{-- Role Karyawan: Akses Terbatas --}}
            @if ($user->role == 'karyawan')
                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Menu</div>
                    <a class="mobile-nav-item" href="{{ route('products.index') }}">
                        <i class="bi bi-box-seam"></i> Lihat Produk
                    </a>
                    <a class="mobile-nav-item" href="{{ route('cart.index') }}">
                        <i class="bi bi-receipt"></i> Lihat Keranjang
                    </a>
                    <a class="mobile-nav-item" href="{{ route('orders.index') }}">
                        <i class="bi bi-list-check"></i> Daftar Invoice
                    </a>
                    <a class="mobile-nav-item" href="{{ route('purchase-orders.index') }}">
                        <i class="bi bi-truck"></i> Purchase Order
                    </a>
                </div>
            @endif

            <div class="mobile-nav-divider"></div>

            <!-- Logout -->
            <div class="mobile-nav-section">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="mobile-nav-item logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <!-- ===== DESKTOP SIDEBAR ===== -->
    <div class="sidebar d-flex flex-column align-items-center d-none d-lg-flex">
        <div class="logo-text">PT MCA</div>
        <hr class="text-white w-75">
        <ul class="nav flex-column w-100">

            @auth
                {{-- Role Owner & Admin: Akses Penuh --}}
                @if (in_array($user->role, ['owner', 'admin']))
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i> Tambah Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Invoice</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchase-orders.index') }}"><i class="bi bi-truck"></i> Purchase Order</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.keuangan') }}"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('financial-reports.index') }}"><i class="bi bi-journal-text"></i> Laporan Detail</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Kelola Akun</a></li>
                @endif

                {{-- Role Karyawan: Akses Terbatas --}}
                @if ($user->role == 'karyawan')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Invoice</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchase-orders.index') }}"><i class="bi bi-truck"></i> Purchase Order</a></li>
                @endif

                {{-- User Info + Logout --}}
                <hr class="text-white w-75 mx-auto my-2">
                <li class="nav-item px-3 mb-1">
                    <small class="text-white-50"><i class="bi bi-person-circle"></i> {{ $user->name }}</small><br>
                    <small class="text-warning">{{ strtoupper($user->role) }}</small>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link text-white nav-link"><i class="bi bi-box-arrow-right"></i> Keluar</button>
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
        (function() {
            const toggle = document.getElementById('mobile-menu-toggle');
            const drawer = document.getElementById('mobile-drawer');
            const overlay = document.getElementById('mobile-overlay');
            const closeBtn = document.getElementById('mobile-drawer-close');

            function openDrawer() {
                drawer.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                drawer.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (toggle) toggle.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (overlay) overlay.addEventListener('click', closeDrawer);

            // Close on swipe left
            let touchStartX = 0;
            if (drawer) {
                drawer.addEventListener('touchstart', function(e) {
                    touchStartX = e.touches[0].clientX;
                }, { passive: true });

                drawer.addEventListener('touchend', function(e) {
                    const touchEndX = e.changedTouches[0].clientX;
                    if (touchStartX - touchEndX > 80) {
                        closeDrawer();
                    }
                }, { passive: true });
            }
        })();
    </script>
</body>
</html>
