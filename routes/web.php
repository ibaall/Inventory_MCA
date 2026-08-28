<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\BuktiKasController;
use App\Http\Controllers\NoPerkiraanController;
use App\Http\Controllers\BuktiBankController;
use App\Http\Controllers\LaporanKasController;
use App\Http\Controllers\JurnalKoreksiController;
use App\Http\Controllers\BukuBesarController;

// ===== LAPORAN & ORDER (tidak butuh auth karena sudah di resource) =====
Route::get('/laporan/export', [OrderController::class, 'exportExcel'])->name('laporan.export');
Route::get('/laporan-keuangan', [OrderController::class, 'laporanKeuangan'])->name('laporan.keuangan');

// ← TAMBAHAN BARU: Export Invoice PDF
Route::get('/orders/{id}/invoice/pdf', [OrderController::class, 'exportInvoicePdf'])->name('orders.invoice.pdf');
Route::get('/orders/{id}/invoice/print', [OrderController::class, 'printInvoice'])->name('orders.invoice.print');
Route::get('/orders/{id}/surat-jalan/pdf', [OrderController::class, 'exportSuratJalanPdf'])->name('orders.sj.pdf');
Route::get('/orders/{id}/surat-jalan/print', [OrderController::class, 'printSuratJalan'])->name('orders.sj.print');
// Bulk PDF export (gabung beberapa invoice ke satu PDF)
Route::get('/orders/bulk-pdf', [OrderController::class, 'exportBulkInvoicePdf'])->name('orders.bulk.pdf');
// Bulk detail (detail gabungan beberapa pesanan)
Route::get('/orders/bulk-detail', [OrderController::class, 'bulkDetail'])->name('orders.bulk.detail');
// Bulk print (cetak invoice gabungan)
Route::get('/orders/bulk-print', [OrderController::class, 'bulkPrint'])->name('orders.bulk.print');
// Resource orders (sudah include index, show, edit, update, destroy)
Route::resource('orders', OrderController::class);

// ===== HOME =====
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');

// ===== LOGOUT =====
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ===== HALAMAN UTAMA =====
Route::get('/', function (Request $request) {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'karyawan_marketing') {
            return redirect()->route('stock-report.index');
        }
        if ($role === 'admin') {
            return redirect()->route('products.index');
        }
        return redirect()->route('products.create');
    }
    return view('welcome');
});

