<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AduanStaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('aduan.borang'));

// ── Portal Aduan Staff (tanpa login) ──────────────────────────
Route::get('/aduan', [AduanStaffController::class, 'borang'])->name('aduan.borang');
Route::post('/aduan', [AduanStaffController::class, 'hantar'])->name('aduan.hantar');
Route::get('/aduan/status/{tiket}', [AduanStaffController::class, 'semak'])->name('aduan.semak');

// ── Panel Admin ───────────────────────────────────────────────
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/',           [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/aduan',      fn () => view('admin.aduan'))->name('aduan');
    Route::get('/aliran',     fn () => view('admin.aliran'))->name('aliran');
    Route::get('/notifikasi', [AdminController::class, 'notifikasi'])->name('notifikasi');
    Route::get('/laporan',    fn () => view('admin.laporan'))->name('laporan');
    Route::get('/pengguna',   [AdminController::class, 'pengguna'])->name('pengguna');
    Route::get('/monitor',    fn () => view('admin.monitor'))->name('monitor');
});
