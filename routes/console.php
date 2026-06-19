<?php

use App\Models\Notification;
use App\Models\User;
use App\Models\UserMission;
use App\Services\DailyResetService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Inspiring quote command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Command: Generate Daily Missions
Artisan::command('missions:generate', function () {
    $this->info('Generating daily missions...');
    $service = new DailyResetService;
    $service->generateMissionsForAllUsers(Carbon::today());
    $this->info('Daily missions generated successfully!');
})->purpose('Generate daily missions for all users');

// Command: Morning Notifications (07:00)
Artisan::command('notifications:morning', function () {
    $this->info('Sending morning notifications...');
    $users = User::all();

    foreach ($users as $user) {
        $message = "Halo {$user->name}! Mulai harimu dengan sehat. Selesaikan daily mission kamu hari ini untuk mendapatkan XP dan naik level!";

        // Log to database
        Notification::create([
            'user_id' => $user->id,
            'type' => 'morning',
            'message' => $message,
            'status' => 'sent',
        ]);

        // Send mail log
        try {
            Mail::raw($message, function ($mail) use ($user) {
                $mail->to($user->email)->subject('Workout Tracker - Mulai Harimu!');
            });
        } catch (Exception $e) {
            Log::error("Failed to send morning mail to {$user->email}: ".$e->getMessage());
        }
    }

    $this->info('Morning notifications sent.');
})->purpose('Send morning workout reminders to all users');

// Command: Evening Notifications (21:00)
Artisan::command('notifications:evening', function () {
    $this->info('Checking and sending evening reminders...');
    $users = User::all();

    foreach ($users as $user) {
        // Check if user has uncompleted missions today
        $hasUncompleted = UserMission::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->where('is_completed', false)
            ->exists();

        if ($hasUncompleted) {
            $message = "Halo {$user->name}! Jangan lupa selesaikan daily mission kamu malam ini sebelum reset. Tetap konsisten!";

            // Log to database
            Notification::create([
                'user_id' => $user->id,
                'type' => 'evening',
                'message' => $message,
                'status' => 'sent',
            ]);

            // Send mail log
            try {
                Mail::raw($message, function ($mail) use ($user) {
                    $mail->to($user->email)->subject('Workout Tracker - Pengingat Misi Malam');
                });
            } catch (Exception $e) {
                Log::error("Failed to send evening mail to {$user->email}: ".$e->getMessage());
            }
        }
    }

    $this->info('Evening reminders processed.');
})->purpose('Send evening reminders to users with incomplete tasks');

// --- Scheduling Definitions ---

Schedule::command('missions:generate')->dailyAt('00:00');
Schedule::command('notifications:morning')->dailyAt('06:00');
Schedule::command('notifications:evening')->dailyAt('14:14');
