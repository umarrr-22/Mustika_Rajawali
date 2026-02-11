<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Berikut adalah rute web untuk aplikasi Mustika Rajawali
| Semua rute akan dimuat oleh RouteServiceProvider
|
*/

// Rute Publik
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rute Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.post');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Rute Terproteksi (Harus Login)
Route::middleware(['auth'])->group(function () {
    // Rute Umum untuk Semua Role
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile');
    
    // Include route files for each role
    require __DIR__.'/admin.php';
    require __DIR__.'/teknisi.php';
    require __DIR__.'/kurir.php';
    require __DIR__.'/refil.php';
});

// Rute Fallback untuk 404
Route::fallback(function () {
    return view('errors.404');
})->name('404');