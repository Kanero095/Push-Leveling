<?php

use App\Models\Mission;
use App\Models\User;
use App\Models\UserMission;
use App\Models\WeeklyProgress;
use App\Models\Workout;
use App\Services\DailyResetService;
use App\Services\WorkoutService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed basic workouts & missions
    $this->pushupWorkout = Workout::create([
        'name' => 'Knee Push-ups (Beginner)',
        'difficulty' => 'beginner',
        'type' => 'push_up',
        'reps_label' => 'reps',
        'duration_label' => 'seconds',
    ]);

    $this->runWorkout = Workout::create([
        'name' => 'Sprint (Advanced)',
        'difficulty' => 'advanced',
        'type' => 'run',
        'reps_label' => 'kilometers',
        'duration_label' => 'seconds',
    ]);

    $this->pushupMission = Mission::create([
        'name' => 'Push-up Daily',
        'type' => 'push_up',
        'target' => 100,
        'base_xp' => 100,
    ]);

    $this->runMission = Mission::create([
        'name' => 'Running Daily',
        'type' => 'run',
        'target' => 10,
        'base_xp' => 150,
    ]);

    $this->user = User::create([
        'name' => 'Test Athlete',
        'email' => 'athlete@example.com',
        'password' => bcrypt('password123'),
        'xp_total' => 0,
        'level' => 1,
        'user_level' => 'beginner',
    ]);
});

test('xp is calculated correctly based on reps, duration and difficulty', function () {
    $workoutService = new WorkoutService;

    // 20 reps, 60 seconds (1 minute), Beginner workout (1.0x multiplier)
    // Formula: (20 * 0.5 + 1.0 * 0.2) * 1.0 = (10 + 0.2) * 1.0 = 10.2 => round to 10
    $result = $workoutService->completeWorkout($this->user, $this->pushupWorkout->id, 20, 60);

    expect($result['workout_xp'])->toBe(10);
    expect($this->user->fresh()->xp_total)->toBe(30); // 10 workout XP + 20 daily mission partial XP
});

test('advanced difficulty workout has 2.0x multiplier applied to XP', function () {
    $workoutService = new WorkoutService;

    // 5 reps (km), 600 seconds (10 minutes), Advanced workout (2.0x multiplier)
    // Formula: (5 * 0.5 + 10.0 * 0.2) * 2.0 = (2.5 + 2) * 2.0 = 4.5 * 2.0 = 9 => 9 XP
    $result = $workoutService->completeWorkout($this->user, $this->runWorkout->id, 5, 600);

    expect($result['workout_xp'])->toBe(9);
});

test('user levels up when crossing total XP threshold', function () {
    // Level 1 threshold to 2: 100 * 1^1.5 = 100 XP
    expect($this->user->level)->toBe(1);

    $this->user->addXP(99);
    expect($this->user->fresh()->level)->toBe(1);

    // Cross the 100 XP threshold
    $result = $this->user->addXP(1);
    expect($result['leveled_up'])->toBeTrue();
    expect($this->user->fresh()->level)->toBe(2);

    // Level 2 threshold to 3: 100 * 2^1.5 = 283 XP
    $this->user->addXP(182); // total 282
    expect($this->user->fresh()->level)->toBe(2);

    $this->user->addXP(1); // total 283
    expect($this->user->fresh()->level)->toBe(3);
});

test('workout updates corresponding daily mission and awards partial XP', function () {
    $workoutService = new WorkoutService;
    $workoutService->ensureMissionsExistForToday($this->user);

    // Initial state: push-up mission progress is 0/100, base_xp = 100
    // User does a workout of 20 pushups (Beginner = 10 XP)
    // Mission progress increases by 20/100 (20% progress)
    // Partial mission XP should be: round(100 * (20 / 100)) - round(100 * (0 / 100)) = 20 XP
    $result = $workoutService->completeWorkout($this->user, $this->pushupWorkout->id, 20, 60);

    expect($result['workout_xp'])->toBe(10);
    expect($result['mission_xp'])->toBe(20);
    expect($result['total_xp_gained'])->toBe(30);

    $userMission = UserMission::where('user_id', $this->user->id)
        ->whereDate('date', Carbon::today())
        ->where('mission_id', $this->pushupMission->id)
        ->first();

    expect($userMission->current_progress)->toBe(20.0);
    expect($userMission->is_completed)->toBeFalse();

    // Do another 80 pushups to complete the mission
    // Old progress = 20, new progress = 100
    // Old mission XP awarded = 20, new mission XP awarded = 100
    // Delta mission XP = 80 XP
    // Workout XP: (80 * 0.5 + 2 minutes * 0.2) * 1.0 = (40 + 0.4) * 1.0 = 40.4 => 40 XP
    $result2 = $workoutService->completeWorkout($this->user, $this->pushupWorkout->id, 80, 120);

    expect($result2['workout_xp'])->toBe(40);
    expect($result2['mission_xp'])->toBe(80);
    expect($result2['total_xp_gained'])->toBe(120);

    expect($userMission->fresh()->current_progress)->toBe(100.0);
    expect($userMission->fresh()->is_completed)->toBeTrue();
});

