<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Halaman Depan / Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');

// Autentikasi Admin
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.store');
});

// Panel Admin (Perlu Login)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Katalog Kain
    Route::get('/catalog', [AdminCatalogController::class, 'index'])->name('catalog.index');
    Route::post('/catalog', [AdminCatalogController::class, 'store'])->name('catalog.store');
    Route::put('/catalog/{catalog}', [AdminCatalogController::class, 'update'])->name('catalog.update');
    Route::delete('/catalog/{catalog}', [AdminCatalogController::class, 'destroy'])->name('catalog.destroy');
    Route::post('/catalog/{catalog}/move/{direction}', [AdminCatalogController::class, 'move'])->name('catalog.move');

    // Pengaturan Teks & Material (mendukung PUT sesuai form edit.blade.php)
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::match(['post', 'put'], '/settings', [SettingController::class, 'update'])->name('settings.update');
});
