<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mission_id',
        'date',
        'current_progress',
        'target_snapshot',
        'is_completed',
    ];

    protected $casts = [
        'date' => 'string',
        'current_progress' => 'float',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Get completion progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->target_snapshot <= 0) {
            return 0;
        }

        return min(100, round(($this->current_progress / $this->target_snapshot) * 100));
    }
}
