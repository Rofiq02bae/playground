<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

// Fase 1 - Blog sisi viewer (updater/admin menyusul)
Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
