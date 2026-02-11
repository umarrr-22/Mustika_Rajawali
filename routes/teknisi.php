<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teknisi\{
    DashboardController,
    ServiceMasukController,
    ServiceProsesController,
    ServiceSelesaiController
};

Route::prefix('teknisi')
    ->middleware(['auth', 'role:teknisi'])
    ->name('teknisi.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Service Masuk
        Route::get('/service-masuk', [ServiceMasukController::class, 'index'])->name('service-masuk');
        Route::post('/service-masuk/{service}/terima', [ServiceMasukController::class, 'terima'])->name('service-masuk.terima');
        Route::delete('/service-masuk/{service}/hapus', [ServiceMasukController::class, 'hapus'])->name('service-masuk.hapus');
        
        // Service Diproses
        Route::get('/service-proses', [ServiceProsesController::class, 'index'])->name('service-proses');
        Route::put('/service-proses/{service}', [ServiceProsesController::class, 'update'])->name('service-proses.update');
        Route::post('/service-proses/{service}/selesai', [ServiceProsesController::class, 'selesai'])->name('service-proses.selesai');
        Route::delete('/service-proses/{service}/hapus', [ServiceProsesController::class, 'hapus'])->name('service-proses.hapus');
        
        // Service Selesai
        Route::get('/service-selesai', [ServiceSelesaiController::class, 'index'])->name('service-selesai');
        Route::delete('/service-selesai/{service}/hapus', [ServiceSelesaiController::class, 'hapus'])->name('service-selesai.hapus');
        
        // Image Handling
        Route::get('/service-images/{filename}', [ServiceMasukController::class, 'showImage'])->name('service-masuk.image');
    });