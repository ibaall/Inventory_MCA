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
        return redirect('http://transaksi.test:8080/products/create');
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

    // Produk
    Route::prefix('/products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
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

    // Keranjang
    Route::prefix('/cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
        Route::post('/set-discount/{cartKey}', [CartController::class, 'setDiscount'])->name('cart.setDiscount');
        Route::delete('/remove/{productId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
        Route::delete('/bulk-remove', [CartController::class, 'bulkRemove'])->name('cart.bulkRemove');
        Route::delete('/clear', [CartController::class, 'clearCart'])->name('cart.clear');
        Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('checkout.store');
    });

    // Invoice
    Route::prefix('/invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::post('/', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // Purchase Order
    Route::prefix('/purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/{id}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('/{id}/terima', [PurchaseOrderController::class, 'terima'])->name('purchase-orders.terima');
        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
        Route::get('/{id}/pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-orders.pdf');
        Route::get('/{id}/print', [PurchaseOrderController::class, 'printPo'])->name('purchase-orders.print');
    });

    // Laporan Keuangan Detail & Pembayaran
    Route::get('/laporan-keuangan-detail', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('financial-reports.index');
    Route::get('/laporan-keuangan-detail/cetak', [\App\Http\Controllers\FinancialReportController::class, 'printReport'])->name('financial-reports.cetak');
    Route::get('/laporan-keuangan-detail/pdf', [\App\Http\Controllers\FinancialReportController::class, 'exportPdf'])->name('financial-reports.pdf');
    Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{id}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');
});

require __DIR__.'/auth.php';
