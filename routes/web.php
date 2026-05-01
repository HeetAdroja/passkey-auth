<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasskeyController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Passkey authentication routes
Route::post('/passkeys/authentication-options', \Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController::class)
    ->name('passkeys.authentication_options');
Route::post('/passkeys/authenticate', \Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController::class)
    ->name('passkeys.authenticate');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Auth protected routes
Route::middleware('auth')->group(function () {
    Route::get('/logs', [LogController::class, 'index'])->name('admin.logs.index');
    Route::post('/logs/clear', [LogController::class, 'clear'])->name('admin.logs.clear');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/clearcache', [ProfileController::class, 'clearcache'])->name('profile.clearcache');
    // Passkey registration
    Route::post('/passkeys/register/options', [PasskeyController::class, 'registerOptions'])->name('passkeys.register_options');
    Route::post('/passkeys/register/store', [PasskeyController::class, 'store'])->name('passkeys.store');
    Route::delete('/passkeys/{id}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');
});
