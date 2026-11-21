<?php

use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\App\HomeController as AppHomeController;
use App\Http\Controllers\App\SawahController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Shop\AuthController as ShopAuthController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\MidtransWebhookController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Shop\RegionController as ShopRegionController;
use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Facades\Route;

// Beranda utama sementara diarahkan ke blog (Fase 1).
// Landing page root belum dibuat; showcase sewa menyusul bersama landing page.
Route::redirect('/', '/blog');

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// ---------- Marketplace (Fase 3): aplikasi toko terpisah, publik ----------
// Eksplor katalog tanpa login; checkout (tahap 3.2) wajib login.
// Rute auth didaftarkan SEBELUM wildcard {product} agar /produk/masuk tidak tertangkap slug.
Route::prefix('produk')->name('shop.')->group(function (): void {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/masuk', [ShopAuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [ShopAuthController::class, 'login'])->name('login.submit');
    Route::get('/daftar', [ShopAuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [ShopAuthController::class, 'register'])->name('register.submit');
    Route::post('/keluar', [ShopAuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Keranjang belanja (publik, berbasis session)
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/keranjang/ubah', [CartController::class, 'update'])->name('cart.update');
    Route::post('/keranjang/hapus/{product}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout & pesanan (wajib login; admin dikecualikan dari membeli)
    Route::middleware('auth')->group(function (): void {
        Route::get('/daerah', [ShopRegionController::class, 'children'])->name('regions.children');
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/ongkir', [CheckoutController::class, 'rates'])->name('checkout.rates');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/pesanan', [ShopOrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order:code}', [ShopOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order:code}/cek-status', [ShopOrderController::class, 'checkStatus'])->name('orders.check');
        Route::post('/pesanan/{order:code}/batal', [ShopOrderController::class, 'cancel'])->name('orders.cancel');
    });

    Route::get('/{product:slug}', [ShopController::class, 'show'])->name('show');
});

// Webhook notifikasi Midtrans (tanpa login; keaslian dari verifikasi signature).
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'handle'])->name('midtrans.notification');

// ---------- Login (satu pintu untuk admin, pemilik, penggarap) ----------
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ---------- Panel admin: khusus role admin (perusahaan) ----------
    Route::middleware(['auth', 'role:admin'])->group(function (): void {
        Route::redirect('/', '/admin/articles');
        Route::resource('articles', AdminArticleController::class)->except(['show']);
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::get('/regions/children', [RegionController::class, 'children'])->name('regions.children');

        // Fase 2 — pencatatan (desktop view perusahaan)
        Route::resource('projects', AdminProjectController::class);
        Route::resource('services', AdminServiceController::class)->except(['show']);

        // Fase 3 — marketplace (dikelola dari dashboard admin yang sama)
        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/proses', [AdminOrderController::class, 'process'])->name('orders.process');
        Route::post('/orders/{order}/kirim', [AdminOrderController::class, 'ship'])->name('orders.ship');
        Route::post('/orders/{order}/kirim-manual', [AdminOrderController::class, 'shipManual'])->name('orders.shipManual');
        Route::post('/orders/{order}/selesai', [AdminOrderController::class, 'complete'])->name('orders.complete');
        Route::post('/orders/{order}/batal', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/kalender', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/laporan', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/{project}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
        Route::post('/projects/{project}/agreement', [\App\Http\Controllers\Admin\AgreementController::class, 'store'])->name('projects.agreement.store');
        Route::get('/projects/{project}/agreement/download', [\App\Http\Controllers\Admin\AgreementController::class, 'download'])->name('projects.agreement.download');
        Route::post('/projects/{project}/harvests', [\App\Http\Controllers\Admin\HarvestController::class, 'store'])->name('projects.harvests.store');
        Route::delete('/harvests/{harvest}', [\App\Http\Controllers\Admin\HarvestController::class, 'destroy'])->name('harvests.destroy');
        Route::post('/projects/{project}/expenses', [AdminExpenseController::class, 'store'])->name('projects.expenses.store');
        Route::post('/expenses/{expense}/approve', [AdminExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [AdminExpenseController::class, 'reject'])->name('expenses.reject');
        Route::delete('/expenses/{expense}', [AdminExpenseController::class, 'destroy'])->name('expenses.destroy');
    });
});

// ---------- WebApp pencatatan (Fase 2): role pencatatan yang login ----------
// Pemilik = view only, penggarap = view + add (approval perusahaan),
// admin (perusahaan) = semua akses termasuk webapp.
// Pembeli marketplace (Fase 3) TIDAK masuk webapp — areanya adalah toko.
Route::prefix('app')->name('app.')->middleware(['auth', 'role:admin,pemilik,penggarap'])->group(function (): void {
    Route::get('/', [AppHomeController::class, 'index'])->name('home');
    Route::get('/sawah', [SawahController::class, 'index'])->name('sawah.index');
    Route::get('/sawah/{project}', [SawahController::class, 'show'])->name('sawah.show');
    Route::get('/kalender', [\App\Http\Controllers\App\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/surat-perjanjian', [\App\Http\Controllers\App\AgreementController::class, 'index'])->name('agreements.index');
    Route::get('/surat-perjanjian/{project}/unduh', [\App\Http\Controllers\App\AgreementController::class, 'download'])->name('agreements.download');
    Route::get('/laporan', [\App\Http\Controllers\App\ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/{project}', [\App\Http\Controllers\App\ReportController::class, 'show'])->name('reports.show');
    Route::post('/sawah/{project}/expenses', [SawahController::class, 'storeExpense'])->name('sawah.expenses.store');
});
