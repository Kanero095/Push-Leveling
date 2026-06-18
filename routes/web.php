<?php

use App\Http\Controllers\CronController;
use App\Http\Controllers\OAuthController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Livewire\Admin\Settings;
use App\Livewire\Dashboard;
use App\Livewire\Leaderboard;
use App\Livewire\ManualLogs;
use App\Livewire\Notifications;
use App\Livewire\Workouts;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', function () {
    return response('OK', 200);
});

Route::redirect('/', '/login')->name('home');

// Simulated OAuth Login callback
Route::get('/login/oauth/{provider}', [OAuthController::class, 'handleMockLogin'])->name('oauth.login');

Route::get('/api/cron', [CronController::class, 'run'])->name('api.cron');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/notifications', Notifications::class)->name('notifications');
    Route::get('/workouts', Workouts::class)->name('workouts');
    Route::get('/manual-logs', ManualLogs::class)->name('manual-logs');
    Route::get('/leaderboard', Leaderboard::class)->name('leaderboard');

    // Admin settings route
    Route::middleware([EnsureUserIsAdmin::class])->group(function () {
        Route::get('/admin/settings', Settings::class)->name('admin.settings');
    });
});

require __DIR__.'/settings.php';
