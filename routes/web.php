<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StafUnit\PengajuanController as StafPengajuanController;
use App\Http\Controllers\Kasium\{VerifikasiController, EksternalController, BkuController};
use App\Http\Controllers\Pimpinan\ApprovalController;
use App\Http\Controllers\Shared\{DashboardController, PengajuanDetailController, LaporanController, ArsipController};
use App\Http\Controllers\Settings\{UserController, UnitKerjaController, PermissionController};

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/', fn() => redirect()->route(auth()->check() ? 'dashboard.index' : 'login'));


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SHARED
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index')
        ->middleware('permission:dashboard.lihat');

    Route::get('pengajuan/{id}', [PengajuanDetailController::class, 'show'])
        ->name('pengajuan.show');

    Route::middleware('permission:arsip.lihat')
        ->group(function () {
            Route::get('arsip', [ArsipController::class, 'index'])->name('arsip.index');
        });

    Route::middleware('permission:laporan.export-pdf')
        ->prefix('laporan')
        ->name('laporan.')
        ->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('pdf/{program}', [LaporanController::class, 'exportPdf'])->name('pdf');
            Route::get('excel/{program}', [LaporanController::class, 'exportExcel'])->name('excel');
            Route::get('realisasi/pdf', [LaporanController::class, 'exportRealisasiPdf'])->name('realisasi-pdf');
            Route::get('realisasi/excel', [LaporanController::class, 'exportRealisasiExcel'])->name('realisasi-excel');
        });


    /*
    |--------------------------------------------------------------------------
    | STAF UNIT
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:pengajuan.create')
        ->prefix('staf')
        ->name('staf.')
        ->group(function () {
            Route::get('pengajuan', [StafPengajuanController::class, 'index'])->name('pengajuan.index');
            Route::get('pengajuan/create', [StafPengajuanController::class, 'create'])->name('pengajuan.create');
            Route::post('pengajuan', [StafPengajuanController::class, 'store'])->name('pengajuan.store');
            Route::get('pengajuan/{id}/edit', [StafPengajuanController::class, 'edit'])->name('pengajuan.edit');
            Route::put('pengajuan/{id}', [StafPengajuanController::class, 'update'])->name('pengajuan.update');
            Route::post('pengajuan/{id}/kirim', [StafPengajuanController::class, 'kirim'])
                ->name('pengajuan.kirim')
                ->middleware('permission:pengajuan.kirim');
        });


    /*
    |--------------------------------------------------------------------------
    | KASIUM
    |--------------------------------------------------------------------------
    */
    Route::prefix('kasium')
        ->name('kasium.')
        ->group(function () {

            // Verifikasi & Eksternal
            Route::middleware('permission:pengajuan.verifikasi')->group(function () {
                Route::get('verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
                Route::post('pengajuan/{id}/verifikasi', [VerifikasiController::class, 'verifikasi'])->name('verifikasi.proses');
                Route::post('pengajuan/{id}/tolak', [VerifikasiController::class, 'tolak'])->name('verifikasi.tolak');
                Route::post('pengajuan/{id}/ajukan-polrestabes', [EksternalController::class, 'ajukanPolrestabes'])->name('eksternal.ajukan');
                Route::post('pengajuan/{id}/dana-cair', [EksternalController::class, 'danaCair'])->name('eksternal.dana-cair');
            });

            // BKU
            Route::middleware('permission:bku.lihat')->group(function () {
                Route::get('bku', [BkuController::class, 'index'])->name('bku.index');
            });
            Route::middleware('permission:bku.input')->group(function () {
                Route::get('bku/create', [BkuController::class, 'create'])->name('bku.create');
                Route::post('bku', [BkuController::class, 'store'])->name('bku.store');
            });

            // Settings
            Route::prefix('settings')
                ->name('settings.')
                ->group(function () {

                    // CRUD Users
                    Route::middleware('permission:settings.users')->group(function () {
                        Route::resource('users', UserController::class)->except('show');
                    });

                    // CRUD Units
                    Route::middleware('permission:settings.units')->group(function () {
                        Route::resource('units', UnitKerjaController::class)->except('show');
                    });

                    // Permission Manager
                    Route::middleware('permission:settings.permissions')->group(function () {
                        Route::get('permissions/role', [PermissionController::class, 'roleIndex'])
                            ->name('permissions.role');
                        Route::put('permissions/role/{role}', [PermissionController::class, 'roleUpdate'])
                            ->name('permissions.role.update');
                        Route::get('permissions/user', [PermissionController::class, 'userIndex'])
                            ->name('permissions.user');
                        Route::get('permissions/user/{id}', [PermissionController::class, 'userShow'])
                            ->name('permissions.user.show');
                        Route::put('permissions/user/{id}', [PermissionController::class, 'userUpdate'])
                            ->name('permissions.user.update');
                        Route::post('permissions/user/{id}/reset', [PermissionController::class, 'userReset'])
                            ->name('permissions.user.reset');
                    });
                });
        });


    /*
    |--------------------------------------------------------------------------
    | PIMPINAN
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:pengajuan.setujui')
        ->prefix('pimpinan')
        ->name('pimpinan.')
        ->group(function () {
            Route::get('approval', [ApprovalController::class, 'index'])->name('approval.index');
            Route::post('pengajuan/{id}/setujui', [ApprovalController::class, 'setujui'])->name('approval.setujui');
            Route::post('pengajuan/{id}/tolak', [ApprovalController::class, 'tolak'])->name('approval.tolak');
        });
});