// ===== DASHBOARD =====
Route::get('/dashboard', function () {
    return redirect('/home');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== ROUTE DENGAN AUTH =====
Route::middleware('auth')->group(function () {

    // Profil
    Route::prefix('/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // ===================================================================
    // PRODUK - Lihat (semua user kecuali karyawan_marketing)
    // ===================================================================
    Route::middleware('role:owner,admin,karyawan_gudang')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    });

    // Produk - Kelola (owner, karyawan_gudang)
    Route::middleware('role:owner,karyawan_gudang')->prefix('/products')->group(function () {
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Variant AJAX operations
        Route::post('/{product}/variants', [ProductController::class, 'storeVariant'])->name('products.variants.store');
        Route::put('/variants/{variant}', [ProductController::class, 'updateVariant'])->name('products.variants.update');
        Route::delete('/variants/{variant}', [ProductController::class, 'destroyVariant'])->name('products.variants.destroy');
    });

    // ===================================================================
    // KERANJANG (owner, admin, karyawan_gudang)
    // ===================================================================
    Route::middleware('role:owner,admin,karyawan_gudang')->prefix('/cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
        Route::post('/set-discount/{cartKey}', [CartController::class, 'setDiscount'])->name('cart.setDiscount');
        Route::delete('/remove/{productId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
        Route::delete('/bulk-remove', [CartController::class, 'bulkRemove'])->name('cart.bulkRemove');
        Route::delete('/clear', [CartController::class, 'clearCart'])->name('cart.clear');
        Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('checkout.store');
    });

    // ===================================================================
    // INVOICE (owner, admin, karyawan_gudang)
    // ===================================================================
    Route::middleware('role:owner,admin,karyawan_gudang')->prefix('/invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::post('/', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // ===================================================================
    // PURCHASE ORDER (owner, admin, karyawan_gudang)
    // ===================================================================
    Route::middleware('role:owner,admin,karyawan_gudang')->prefix('/purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('/{id}/terima', [PurchaseOrderController::class, 'terima'])->name('purchase-orders.terima');
        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
        Route::get('/{id}/pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-orders.pdf');
        Route::get('/{id}/print', [PurchaseOrderController::class, 'printPo'])->name('purchase-orders.print');
    });

    // ===================================================================
    // MASTER DATA (owner, admin)
    // ===================================================================
    Route::middleware('role:owner,admin')->prefix('master-data')->group(function () {
        Route::get('/', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master-data.index');

        // Supplier CRUD
        Route::post('/supplier', [\App\Http\Controllers\MasterDataController::class, 'storeSupplier'])->name('master-data.supplier.store');
        Route::put('/supplier/{supplier}', [\App\Http\Controllers\MasterDataController::class, 'updateSupplier'])->name('master-data.supplier.update');
        Route::delete('/supplier/{supplier}', [\App\Http\Controllers\MasterDataController::class, 'destroySupplier'])->name('master-data.supplier.destroy');

        // Customer CRUD
        Route::post('/customer', [\App\Http\Controllers\MasterDataController::class, 'storeCustomer'])->name('master-data.customer.store');
        Route::put('/customer/{customer}', [\App\Http\Controllers\MasterDataController::class, 'updateCustomer'])->name('master-data.customer.update');
        Route::delete('/customer/{customer}', [\App\Http\Controllers\MasterDataController::class, 'destroyCustomer'])->name('master-data.customer.destroy');

        // JSON API for dropdowns
        Route::get('/api/suppliers', [\App\Http\Controllers\MasterDataController::class, 'getSuppliers'])->name('master-data.api.suppliers');
        Route::get('/api/customers', [\App\Http\Controllers\MasterDataController::class, 'getCustomers'])->name('master-data.api.customers');
    });

    // ===================================================================
    // BUKTI KAS HARIAN - BKK & BKM (owner, admin)
    // ===================================================================
    Route::middleware('role:owner,admin')->prefix('bukti-kas')->group(function () {
        // BKK - Bukti Kas Keluar
        Route::get('/bkk', [BuktiKasController::class, 'indexBkk'])->name('bukti-kas.bkk.index');
        Route::get('/bkk/create', [BuktiKasController::class, 'createBkk'])->name('bukti-kas.bkk.create');

        // BKM - Bukti Kas Masuk
        Route::get('/bkm', [BuktiKasController::class, 'indexBkm'])->name('bukti-kas.bkm.index');
        Route::get('/bkm/create', [BuktiKasController::class, 'createBkm'])->name('bukti-kas.bkm.create');

        // Shared CRUD routes
        Route::post('/', [BuktiKasController::class, 'store'])->name('bukti-kas.store');
        Route::get('/{id}', [BuktiKasController::class, 'show'])->name('bukti-kas.show');
        Route::get('/{id}/edit', [BuktiKasController::class, 'edit'])->name('bukti-kas.edit');
        Route::put('/{id}', [BuktiKasController::class, 'update'])->name('bukti-kas.update');
        Route::delete('/{id}', [BuktiKasController::class, 'destroy'])->name('bukti-kas.destroy');
        Route::get('/{id}/print', [BuktiKasController::class, 'print'])->name('bukti-kas.print');

        // Konfirmasi (Owner only)
        Route::middleware('role:owner')->post('/{id}/konfirmasi', [BuktiKasController::class, 'konfirmasi'])->name('bukti-kas.konfirmasi');
    });

    // ===================================================================
    // LAPORAN STOK (owner, karyawan_gudang, karyawan_marketing)
    // ===================================================================
    Route::middleware('role:owner,karyawan_gudang,karyawan_marketing')->group(function () {
        Route::get('/laporan-stok', [StockReportController::class, 'index'])->name('stock-report.index');
        Route::get('/laporan-stok/export', [StockReportController::class, 'exportExcel'])->name('stock-report.export');
    });

    // ===================================================================
    // LAPORAN KEUANGAN DETAIL & PEMBAYARAN (owner, admin)
    // ===================================================================
    Route::middleware('role:owner,admin')->group(function () {
        Route::get('/laporan-keuangan-detail', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('financial-reports.index');
        Route::get('/laporan-keuangan-detail/cetak', [\App\Http\Controllers\FinancialReportController::class, 'cetak'])->name('financial-reports.cetak');
        Route::get('/laporan-keuangan-detail/pdf', [\App\Http\Controllers\FinancialReportController::class, 'pdf'])->name('financial-reports.pdf');
        Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/payments/{id}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // ===================================================================
    // LAPORAN KAS (owner, admin)
    // ===================================================================
    Route::middleware('role:owner,admin')->group(function () {
        Route::get('/laporan-kas', [LaporanKasController::class, 'index'])->name('laporan-kas.index');
        Route::get('/laporan-kas/cetak', [LaporanKasController::class, 'cetak'])->name('laporan-kas.cetak');
    });

    // ===================================================================
    // BUKU BESAR (owner only)
    // ===================================================================
    Route::middleware('role:owner')->group(function () {
        Route::get('/buku-besar', [BukuBesarController::class, 'index'])->name('buku-besar.index');
        Route::get('/buku-besar/cetak', [BukuBesarController::class, 'cetak'])->name('buku-besar.cetak');
    });

    // ===================================================================
    // BUKTI BANK - BBM & BBK (owner only)
    // ===================================================================
    Route::middleware('role:owner')->prefix('bukti-bank')->group(function () {
        // BBM - Bukti Bank Masuk
        Route::get('/masuk', [BuktiBankController::class, 'indexMasuk'])->name('bukti-bank.masuk.index');
        Route::get('/masuk/create', [BuktiBankController::class, 'createMasuk'])->name('bukti-bank.masuk.create');

        // BBK - Bukti Bank Keluar
        Route::get('/keluar', [BuktiBankController::class, 'indexKeluar'])->name('bukti-bank.keluar.index');
        Route::get('/keluar/create', [BuktiBankController::class, 'createKeluar'])->name('bukti-bank.keluar.create');

        // Shared CRUD routes
        Route::post('/', [BuktiBankController::class, 'store'])->name('bukti-bank.store');
        Route::get('/{id}', [BuktiBankController::class, 'show'])->name('bukti-bank.show');
        Route::get('/{id}/edit', [BuktiBankController::class, 'edit'])->name('bukti-bank.edit');
        Route::put('/{id}', [BuktiBankController::class, 'update'])->name('bukti-bank.update');
        Route::delete('/{id}', [BuktiBankController::class, 'destroy'])->name('bukti-bank.destroy');
        Route::get('/{id}/cetak', [BuktiBankController::class, 'cetak'])->name('bukti-bank.cetak');

        // Konfirmasi (Owner only)
        Route::post('/{id}/konfirmasi', [BuktiBankController::class, 'konfirmasi'])->name('bukti-bank.konfirmasi');

        // API endpoints
        Route::get('/api/invoices', [BuktiBankController::class, 'apiInvoices'])->name('bukti-bank.api.invoices');
        Route::get('/api/purchase-orders', [BuktiBankController::class, 'apiPurchaseOrders'])->name('bukti-bank.api.pos');
    });

    // ===================================================================
    // JURNAL KOREKSI (owner only)
    // ===================================================================
    Route::middleware('role:owner')->prefix('jurnal-koreksi')->group(function () {
        Route::get('/', [JurnalKoreksiController::class, 'index'])->name('jurnal-koreksi.index');
        Route::get('/create', [JurnalKoreksiController::class, 'create'])->name('jurnal-koreksi.create');
        Route::post('/', [JurnalKoreksiController::class, 'store'])->name('jurnal-koreksi.store');
        Route::get('/{id}', [JurnalKoreksiController::class, 'show'])->name('jurnal-koreksi.show');
        Route::get('/{id}/edit', [JurnalKoreksiController::class, 'edit'])->name('jurnal-koreksi.edit');
        Route::put('/{id}', [JurnalKoreksiController::class, 'update'])->name('jurnal-koreksi.update');
        Route::delete('/{id}', [JurnalKoreksiController::class, 'destroy'])->name('jurnal-koreksi.destroy');
        Route::post('/{id}/konfirmasi', [JurnalKoreksiController::class, 'konfirmasi'])->name('jurnal-koreksi.konfirmasi');
    });

    // ===================================================================
    // NO. PERKIRAAN
    // ===================================================================
    // View only (owner, admin)
    Route::middleware('role:owner,admin')->prefix('no-perkiraan')->group(function () {
        Route::get('/', [NoPerkiraanController::class, 'index'])->name('no-perkiraan.index');
    });
    // CRUD (owner only)
    Route::middleware('role:owner')->prefix('no-perkiraan')->group(function () {
        Route::get('/create', [NoPerkiraanController::class, 'create'])->name('no-perkiraan.create');
        Route::post('/', [NoPerkiraanController::class, 'store'])->name('no-perkiraan.store');
        Route::get('/{id}/edit', [NoPerkiraanController::class, 'edit'])->name('no-perkiraan.edit');
        Route::put('/{id}', [NoPerkiraanController::class, 'update'])->name('no-perkiraan.update');
        Route::delete('/{id}', [NoPerkiraanController::class, 'destroy'])->name('no-perkiraan.destroy');
    });

    // API No. Perkiraan (JSON for dropdowns)
    Route::get('/api/no-perkiraan', [NoPerkiraanController::class, 'apiList'])->name('no-perkiraan.api');

    // ===================================================================
    // KELOLA AKUN (owner only)
    // ===================================================================
    Route::middleware('role:owner')->prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.resetPassword');
    });

    // ===================================================================
    // LOGIN TRACKER (owner only)
    // ===================================================================
    Route::middleware('role:owner')->group(function () {
        Route::get('/login-tracker', function (\Illuminate\Http\Request $request) {
            $query = \App\Models\LoginLog::with('user:id,name,role')
                ->orderByDesc('logged_in_at');

            // Filter by user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('logged_in_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('logged_in_at', '<=', $request->date_to);
            }

            $logs = $query->paginate(25)->withQueryString();
            $users = \App\Models\User::orderBy('name')->get(['id', 'name', 'role']);

            return view('login-tracker.index', compact('logs', 'users'));
        })->name('login-tracker.index');
    });
});

require __DIR__.'/auth.php';
