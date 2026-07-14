<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\LiveBeaconController;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\UserDashboardController;
use Illuminate\Support\Facades\Route;

// ── Halaman utama — landing page ─────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Live Beacon publik ────────────────────────────────────────────────────────
Route::get('/live/{token}', [LiveBeaconController::class, 'publicView'])
    ->name('live.beacon');

// ── Auth Web ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [WebAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[WebAuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── User Dashboard ────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/',        [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
});

// ── Admin Routes ──────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'is_admin'])
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Settings
        Route::get('settings',  [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings',  [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-ai', [AdminSettingController::class, 'testAi'])->name('settings.test-ai');

        // Users
        Route::get('users',                            [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}',                     [AdminUserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit',                [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}',                     [AdminUserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/ban',                [AdminUserController::class, 'ban'])->name('users.ban');
        Route::patch('users/{user}/unban',             [AdminUserController::class, 'unban'])->name('users.unban');
        Route::patch('users/{user}/toggle-admin',      [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::patch('users/{user}/toggle-verified',   [AdminUserController::class, 'toggleVerified'])->name('users.toggle-verified');
    });
