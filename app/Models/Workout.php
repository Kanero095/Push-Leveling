<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'difficulty',
        'video_url',
        'type',
        'description',
        'reps_label',
        'duration_label',
    ];

    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    /**
     * Get difficulty multiplier for XP calculation.
     */
    public function getDifficultyMultiplier(): float
    {
        return match (strtolower($this->difficulty)) {
            'easy', 'beginner' => 1.0,
            'medium', 'intermediate' => 1.5,
            'hard', 'advanced' => 2.0,
            default => 1.0,
        };
    }

    /**
     * Get YouTube Embed URL.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $urlParts = parse_url($this->video_url);

        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParts);
            if (isset($queryParts['v'])) {
                return 'https://www.youtube.com/embed/'.$queryParts['v'];
            }
        }

        if (isset($urlParts['path'])) {
            $pathParts = explode('/', trim($urlParts['path'], '/'));
            if (count($pathParts) > 0 && (str_contains($urlParts['host'] ?? '', 'youtu.be') || str_contains($urlParts['host'] ?? '', 'youtube.com'))) {
                return 'https://www.youtube.com/embed/'.end($pathParts);
            }
        }

        return $this->video_url;
    }
}
