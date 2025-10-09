<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/auth/authentik', [AuthController::class, 'redirectToAuthentik'])->name('auth.authentik');
    Route::get('/auth/authentik/callback', [AuthController::class, 'handleAuthentikCallback'])->name('auth.authentik.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Захищені маршрути
Route::middleware('auth')->group(function () {
    // Wishes (бажання)
    Route::resource('wishes', WishController::class);

    // Профіль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Користувачі
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
});
