<?php

use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\App\HomeController as AppHomeController;
use App\Http\Controllers\App\SawahController;
use App\Http\Controllers\BlogController;
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

        // Fase 2 — pencatatan (desktop view perusahaan)
        Route::resource('projects', AdminProjectController::class);
        Route::resource('services', AdminServiceController::class)->except(['show']);
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/projects/{project}/expenses', [AdminExpenseController::class, 'store'])->name('projects.expenses.store');
        Route::post('/expenses/{expense}/approve', [AdminExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('/expenses/{expense}/reject', [AdminExpenseController::class, 'reject'])->name('expenses.reject');
        Route::delete('/expenses/{expense}', [AdminExpenseController::class, 'destroy'])->name('expenses.destroy');
    });
});

// ---------- WebApp pencatatan (Fase 2): semua role yang login ----------
// Pemilik = view only, penggarap = view + add (approval perusahaan),
// admin (perusahaan) = semua akses termasuk webapp.
Route::prefix('app')->name('app.')->middleware('auth')->group(function (): void {
    Route::get('/', [AppHomeController::class, 'index'])->name('home');
    Route::get('/sawah', [SawahController::class, 'index'])->name('sawah.index');
    Route::get('/sawah/{project}', [SawahController::class, 'show'])->name('sawah.show');
    Route::post('/sawah/{project}/expenses', [SawahController::class, 'storeExpense'])->name('sawah.expenses.store');
});