test('mission progress and partial XP is capped at 100% target snapshot', function () {
    $workoutService = new WorkoutService;
    $workoutService->ensureMissionsExistForToday($this->user);

    // User does a huge pushup session of 150 pushups (target is 100)
    // Progress capped at 100. Partial XP awarded: round(100 * (100/100)) = 100 XP.
    $result = $workoutService->completeWorkout($this->user, $this->pushupWorkout->id, 150, 300);

    expect($result['mission_xp'])->toBe(100);

    $userMission = UserMission::where('user_id', $this->user->id)
        ->whereDate('date', Carbon::today())
        ->where('mission_id', $this->pushupMission->id)
        ->first();

    expect($userMission->current_progress)->toBe(100.0);
    expect($userMission->is_completed)->toBeTrue();

    // Logging another workout does not give additional mission XP (already 100% complete)
    $result2 = $workoutService->completeWorkout($this->user, $this->pushupWorkout->id, 20, 60);
    expect($result2['mission_xp'])->toBe(0);
});

test('hell mode activates tomorrow when completing daily missions for 5 days', function () {
    $workoutService = new WorkoutService;
    $resetService = new DailyResetService;

    // We need 5 completed days in the week.
    // Let's mock completions for Mon, Tue, Wed, Thu, Fri.
    // Monday starts on:
    $monday = Carbon::today()->startOfWeek(Carbon::MONDAY);

    // Simulate completions for 5 days
    for ($i = 0; $i < 5; $i++) {
        $date = $monday->copy()->addDays($i);

        // Generate missions for that day
        $resetService->generateMissionsForUser($this->user, $date);

        // Mark them all as completed
        UserMission::where('user_id', $this->user->id)
            ->whereDate('date', $date)
            ->update(['current_progress' => 100, 'is_completed' => true]);

        // For testing, update weekly progress manually or via workout service helper
        // Since we are completing them, we run the weekly progress updater
        // We travel time to that day
        Carbon::setTestNow($date);
        $workoutService->updateWeeklyProgress($this->user);
    }

    // Today is Friday (index 4)
    $weekStart = $monday->toDateString();
    $weeklyProgress = WeeklyProgress::where('user_id', $this->user->id)
        ->where('week_start', $weekStart)
        ->first();

    expect($weeklyProgress->completed_days)->toBe(5);
    expect($weeklyProgress->hell_mode_ready)->toBeTrue();
    expect($weeklyProgress->hell_mode_used)->toBeFalse();

    // Now, let's simulate the next day (Saturday) reset.
    // Daily reset should detect hell_mode_ready = true, used = false,
    // double the mission targets snapshot for that day, set hell_mode_used = true, and ready = false.
    $saturday = $monday->copy()->addDays(5);
    Carbon::setTestNow($saturday);

    $resetService->generateMissionsForUser($this->user, $saturday);

    $weeklyProgress->refresh();
    expect($weeklyProgress->hell_mode_ready)->toBeFalse();
    expect($weeklyProgress->hell_mode_used)->toBeTrue();

    // Verify Saturday's mission target snapshot is doubled!
    $pushupUserMission = UserMission::where('user_id', $this->user->id)
        ->whereDate('date', $saturday)
        ->where('mission_id', $this->pushupMission->id)
        ->first();

    expect($pushupUserMission->target_snapshot)->toBe(200); // 100 * 2.0 = 200

    // Reset Carbon clock
    Carbon::setTestNow();
});
