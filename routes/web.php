<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KomoditasController as AdminKomoditasController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Pembeli\DashboardController as PembeliDashboardController;
use App\Http\Controllers\Pembeli\JelajahSurplusController as PembeliJelajahController;
use App\Http\Controllers\Pembeli\KebutuhanController as PembeliKebutuhanController;
use App\Http\Controllers\Pembeli\PenawaranController as PembeliPenawaranController;
use App\Http\Controllers\Pembeli\PenyaluranController as PembeliPenyaluranController;
use App\Http\Controllers\Petani\DashboardController as PetaniDashboardController;
use App\Http\Controllers\Petani\PenawaranController as PetaniPenawaranController;
use App\Http\Controllers\Petani\PenyaluranController as PetaniPenyaluranController;
use App\Http\Controllers\Petani\RencanaPanenController as PetaniRencanaPanenController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/api/simulasi', [HomeController::class, 'hitungSimulasi'])->name('api.simulasi');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/demo/{role}', [AuthController::class, 'demoLogin'])->name('auth.demo');

// Petani routes
Route::middleware(['auth', 'role:petani'])->prefix('petani')->name('petani.')->group(function () {
    Route::get('/dashboard', [PetaniDashboardController::class, 'index'])->name('dashboard');

    // Rencana Panen
    Route::get('/rencana-panen', [PetaniRencanaPanenController::class, 'index'])->name('rencana-panen.index');
    Route::get('/rencana-panen/create', [PetaniRencanaPanenController::class, 'create'])->name('rencana-panen.create');
    Route::post('/rencana-panen', [PetaniRencanaPanenController::class, 'store'])->name('rencana-panen.store');
    Route::get('/rencana-panen/{id}', [PetaniRencanaPanenController::class, 'show'])->name('rencana-panen.show');
    Route::get('/rencana-panen/{id}/edit', [PetaniRencanaPanenController::class, 'edit'])->name('rencana-panen.edit');
    Route::put('/rencana-panen/{id}', [PetaniRencanaPanenController::class, 'update'])->name('rencana-panen.update');
    Route::delete('/rencana-panen/{id}', [PetaniRencanaPanenController::class, 'destroy'])->name('rencana-panen.destroy');

    // Penawaran
    Route::get('/penawaran', [PetaniPenawaranController::class, 'index'])->name('penawaran.index');
    Route::post('/penawaran/ajukan', [PetaniPenawaranController::class, 'ajukan'])->name('penawaran.ajukan');
    Route::post('/penawaran/{id}/terima', [PetaniPenawaranController::class, 'terima'])->name('penawaran.terima');
    Route::post('/penawaran/{id}/tolak', [PetaniPenawaranController::class, 'tolak'])->name('penawaran.tolak');

    // Penyaluran Realisasi
    Route::get('/penyaluran', [PetaniPenyaluranController::class, 'index'])->name('penyaluran.index');
    Route::get('/penyaluran/catat/{penawaranId}', [PetaniPenyaluranController::class, 'formCatat'])->name('penyaluran.formCatat');
    Route::post('/penyaluran/catat/{penawaranId}', [PetaniPenyaluranController::class, 'simpanCatat'])->name('penyaluran.simpanCatat');
});

// Pembeli (Pedagang & UMKM) routes
Route::middleware(['auth', 'role:pedagang,umkm'])->prefix('pembeli')->name('pembeli.')->group(function () {
    Route::get('/dashboard', [PembeliDashboardController::class, 'index'])->name('dashboard');

    // Kebutuhan
    Route::get('/kebutuhan', [PembeliKebutuhanController::class, 'index'])->name('kebutuhan.index');
    Route::get('/kebutuhan/create', [PembeliKebutuhanController::class, 'create'])->name('kebutuhan.create');
    Route::post('/kebutuhan', [PembeliKebutuhanController::class, 'store'])->name('kebutuhan.store');
    Route::get('/kebutuhan/{id}/edit', [PembeliKebutuhanController::class, 'edit'])->name('kebutuhan.edit');
    Route::put('/kebutuhan/{id}', [PembeliKebutuhanController::class, 'update'])->name('kebutuhan.update');

    // Jelajah Surplus Pre-Harvest
    Route::get('/jelajah-surplus', [PembeliJelajahController::class, 'index'])->name('jelajah-surplus');
    Route::get('/jelajah-surplus/{id}', [PembeliJelajahController::class, 'detail'])->name('jelajah-surplus.detail');

    // Penawaran
    Route::get('/penawaran', [PembeliPenawaranController::class, 'index'])->name('penawaran.index');
    Route::post('/penawaran/buat', [PembeliPenawaranController::class, 'buat'])->name('penawaran.buat');
    Route::post('/penawaran/{id}/batalkan', [PembeliPenawaranController::class, 'batalkan'])->name('penawaran.batalkan');

    // Penyaluran Diterima & Rating
    Route::get('/penyaluran', [PembeliPenyaluranController::class, 'index'])->name('penyaluran.index');
    Route::post('/penyaluran/{id}/konfirmasi', [PembeliPenyaluranController::class, 'konfirmasi'])->name('penyaluran.konfirmasi');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Komoditas Master Data
    Route::get('/komoditas', [AdminKomoditasController::class, 'index'])->name('komoditas.index');
    Route::get('/komoditas/create', [AdminKomoditasController::class, 'create'])->name('komoditas.create');
    Route::post('/komoditas', [AdminKomoditasController::class, 'store'])->name('komoditas.store');
    Route::get('/komoditas/{id}/edit', [AdminKomoditasController::class, 'edit'])->name('komoditas.edit');
    Route::put('/komoditas/{id}', [AdminKomoditasController::class, 'update'])->name('komoditas.update');
    Route::delete('/komoditas/{id}', [AdminKomoditasController::class, 'destroy'])->name('komoditas.destroy');

    // User Management & Verification
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/verifikasi', [AdminUserController::class, 'verifikasi'])->name('users.verifikasi');

    // Laporan & Audit Log
    Route::get('/laporan/sdg', [AdminLaporanController::class, 'laporanSdg'])->name('laporan.sdg');
    Route::get('/laporan/audit-log', [AdminLaporanController::class, 'auditLog'])->name('laporan.audit-log');
});
