<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'PT MCA')</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

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
            padding-top: 80px;
        }

        /* ===== TOP NAVBAR ===== */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .top-navbar .navbar-greeting {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-navbar .greeting-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #1a1a2e;
            font-size: 15px;
            flex-shrink: 0;
        }

        .top-navbar .greeting-text h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            line-height: 1.3;
        }

        .top-navbar .greeting-text span {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
        }

        .top-navbar .navbar-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .top-navbar .navbar-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .top-navbar .badge-owner {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #1a1a2e;
        }

        .top-navbar .badge-admin {
            background: linear-gradient(135deg, #4fc3f7, #29b6f6);
            color: #0d2137;
        }

        .top-navbar .badge-karyawan_gudang {
            background: linear-gradient(135deg, #66bb6a, #43a047);
            color: #fff;
        }

        .top-navbar .badge-karyawan_marketing {
            background: linear-gradient(135deg, #ab47bc, #8e24aa);
            color: #fff;
        }

        .top-navbar .navbar-clock {
            display: flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            font-weight: 500;
        }

        .top-navbar .navbar-clock i {
            font-size: 14px;
            color: #ffc107;
        }

        .top-navbar .navbar-divider {
            width: 1px;
            height: 28px;
            background: rgba(255,255,255,0.12);
        }

        .top-navbar .navbar-quick-action {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .top-navbar .navbar-quick-action:hover {
            background: rgba(255,193,7,0.15);
            color: #ffc107;
            border-color: rgba(255,193,7,0.3);
        }

        .top-navbar .navbar-quick-action i {
            font-size: 14px;
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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1200;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
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

        @media (max-width: 991.98px) {
            .top-navbar {
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

        /* ========================================================
           GLOBAL MOBILE RESPONSIVE FIXES (iPhone 13 / 390px)
           ======================================================== */
        @media (max-width: 767.98px) {

            /* --- Container & Typography --- */
            .content .container {
                padding-left: 8px;
                padding-right: 8px;
            }

            .content h2 {
                font-size: 1.3rem;
            }

            .content h5,
            .content .card-title {
                font-size: 0.95rem;
            }

            /* --- Tables: make them usable on mobile --- */
            .table-responsive {
                border-radius: 8px;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                font-size: 0.78rem;
            }

            .table th,
            .table td {
                padding: 6px 8px;
                white-space: nowrap;
            }

            .table .btn-sm {
                font-size: 0.7rem;
                padding: 4px 8px;
                white-space: nowrap;
            }

            /* --- Action buttons in table cells: stack them --- */
            td .btn-sm {
                margin-bottom: 3px !important;
                display: inline-block;
            }

            /* --- Cards: full width, less padding --- */
            .card-body {
                padding: 12px;
            }

            .card-header {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            /* --- Row/Col on mobile --- */
            .row > .col-md-6 {
                margin-bottom: 12px;
            }

            /* --- Page header: stack title + buttons vertically --- */
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 10px;
            }

            .d-flex.justify-content-between.align-items-center > .d-flex {
                flex-wrap: wrap;
                gap: 6px;
                width: 100%;
            }

            .d-flex.justify-content-between.align-items-center > .d-flex > .btn,
            .d-flex.justify-content-between.align-items-center > .d-flex > form > .btn {
                font-size: 0.78rem;
                padding: 6px 10px;
                flex: 1 1 auto;
                min-width: 0;
                text-align: center;
            }

            /* --- Forms: proper sizing on mobile --- */
            .form-control,
            .form-select {
                font-size: 0.9rem;
            }

            .form-label {
                font-size: 0.85rem;
            }

            /* --- Modals: full-width on mobile --- */
            .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .modal-body {
                padding: 16px;
            }

            .modal-content {
                border-radius: 12px;
            }

            /* --- Floating Bulk Action Bar: mobile friendly --- */
            #bulkActionBar {
                left: 10px !important;
                right: 10px !important;
                transform: none !important;
                flex-wrap: wrap !important;
                padding: 10px 14px !important;
                gap: 8px !important;
                border-radius: 12px !important;
                font-size: 12px !important;
                bottom: 16px !important;
            }

            #bulkActionBar .btn {
                font-size: 11px !important;
                padding: 6px 10px !important;
                border-radius: 8px !important;
            }

            /* --- Badges --- */
            .badge {
                font-size: 0.68rem;
                padding: 3px 6px;
            }

            /* --- Filter cards on mobile --- */
            .card .row.g-3 > [class*="col-md"] {
                margin-bottom: 4px;
            }

            /* --- Variant modal tabs on mobile --- */
            .nav-tabs .nav-link {
                font-size: 0.8rem;
                padding: 8px 10px;
            }

            /* --- Input group compact on mobile --- */
            .input-group {
                flex-wrap: nowrap;
            }

            .input-group-text {
                font-size: 0.8rem;
                padding: 4px 8px;
            }

            /* --- Product images smaller on mobile --- */
            .img-thumbnail {
                width: 50px !important;
                height: 50px !important;
            }

            /* --- Pagination styling on mobile --- */
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 2px;
            }

            .pagination .page-link {
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            /* --- Alert/Info boxes --- */
            .alert {
                font-size: 0.85rem;
                padding: 10px 14px;
            }

            /* --- Charts: proper sizing on mobile --- */
            .card-body canvas {
                max-height: 200px !important;
            }

            /* --- Checkout/Form buttons full width on mobile --- */
            .btn-primary,
            .btn-success,
            .btn-warning,
            .btn-secondary,
            .btn-danger {
                font-size: 0.85rem;
            }

            /* --- Tabs scrollable on mobile --- */
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .nav-tabs::-webkit-scrollbar {
                display: none;
            }

            .nav-tabs .nav-item {
                flex-shrink: 0;
            }

            /* --- Home dashboard cards --- */
            .col-6 .card-body {
                padding: 10px;
            }

            .col-6 .card-title {
                font-size: 0.8rem;
            }

            .col-6 .card-text {
                font-size: 0.72rem;
                margin-bottom: 8px;
            }

            .col-6 .btn-sm {
                font-size: 0.68rem;
                padding: 3px 6px;
            }
        }

        /* Extra small phones (SE etc) */
        @media (max-width: 374px) {
            .content {
                padding: 10px;
            }

            .table {
                font-size: 0.72rem;
            }

            .col-6 .card-title {
                font-size: 0.72rem;
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

            {{-- ======= OWNER: Akses Penuh ======= --}}
            @if ($user->role == 'owner')
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
                    <div class="mobile-nav-section-title">Kas Harian</div>
                    <a class="mobile-nav-item" href="{{ route('bukti-kas.bkm.index') }}">
                        <i class="bi bi-cash-coin"></i> BKM (Kas Masuk)
                    </a>
                    <a class="mobile-nav-item" href="{{ route('bukti-kas.bkk.index') }}">
                        <i class="bi bi-cash-stack"></i> BKK (Kas Keluar)
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Bukti Bank</div>
                    <a class="mobile-nav-item" href="{{ route('bukti-bank.masuk.index') }}">
                        <i class="bi bi-bank"></i> Bukti Bank Masuk
                    </a>
                    <a class="mobile-nav-item" href="{{ route('bukti-bank.keluar.index') }}">
                        <i class="bi bi-bank2"></i> Bukti Bank Keluar
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Akuntansi</div>
                    <a class="mobile-nav-item" href="{{ route('jurnal-koreksi.index') }}">
                        <i class="bi bi-journal-check"></i> Jurnal Koreksi
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Laporan</div>
                    <a class="mobile-nav-item" href="{{ route('laporan.keuangan') }}">
                        <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
                    </a>
                    <a class="mobile-nav-item" href="{{ route('financial-reports.index') }}">
                        <i class="bi bi-journal-text"></i> Laporan Detail
                    </a>
                    <a class="mobile-nav-item" href="{{ route('stock-report.index') }}">
                        <i class="bi bi-box-seam-fill"></i> Laporan Stok
                    </a>
                    <a class="mobile-nav-item" href="{{ route('laporan-kas.index') }}">
                        <i class="bi bi-journal-richtext"></i> Laporan Kas
                    </a>
                    <a class="mobile-nav-item" href="{{ route('buku-besar.index') }}">
                        <i class="bi bi-book"></i> Buku Besar
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Administrasi</div>
                    <a class="mobile-nav-item" href="{{ route('master-data.index') }}">
                        <i class="bi bi-database"></i> Master Data
                    </a>
                    <a class="mobile-nav-item" href="{{ route('no-perkiraan.index') }}">
                        <i class="bi bi-journal-bookmark"></i> No. Perkiraan
                    </a>
                    <a class="mobile-nav-item" href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i> Kelola Akun
                    </a>
                </div>

                {{-- Mobile Login Tracker Link --}}
                <div class="mobile-nav-divider"></div>
                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Monitoring</div>
                    <a class="mobile-nav-item" href="{{ route('login-tracker.index') }}">
                        <i class="bi bi-activity"></i> Login Tracker
                    </a>
                </div>
            @endif

            {{-- ======= ADMIN ======= --}}
            @if ($user->role == 'admin')
                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Produk</div>
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
                    <div class="mobile-nav-section-title">Kas Harian</div>
                    <a class="mobile-nav-item" href="{{ route('bukti-kas.bkm.index') }}">
                        <i class="bi bi-cash-coin"></i> BKM (Kas Masuk)
                    </a>
                    <a class="mobile-nav-item" href="{{ route('bukti-kas.bkk.index') }}">
                        <i class="bi bi-cash-stack"></i> BKK (Kas Keluar)
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Laporan</div>
                    <a class="mobile-nav-item" href="{{ route('laporan.keuangan') }}">
                        <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
                    </a>
                    <a class="mobile-nav-item" href="{{ route('financial-reports.index') }}">
                        <i class="bi bi-journal-text"></i> Laporan Detail
                    </a>
                    <a class="mobile-nav-item" href="{{ route('laporan-kas.index') }}">
                        <i class="bi bi-journal-richtext"></i> Laporan Kas
                    </a>
                </div>

                <div class="mobile-nav-divider"></div>

                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Administrasi</div>
                    <a class="mobile-nav-item" href="{{ route('master-data.index') }}">
                        <i class="bi bi-database"></i> Master Data
                    </a>
                    <a class="mobile-nav-item" href="{{ route('no-perkiraan.index') }}">
                        <i class="bi bi-journal-bookmark"></i> No. Perkiraan
                    </a>
                </div>
            @endif

            {{-- ======= KARYAWAN GUDANG: Produk, Keranjang, PO, Invoice, Laporan Stok ======= --}}
            @if ($user->role == 'karyawan_gudang')
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
                    <div class="mobile-nav-section-title">Laporan</div>
                    <a class="mobile-nav-item" href="{{ route('stock-report.index') }}">
                        <i class="bi bi-box-seam-fill"></i> Laporan Stok
                    </a>
                </div>
            @endif

            {{-- ======= KARYAWAN MARKETING: Hanya Laporan Stok ======= --}}
            @if ($user->role == 'karyawan_marketing')
                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">Laporan</div>
                    <a class="mobile-nav-item" href="{{ route('stock-report.index') }}">
                        <i class="bi bi-box-seam-fill"></i> Laporan Stok
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
                {{-- ======= OWNER: Akses Penuh ======= --}}
                @if ($user->role == 'owner')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i> Tambah Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Invoice</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchase-orders.index') }}"><i class="bi bi-truck"></i> Purchase Order</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('master-data.index') }}"><i class="bi bi-database"></i> Master Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('no-perkiraan.index') }}"><i class="bi bi-journal-bookmark"></i> No. Perkiraan</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('bukti-kas.bkm.index') }}"><i class="bi bi-cash-coin"></i> BKM (Kas Masuk)</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('bukti-kas.bkk.index') }}"><i class="bi bi-cash-stack"></i> BKK (Kas Keluar)</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('bukti-bank.masuk.index') }}"><i class="bi bi-bank"></i> Bukti Bank Masuk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('bukti-bank.keluar.index') }}"><i class="bi bi-bank2"></i> Bukti Bank Keluar</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('jurnal-koreksi.index') }}"><i class="bi bi-journal-check"></i> Jurnal Koreksi</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.keuangan') }}"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('financial-reports.index') }}"><i class="bi bi-journal-text"></i> Laporan Detail</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('stock-report.index') }}"><i class="bi bi-box-seam-fill"></i> Laporan Stok</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan-kas.index') }}"><i class="bi bi-journal-richtext"></i> Laporan Kas</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('buku-besar.index') }}"><i class="bi bi-book"></i> Buku Besar</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Kelola Akun</a></li>

                    {{-- Login Tracker Link --}}
                    <li class="nav-item"><a class="nav-link" href="{{ route('login-tracker.index') }}"><i class="bi bi-activity"></i> Login Tracker</a></li>
                @endif

                {{-- ======= ADMIN ======= --}}
                @if ($user->role == 'admin')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Invoice</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchase-orders.index') }}"><i class="bi bi-truck"></i> Purchase Order</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('master-data.index') }}"><i class="bi bi-database"></i> Master Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('no-perkiraan.index') }}"><i class="bi bi-journal-bookmark"></i> No. Perkiraan</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('bukti-kas.bkm.index') }}"><i class="bi bi-cash-coin"></i> BKM (Kas Masuk)</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('bukti-kas.bkk.index') }}"><i class="bi bi-cash-stack"></i> BKK (Kas Keluar)</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan.keuangan') }}"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('financial-reports.index') }}"><i class="bi bi-journal-text"></i> Laporan Detail</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('laporan-kas.index') }}"><i class="bi bi-journal-richtext"></i> Laporan Kas</a></li>
                @endif

                {{-- ======= KARYAWAN GUDANG: Produk, Keranjang, PO, Invoice, Laporan Stok ======= --}}
                @if ($user->role == 'karyawan_gudang')
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.create') }}"><i class="bi bi-plus-circle"></i> Tambah Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Lihat Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}"><i class="bi bi-receipt"></i> Lihat Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> Daftar Invoice</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchase-orders.index') }}"><i class="bi bi-truck"></i> Purchase Order</a></li>
                    <hr class="text-white w-75 mx-auto my-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('stock-report.index') }}"><i class="bi bi-box-seam-fill"></i> Laporan Stok</a></li>
                @endif

                {{-- ======= KARYAWAN MARKETING: Hanya Laporan Stok ======= --}}
                @if ($user->role == 'karyawan_marketing')
                    <li class="nav-item"><a class="nav-link" href="{{ route('stock-report.index') }}"><i class="bi bi-box-seam-fill"></i> Laporan Stok</a></li>
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

    <!-- ===== TOP NAVBAR ===== -->
    @auth
    <div class="top-navbar">
        <div class="navbar-greeting">
            <div class="greeting-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="greeting-text">
                <h6>Hai, {{ Auth::user()->name }} 👋</h6>
                <span>Selamat datang di PT MCA</span>
            </div>
        </div>
        <div class="navbar-info">
            <div class="navbar-clock">
                <i class="bi bi-calendar3"></i>
                <span id="navbarDate">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="navbar-divider"></div>
            <div class="navbar-clock">
                <i class="bi bi-clock"></i>
                <span id="navbarTime">{{ now()->format('H:i') }}</span>
            </div>
            <div class="navbar-divider"></div>
            <span class="navbar-badge badge-{{ Auth::user()->role }}">
                @php
                    $roleLabels = [
                        'owner' => 'Owner',
                        'admin' => 'Admin',
                        'karyawan_gudang' => 'Gudang',
                        'karyawan_marketing' => 'Marketing',
                    ];
                @endphp
                <i class="bi bi-shield-check"></i> {{ $roleLabels[Auth::user()->role] ?? Auth::user()->role }}
            </span>
            <div class="navbar-divider"></div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="navbar-quick-action" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </div>
    @endauth

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- jQuery + Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bootstrap JS Bundle (global) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Mobile Drawer Script -->
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

    <!-- Live Clock -->
    <script>
        (function() {
            const timeEl = document.getElementById('navbarTime');
            if (timeEl) {
                setInterval(function() {
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    timeEl.textContent = h + ':' + m + ':' + s;
                }, 1000);
            }
        })();
    </script>


    @yield('scripts')
</body>
</html>
