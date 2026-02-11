<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    TambahJadwalController,
    ServiceMasukController,
    ServiceProsesController,
    ServiceSelesaiController,
    RefilMasukController,
    RefilProsesController,
    RefilSelesaiController,
    JadwalSelesaiController,
    PengaturanAkunController
};

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
            
        // Schedule Management
        Route::prefix('tambah-jadwal')->group(function() {
            Route::get('/', [TambahJadwalController::class, 'index'])
                ->name('tambah-jadwal');
            Route::post('/', [TambahJadwalController::class, 'store'])
                ->name('tambah-jadwal.store');
            Route::get('/edit/{id}', [TambahJadwalController::class, 'edit'])
                ->name('tambah-jadwal.edit');
            Route::put('/{id}', [TambahJadwalController::class, 'update'])
                ->name('tambah-jadwal.update');
            Route::post('/{id}/kirim', [TambahJadwalController::class, 'kirim'])
                ->name('tambah-jadwal.kirim');
            Route::delete('/{id}', [TambahJadwalController::class, 'destroy'])
                ->name('tambah-jadwal.destroy');
            Route::get('/get-kurir', [TambahJadwalController::class, 'getKurirList'])
                ->name('tambah-jadwal.get-kurir');
        });

        // Service Routes
        Route::prefix('service')->group(function() {
            // Service Masuk
            Route::prefix('masuk')->group(function() {
                Route::get('/', [ServiceMasukController::class, 'index'])
                    ->name('service-masuk');
                Route::get('/images/{filename}', [ServiceMasukController::class, 'showImage'])
                    ->name('service-masuk.image');
            });

            // Service Proses
            Route::prefix('proses')->group(function() {
                Route::get('/', [ServiceProsesController::class, 'index'])
                    ->name('service-proses');
                Route::get('/teknisi/{teknisiId}', [ServiceProsesController::class, 'byTechnician'])
                    ->name('service-proses.teknisi');
                Route::get('/images/{filename}', [ServiceMasukController::class, 'showImage'])
                    ->name('service-proses.image');
            });

            // Service Selesai
            Route::prefix('selesai')->group(function() {
                Route::get('/', [ServiceSelesaiController::class, 'index'])
                    ->name('service-selesai');
                Route::get('/images/{filename}', [ServiceSelesaiController::class, 'showImage'])
                    ->name('service-selesai.image');
                Route::post('/{id}/toggle-verifikasi', [ServiceSelesaiController::class, 'toggleVerifikasi'])
                    ->name('service-selesai.toggle-verifikasi');
            });
        });

        // Refil Routes
        Route::prefix('refil')->group(function() {
            // Refil Masuk
            Route::prefix('masuk')->group(function() {
                Route::get('/', [RefilMasukController::class, 'index'])
                    ->name('refil-masuk');
                Route::get('/images/{filename}', [RefilMasukController::class, 'showImage'])
                    ->name('refil-masuk.image');
            });

            // Refil Proses
            Route::prefix('proses')->group(function() {
                Route::get('/', [RefilProsesController::class, 'index'])
                    ->name('refil-proses');
                Route::get('/images/{filename}', [RefilMasukController::class, 'showImage'])
                    ->name('refil-proses.image');
            });

            // Refil Selesai
            Route::prefix('selesai')->group(function() {
                Route::get('/', [RefilSelesaiController::class, 'index'])
                    ->name('refil-selesai');
                Route::get('/images/{filename}', [RefilSelesaiController::class, 'showImage'])
                    ->name('refil-selesai.image');
                Route::post('/{id}/toggle-verifikasi', [RefilSelesaiController::class, 'toggleVerifikasi'])
                    ->name('refil-selesai.toggle-verifikasi');
            });
        });
            
        // Completed Schedules
        Route::get('/jadwal-selesai', [JadwalSelesaiController::class, 'index'])
            ->name('jadwal-selesai');
            
        // Account Settings
        Route::prefix('pengaturan-akun')->group(function() {
            Route::get('/', [PengaturanAkunController::class, 'index'])
                ->name('pengaturan-akun');
            Route::post('/', [PengaturanAkunController::class, 'store'])
                ->name('pengaturan-akun.store');
            Route::put('/{id}', [PengaturanAkunController::class, 'update'])
                ->name('pengaturan-akun.update');
            Route::delete('/{id}', [PengaturanAkunController::class, 'destroy'])
                ->name('pengaturan-akun.destroy');
            
            // Additional account routes if needed
            Route::put('/update-password', [PengaturanAkunController::class, 'updatePassword'])
                ->name('pengaturan-akun.update-password');
        });
    });