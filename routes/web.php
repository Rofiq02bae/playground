<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

// Beranda utama sementara diarahkan ke blog (Fase 1).
Route::redirect('/', '/blog');

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// ---------- Admin (updater) ----------
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function (): void {
        Route::redirect('/', '/admin/articles');
        Route::resource('articles', AdminArticleController::class)->except(['show']);
    });
});
