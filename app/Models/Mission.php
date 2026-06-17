<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'target',
        'base_xp',
    ];

    public function userMissions()
    {
        return $this->hasMany(UserMission::class);
    }
}
