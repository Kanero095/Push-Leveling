<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAuthController;
use App\Livewire\Dashboard;
use App\Livewire\Workouts;
use App\Livewire\ManualLogs;
use App\Livewire\Leaderboard;

Route::redirect('/', '/login')->name('home');

// Simulated OAuth Login callback
Route::get('/login/oauth/{provider}', [OAuthController::class, 'handleMockLogin'])->name('oauth.login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/workouts', Workouts::class)->name('workouts');
    Route::get('/manual-logs', ManualLogs::class)->name('manual-logs');
    Route::get('/leaderboard', Leaderboard::class)->name('leaderboard');

    // Admin settings route
    Route::middleware([\App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
        Route::get('/admin/settings', \App\Livewire\Admin\Settings::class)->name('admin.settings');
    });
});

require __DIR__.'/settings.php';
