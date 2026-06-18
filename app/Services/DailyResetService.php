<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\User;
use App\Models\UserMission;
use App\Models\WeeklyProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyResetService
{
    /**
     * Generate daily missions for a specific user and date.
     * Integrates Hell Mode multipliers (2x targets) if the user is eligible.
     */
    public function generateMissionsForUser(User $user, Carbon $date): void
    {
        $dateString = $date->toDateString();
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY)->toDateString();

        DB::transaction(function () use ($user, $dateString, $weekStart) {
            // Find or create weekly progress
            $weeklyProgress = WeeklyProgress::firstOrCreate(
                ['user_id' => $user->id, 'week_start' => $weekStart],
                [
                    'completed_days' => 0,
                    'hell_mode_ready' => false,
                    'hell_mode_used' => false,
                ]
            );

            // Determine if Hell Mode is active for today
            $multiplier = 1.0;
            if ($weeklyProgress->hell_mode_ready && ! $weeklyProgress->hell_mode_used) {
                $multiplier = 2.0;
                // Update weekly progress to show hell mode has been activated
                $weeklyProgress->hell_mode_used = true;
                $weeklyProgress->hell_mode_ready = false;
                $weeklyProgress->save();
            }

            // Get all standard missions
            $missions = Mission::all();

            // If no missions are seeded, do nothing
            if ($missions->isEmpty()) {
                return;
            }

            foreach ($missions as $mission) {
                $targetSnapshot = (int) round($mission->target * $multiplier);

                UserMission::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'mission_id' => $mission->id,
                        'date' => $dateString,
                    ],
                    [
                        'current_progress' => 0,
                        'target_snapshot' => $targetSnapshot,
                        'is_completed' => false,
                    ]
                );
            }
        });
    }

    /**
     * Generate daily missions for all registered users for a specific date.
     */
    public function generateMissionsForAllUsers(Carbon $date): void
    {
        User::chunk(100, function ($users) use ($date) {
            foreach ($users as $user) {
                $this->generateMissionsForUser($user, $date);
            }
        });
    }
}
