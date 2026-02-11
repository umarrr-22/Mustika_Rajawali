<?php
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    // Route admin lainnya...
});

Route::middleware(['auth', 'role:teknisi,admin'])->group(function () {
    Route::get('/service', [ServiceController::class, 'index']);
    // Route yang bisa diakses teknisi dan admin...
});