<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyProgress extends Model
{
    use HasFactory;

    protected $table = 'weekly_progress';

    protected $fillable = [
        'user_id',
        'week_start',
        'completed_days',
        'hell_mode_ready',
        'hell_mode_used',
    ];

    protected $casts = [
        'week_start' => 'string',
        'hell_mode_ready' => 'boolean',
        'hell_mode_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
