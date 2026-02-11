<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kurir\{
    DashboardController,
    JadwalHariIniController,
    ServiceMasukController,
    RefilMasukController,
    JadwalSelesaiController
};

Route::prefix('kurir')
    ->middleware(['auth', 'role:kurir'])
    ->name('kurir.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Jadwal Hari Ini
        Route::prefix('jadwal-hari-ini')->group(function() {
            Route::get('/', [JadwalHariIniController::class, 'index'])
                ->name('jadwal-hari-ini');
            Route::post('/{id}/selesai', [JadwalHariIniController::class, 'markAsDone'])
                ->name('jadwal-hari-ini.selesai');
            Route::delete('/{id}/hapus', [JadwalHariIniController::class, 'hapus'])
                ->name('jadwal-hari-ini.hapus');  
        });
        
        // Service Masuk
        Route::prefix('service-masuk')->group(function() {
            Route::get('/', [ServiceMasukController::class, 'index'])
                ->name('service-masuk');
            Route::post('/', [ServiceMasukController::class, 'store'])
                ->name('service-masuk.store');
            Route::put('/{service}', [ServiceMasukController::class, 'update'])
                ->name('service-masuk.update');
            Route::delete('/{service}', [ServiceMasukController::class, 'destroy'])
                ->name('service-masuk.destroy');
            Route::post('/{service}/kirim', [ServiceMasukController::class, 'kirim'])
                ->name('service-masuk.kirim');
            Route::get('/images/{filename}', [ServiceMasukController::class, 'showImage'])
                ->name('service-masuk.image');
        });
        
        // Refil Masuk
        Route::prefix('refil-masuk')->group(function() {
            Route::get('/', [RefilMasukController::class, 'index'])
                ->name('refil-masuk');
            Route::post('/', [RefilMasukController::class, 'store'])
                ->name('refil-masuk.store');
            Route::put('/{refil}', [RefilMasukController::class, 'update'])
                ->name('refil-masuk.update');
            Route::delete('/{refil}', [RefilMasukController::class, 'destroy'])
                ->name('refil-masuk.destroy');
            Route::post('/{refil}/kirim', [RefilMasukController::class, 'kirim'])
                ->name('refil-masuk.kirim');
            Route::get('/images/{filename}', [RefilMasukController::class, 'showImage'])
                ->name('refil-masuk.image');
        });
        
        // Jadwal Selesai
        Route::prefix('jadwal-selesai')->group(function() {
            Route::get('/', [JadwalSelesaiController::class, 'index'])
                ->name('jadwal-selesai');
            Route::delete('/{id}/hapus', [JadwalSelesaiController::class, 'destroy'])
                ->name('jadwal-selesai.hapus');
        });
    });