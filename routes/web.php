<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UrlParserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishController;
use Illuminate\Support\Facades\Route;

// Головна сторінка
Route::get('/', function () {
    return view('home');
})->name('home');

// Авторизація
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Захищені маршрути
Route::middleware('auth')->group(function () {
    // Wishes (бажання)
    Route::resource('wishes', WishController::class);

    // URL Parser API
    Route::post('/api/parse-url', [UrlParserController::class, 'parse'])->name('api.parse-url');
    Route::post('/api/download-image', [UrlParserController::class, 'downloadImage'])->name('api.download-image');

    // Профіль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Користувачі
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
});
