<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'xp_total', 'level', 'user_level', 'phone', 'title', 'profile_photo_path', 'locale', 'is_admin'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'xp_total' => 'integer',
            'level' => 'integer',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // --- Relationships ---

    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    public function userMissions()
    {
        return $this->hasMany(UserMission::class);
    }

    public function weeklyProgress()
    {
        return $this->hasMany(WeeklyProgress::class);
    }

    public function manualLogs()
    {
        return $this->hasMany(ManualLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // --- Gamification & Leveling Helpers ---

    /**
     * Calculate Level based on Cumulative XP.
     * XP required for level L is 100 * (L ^ 1.5).
     */
    public static function calculateLevelFromXp($xpTotal): int
    {
        $level = 1;
        while (true) {
            $xpNeeded = 100 * pow($level, 1.5);
            if ($xpTotal >= $xpNeeded) {
                $level++;
            } else {
                break;
            }
        }
        return $level;
    }

    /**
     * Add XP and handle potential level ups.
     */
    public function addXP(int $xp): array
    {
        $oldLevel = $this->level;
        $this->xp_total += $xp;
        
        $newLevel = self::calculateLevelFromXp($this->xp_total);
        $leveledUp = $newLevel > $oldLevel;
        
        if ($leveledUp) {
            $this->level = $newLevel;
        }
        
        $this->save();

        return [
            'leveled_up' => $leveledUp,
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'xp_earned' => $xp,
        ];
    }

    /**
     * Get level progress stats for the UI (current level XP progress, target XP for next level).
     */
    public function getXpProgressAttribute(): array
    {
        $currentLevel = $this->level;
        $prevLevelThreshold = $currentLevel == 1 ? 0 : 100 * pow($currentLevel - 1, 1.5);
        $nextLevelThreshold = 100 * pow($currentLevel, 1.5);
        
        $xpInCurrentLevel = $this->xp_total - $prevLevelThreshold;
        $xpNeededForNextLevel = $nextLevelThreshold - $prevLevelThreshold;
        
        return [
            'current' => max(0, (int) $xpInCurrentLevel),
            'target' => (int) $xpNeededForNextLevel,
            'percentage' => min(100, max(0, $xpNeededForNextLevel > 0 ? round(($xpInCurrentLevel / $xpNeededForNextLevel) * 100) : 0))
        ];
    }

    /**
     * Get Tier Name based on User Level.
     */
    public function getTier(): string
    {
        $level = $this->level;
        if ($level <= 5) return 'Beginner';
        if ($level <= 10) return 'Rookie';
        if ($level <= 15) return 'Trainee';
        if ($level <= 20) return 'Fighter';
        if ($level <= 30) return 'Warrior';
        if ($level <= 40) return 'Elite';
        if ($level <= 50) return 'Champion';
        if ($level <= 70) return 'Master';
        if ($level <= 90) return 'Grandmaster';
        return 'Legend';
    }

    /**
     * Get Titles unlocked based on current level.
     */
    public function getUnlockedTitles(): array
    {
        $level = $this->level;
        $titles = [];
        
        if ($level >= 10) $titles[] = 'Awakened';
        if ($level >= 20) $titles[] = 'Relentless';
        if ($level >= 40) $titles[] = 'Iron Body';
        if ($level >= 60) $titles[] = 'Unbreakable';
        if ($level >= 100) $titles[] = 'Limit Breaker';
        
        return $titles;
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }
}
