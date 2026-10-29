<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Halaman Depan / Publik
Route::get('/', [HomeController::class, 'index'])->name('home');

// Autentikasi Admin
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.store');
});

// Panel Admin (Perlu Login)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Pengaturan Teks & Material (mendukung PUT sesuai form edit.blade.php)
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::match(['post', 'put'], '/settings', [SettingController::class, 'update'])->name('settings.update');

    // Galeri Foto
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::put('/gallery/{photo}', [GalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/gallery/{photo}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::post('/gallery/{photo}/move/{direction}', [GalleryController::class, 'move'])->name('gallery.move');
});
