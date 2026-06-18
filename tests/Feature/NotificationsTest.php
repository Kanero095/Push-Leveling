<?php

use App\Livewire\Notifications;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->originalCronSecret = $_ENV['CRON_SECRET'] ?? env('CRON_SECRET');
    $this->user = User::factory()->create([
        'name' => 'Athlete Test',
        'email' => 'athlete@example.com',
    ]);
});

afterEach(function () {
    if ($this->originalCronSecret !== null) {
        $_ENV['CRON_SECRET'] = $this->originalCronSecret;
        $_SERVER['CRON_SECRET'] = $this->originalCronSecret;
        putenv("CRON_SECRET={$this->originalCronSecret}");
    } else {
        unset($_ENV['CRON_SECRET']);
        unset($_SERVER['CRON_SECRET']);
        putenv('CRON_SECRET=');
    }
});

test('guest is redirected to login for notifications page', function () {
    $response = $this->get(route('notifications'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can visit notifications page', function () {
    $response = $this->actingAs($this->user)->get(route('notifications'));
    $response->assertStatus(200);
});

test('cron webhook without CRON_SECRET configured returns 500', function () {
    $_ENV['CRON_SECRET'] = '';
    $_SERVER['CRON_SECRET'] = '';
    putenv('CRON_SECRET='); // Clear secret

    $response = $this->getJson('/api/cron?token=some_token');
    $response->assertStatus(500)
        ->assertJsonFragment(['status' => 'error', 'message' => 'CRON_SECRET is not configured on the server.']);
});

test('cron webhook with invalid token returns 403', function () {
    $_ENV['CRON_SECRET'] = 'super_secret_token';
    $_SERVER['CRON_SECRET'] = 'super_secret_token';
    putenv('CRON_SECRET=super_secret_token');

    $response = $this->getJson('/api/cron?token=wrong_token');
    $response->assertStatus(403)
        ->assertJsonFragment(['status' => 'error', 'message' => 'Unauthorized.']);
});

test('cron webhook with valid token run schedule:run returns success', function () {
    $_ENV['CRON_SECRET'] = 'super_secret_token';
    $_SERVER['CRON_SECRET'] = 'super_secret_token';
    putenv('CRON_SECRET=super_secret_token');

    $response = $this->getJson('/api/cron?token=super_secret_token');
    $response->assertStatus(200)
        ->assertJsonFragment(['status' => 'success', 'command' => 'schedule:run']);
});

test('cron webhook with unwhitelisted command returns 400', function () {
    $_ENV['CRON_SECRET'] = 'super_secret_token';
    $_SERVER['CRON_SECRET'] = 'super_secret_token';
    putenv('CRON_SECRET=super_secret_token');

    $response = $this->getJson('/api/cron?token=super_secret_token&command=migrate:fresh');
    $response->assertStatus(400)
        ->assertJsonFragment(['status' => 'error', 'message' => 'Command not allowed.']);
});

test('notifications component can mark a notification as read', function () {
    $notif = Notification::create([
        'user_id' => $this->user->id,
        'type' => 'morning',
        'message' => 'Latihan pagi!',
        'status' => 'sent',
        'is_read' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(Notifications::class)
        ->call('markAsRead', $notif->id)
        ->assertDispatched('toast', variant: 'success', text: 'Notifikasi ditandai sebagai dibaca.');

    expect($notif->fresh()->is_read)->toBeTrue();
});

test('notifications component can mark all notifications as read', function () {
    Notification::create([
        'user_id' => $this->user->id,
        'type' => 'morning',
        'message' => 'Latihan pagi!',
        'status' => 'sent',
        'is_read' => false,
    ]);

    Notification::create([
        'user_id' => $this->user->id,
        'type' => 'evening',
        'message' => 'Latihan malam!',
        'status' => 'sent',
        'is_read' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(Notifications::class)
        ->call('markAllAsRead')
        ->assertDispatched('toast', variant: 'success', text: 'Semua notifikasi ditandai sebagai dibaca.');

    expect(Notification::where('user_id', $this->user->id)->where('is_read', false)->count())->toBe(0);
});

test('notifications component can delete a notification', function () {
    $notif = Notification::create([
        'user_id' => $this->user->id,
        'type' => 'morning',
        'message' => 'Latihan pagi!',
        'status' => 'sent',
        'is_read' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(Notifications::class)
        ->call('deleteNotification', $notif->id)
        ->assertDispatched('toast', variant: 'info', text: 'Notifikasi berhasil dihapus.');

    expect(Notification::find($notif->id))->toBeNull();
});

test('notifications component can clear all notifications', function () {
    Notification::create([
        'user_id' => $this->user->id,
        'type' => 'morning',
        'message' => 'Latihan pagi!',
        'status' => 'sent',
        'is_read' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test(Notifications::class)
        ->call('clearAll')
        ->assertDispatched('toast', variant: 'info', text: 'Semua riwayat notifikasi dihapus.');

    expect(Notification::where('user_id', $this->user->id)->count())->toBe(0);
});
