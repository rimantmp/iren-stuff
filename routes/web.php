<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BantuanAirController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisBantuanController;
use App\Http\Controllers\MasterWilayahController;
use App\Http\Controllers\ProgramBantuanController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::middleware('auth')->group(function (): void {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Bantuan Air (Fitur Utama)
    Route::prefix('bantuan/air')->name('bantuan.air.')->group(function (): void {
        Route::get('/', [BantuanAirController::class, 'index'])->name('index');
        Route::get('/tambah', [BantuanAirController::class, 'create'])->name('create');
        Route::post('/', [BantuanAirController::class, 'store'])->name('store');
        Route::get('/{id}', [BantuanAirController::class, 'show'])->name('show');
        Route::get('/{id}/cetak', [BantuanAirController::class, 'cetak'])->name('cetak');
        Route::get('/{id}/edit', [BantuanAirController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BantuanAirController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [BantuanAirController::class, 'updateStatus'])->name('status');
        Route::delete('/{id}', [BantuanAirController::class, 'destroy'])->name('destroy');
    });

    // Program Bantuan Lainnya (Coming Soon)
    Route::prefix('bantuan')->name('bantuan.')->group(function (): void {
        Route::get('/sembako', [ProgramBantuanController::class, 'sembako'])->name('sembako');
        Route::get('/tunai-gereja', [ProgramBantuanController::class, 'tunaiGereja'])->name('tunai-gereja');
        Route::get('/pengadaan', [ProgramBantuanController::class, 'pengadaan'])->name('pengadaan');
    });

    // Kelola Master Jenis Bantuan
    Route::prefix('bantuan/jenis')->name('bantuan.jenis.')->group(function (): void {
        Route::get('/', [JenisBantuanController::class, 'index'])->name('index');
        Route::post('/', [JenisBantuanController::class, 'store'])->name('store');
        Route::put('/{id}', [JenisBantuanController::class, 'update'])->name('update');
        Route::delete('/{id}', [JenisBantuanController::class, 'destroy'])->name('destroy');
    });

    // Master Data Wilayah
    Route::prefix('master')->name('master.')->group(function (): void {
        Route::get('/provinsi', [MasterWilayahController::class, 'provinsi'])->name('provinsi');
        Route::put('/provinsi/{id}', [MasterWilayahController::class, 'updateProvinsi'])->name('provinsi.update');

        Route::get('/kota', [MasterWilayahController::class, 'kota'])->name('kota');
        Route::put('/kota/{id}', [MasterWilayahController::class, 'updateKota'])->name('kota.update');

        Route::get('/kecamatan', [MasterWilayahController::class, 'kecamatan'])->name('kecamatan');
        Route::put('/kecamatan/{id}', [MasterWilayahController::class, 'updateKecamatan'])->name('kecamatan.update');

        Route::get('/kelurahan', [MasterWilayahController::class, 'kelurahan'])->name('kelurahan');
        Route::put('/kelurahan/{id}', [MasterWilayahController::class, 'updateKelurahan'])->name('kelurahan.update');
    });

    // Rekapitulasi & Laporan
    Route::prefix('rekap')->name('rekap.')->group(function (): void {
        Route::get('/', [RekapController::class, 'index'])->name('index');
        Route::get('/cetak', [RekapController::class, 'cetak'])->name('cetak');
        Route::get('/perbandingan', [RekapController::class, 'perbandingan'])->name('perbandingan');
        Route::get('/perbandingan/cetak', [RekapController::class, 'cetakPerbandingan'])->name('perbandingan.cetak');
        Route::get('/perbandingan/excel', [RekapController::class, 'exportPerbandinganExcel'])->name('perbandingan.excel');
    });

    // Manajemen Administrator
    Route::prefix('admin/users')->name('admin.users.')->group(function (): void {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/tambah', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    // AJAX Endpoint Select2 Wilayah
    Route::prefix('wilayah')->name('wilayah.')->group(function (): void {
        Route::get('/provinsi', [WilayahController::class, 'getProvinsi'])->name('provinsi');
        Route::get('/kota/{provinsiId}', [WilayahController::class, 'getKota'])->name('kota');
        Route::get('/kecamatan/{kotaId}', [WilayahController::class, 'getKecamatan'])->name('kecamatan');
        Route::get('/kelurahan/{kecamatanId}', [WilayahController::class, 'getKelurahan'])->name('kelurahan');
    });
});
