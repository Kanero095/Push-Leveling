<?php

use App\Livewire\Dashboard;
use App\Models\Mission;
use App\Models\User;
use App\Models\UserMission;
use App\Models\WeeklyProgress;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Test Athlete',
        'email' => 'athlete@example.com',
        'password' => bcrypt('password123'),
        'xp_total' => 0,
        'level' => 1,
        'user_level' => 'beginner',
    ]);

    // Create a mock mission
    $this->pushupMission = Mission::create([
        'name' => 'Push-up Daily',
        'type' => 'push_up',
        'target' => 100,
        'base_xp' => 100,
    ]);
});

test('guest is redirected to login', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can visit dashboard', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard'));
    $response->assertStatus(200);
});

test('dashboard component loads missions and user data', function () {
    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertSee('Test Athlete')
        ->assertSee('beginner Mode')
        ->assertSee('Daily Missions');

    // Verify user mission was created
    expect(UserMission::where('user_id', $this->user->id)->count())->toBe(1);
});

test('dashboard sandbox can simulate mission completion', function () {
    $this->user->update(['is_admin' => true]);

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('simulateMissionCompletion')
        ->assertDispatched('toast', variant: 'success', text: 'Simulated completing all missions for today!');

    $mission = UserMission::where('user_id', $this->user->id)->first();
    expect($mission->is_completed)->toBeTrue();
    expect($mission->current_progress)->toEqual($mission->target_snapshot);
});

test('dashboard sandbox can toggle hell mode status', function () {
    $this->user->update(['is_admin' => true]);

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('toggleHellModeReady')
        ->assertDispatched('toast', variant: 'info', text: 'Hell Mode status set to: READY');

    $weekStart = Carbon::today()->startOfWeek(Carbon::MONDAY)->toDateString();
    $progress = WeeklyProgress::where('user_id', $this->user->id)->where('week_start', $weekStart)->first();
    expect($progress->hell_mode_ready)->toBeTrue();
});

test('non-admin user cannot access dashboard sandbox simulation methods', function () {
    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('simulateMissionCompletion')
        ->assertStatus(403);
});
