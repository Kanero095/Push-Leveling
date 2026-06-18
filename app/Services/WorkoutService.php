<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMission;
use App\Models\WeeklyProgress;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkoutService
{
    /**
     * Log a workout session for a user.
     * Computes workout XP, updates daily missions, awards partial XP, and updates weekly progress.
     */
    public function completeWorkout(User $user, int $workoutId, int $reps, int $durationInSeconds): array
    {
        return DB::transaction(function () use ($user, $workoutId, $reps, $durationInSeconds) {
            $workout = Workout::findOrFail($workoutId);

            // 1. Calculate Workout XP
            // Formula: XP = (reps * 0.5 + duration_minutes * 0.2) * difficulty_multiplier
            $durationInMinutes = $durationInSeconds / 60.0;
            $difficultyMultiplier = $workout->getDifficultyMultiplier();
            $workoutXp = (int) round(($reps * 0.5 + $durationInMinutes * 0.2) * $difficultyMultiplier);
            $workoutXp = max(1, $workoutXp); // Ensure at least 1 XP is earned

            // 2. Create Workout Log
            $log = WorkoutLog::create([
                'user_id' => $user->id,
                'workout_id' => $workout->id,
                'reps' => $reps,
                'duration' => $durationInSeconds,
                'xp_earned' => $workoutXp,
            ]);

            // 3. Add XP to User
            $levelResult = $user->addXP($workoutXp);

            // 4. Update Daily Mission progress if applicable
            $missionXpEarned = 0;
            $missionCompleted = false;

            if ($workout->type) {
                // Ensure today's missions exist
                $this->ensureMissionsExistForToday($user);

                $todayString = Carbon::today()->toDateString();

                // Find user mission of this type for today
                $userMission = UserMission::where('user_id', $user->id)
                    ->where('date', $todayString)
                    ->whereHas('mission', function ($query) use ($workout) {
                        $query->where('type', $workout->type);
                    })
                    ->first();

                if ($userMission) {
                    $oldProgress = $userMission->current_progress;
                    $target = $userMission->target_snapshot;

                    // Add progress (clamp to target)
                    $newProgress = min($target, $oldProgress + $reps);

                    // Compute partial XP
                    // XP = baseXP * progress / target
                    // deltaXP = newXP_so_far - oldXP_so_far
                    $baseXp = $userMission->mission->base_xp;
                    $oldXpAwarded = (int) round($baseXp * ($oldProgress / $target));
                    $newXpAwarded = (int) round($baseXp * ($newProgress / $target));
                    $deltaXp = max(0, $newXpAwarded - $oldXpAwarded);

                    if ($deltaXp > 0) {
                        $user->addXP($deltaXp);
                        $missionXpEarned = $deltaXp;
                    }

                    $userMission->current_progress = $newProgress;
                    if ($newProgress >= $target) {
                        $userMission->is_completed = true;
                        $missionCompleted = true;
                    }
                    $userMission->save();

                    // 5. Update Weekly Progress
                    $this->updateWeeklyProgress($user);
                }
            }

            return [
                'workout_log' => $log,
                'workout_xp' => $workoutXp,
                'mission_xp' => $missionXpEarned,
                'total_xp_gained' => $workoutXp + $missionXpEarned,
                'leveled_up' => $levelResult['leveled_up'],
                'new_level' => $levelResult['new_level'],
                'mission_completed' => $missionCompleted,
            ];
        });
    }

    /**
     * Ensure daily missions have been generated for today.
     */
    public function ensureMissionsExistForToday(User $user): void
    {
        $today = Carbon::today()->toDateString();
        $exists = UserMission::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        if (! $exists) {
            // Instantiate DailyResetService and generate
            $resetService = new DailyResetService;
            $resetService->generateMissionsForUser($user, Carbon::today());
        }
    }

    /**
     * Recalculate completed days in the week and update Hell Mode availability.
     */
    public function updateWeeklyProgress(User $user): void
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = $today->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        // Count unique dates in this week where all daily missions are completed
        // Each day has exactly 4 missions.
        $completedDatesCount = UserMission::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->select('date')
            ->groupBy('date')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END)')
            ->get()
            ->count();

        // Find or create weekly progress
        $weeklyProgress = WeeklyProgress::firstOrCreate(
            ['user_id' => $user->id, 'week_start' => $weekStart],
            [
                'completed_days' => 0,
                'hell_mode_ready' => false,
                'hell_mode_used' => false,
            ]
        );

        $weeklyProgress->completed_days = $completedDatesCount;

        // Hell Mode Logic:
        // Completed daily mission >= 5 days in a week
        // Not Sunday
        if ($weeklyProgress->completed_days >= 5 && Carbon::today()->dayOfWeek !== Carbon::SUNDAY) {
            if (! $weeklyProgress->hell_mode_used) {
                $weeklyProgress->hell_mode_ready = true;
            }
        } else {
            // If completed days drops below 5 (e.g. debugging/data manipulation), or is Sunday, reset ready.
            // But if it is Sunday, we can't make it ready if it wasn't already.
            if (Carbon::today()->dayOfWeek === Carbon::SUNDAY) {
                $weeklyProgress->hell_mode_ready = false;
            }
        }

        $weeklyProgress->save();
    }
}
