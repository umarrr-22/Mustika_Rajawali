<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Refil\{
    DashboardController,
    RefilMasukController,
    RefilProsesController,
    RefilSelesaiController
};

Route::prefix('refil')
    ->middleware(['auth', 'role:refil'])
    ->name('refil.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Refil Masuk
        Route::get('/refil-masuk', [RefilMasukController::class, 'index'])
            ->name('refil-masuk');
        Route::post('/refil-masuk/{refil}/proses', [RefilMasukController::class, 'proses'])
            ->name('refil-masuk.proses');
        Route::delete('/refil-masuk/{refil}//cancel', [RefilMasukController::class, 'cancel'])
            ->name('refil-masuk.cancel');
        Route::get('/refil-images/{filename}', [RefilMasukController::class, 'showImage'])
            ->name('refil-masuk.image');
        
        // Refil Proses
        Route::get('/refil-proses', [RefilProsesController::class, 'index'])
            ->name('refil-proses');
        Route::put('/refil-proses/{refil}', [RefilProsesController::class, 'update'])
            ->name('refil-proses.update');
        Route::post('/refil-proses/{refil}/complete', [RefilProsesController::class, 'complete'])
            ->name('refil-proses.complete');
        Route::delete('/refil-proses/{refil}/cancel', [RefilProsesController::class, 'cancel'])
            ->name('refil-proses.cancel');
        
        // Refil Selesai
        Route::get('/refil-selesai', [RefilSelesaiController::class, 'index'])
            ->name('refil-selesai');
        Route::delete('/refil-selesai/{id}/hapus', [RefilSelesaiController::class, 'hapus'])
            ->name('refil-selesai.hapus');
    });  