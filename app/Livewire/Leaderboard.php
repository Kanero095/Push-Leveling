<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Leaderboard extends Component
{
    public $users = [];
    public $currentUser;
    public $unlockedTitles = [];
    public $selectedType = '';
    public $workoutTypes = [];
    public $currentUserXp = [];

    public static $typeLabels = [
        'push_up' => 'Push Up',
        'sit_up' => 'Sit Up',
        'squat' => 'Squat',
        'run' => 'Lari (Running)',
        'swim' => 'Berenang (Swimming)',
        'jump_rope' => 'Lompat Tali (Jump Rope)',
        'pull_up' => 'Pull Up',
        'cycle' => 'Bersepeda (Cycling)',
        'plank' => 'Plank',
        'lunge' => 'Lunge',
        'burpee' => 'Burpee',
        'bench_press' => 'Bench Press',
        'deadlift' => 'Deadlift',
        'bicep_curl' => 'Bicep Curl',
        'shoulder_press' => 'Shoulder Press',
        'back_pull' => 'Back Pull / Lat Row',
    ];

    public function mount()
    {
        $this->currentUser = Auth::user();
        
        // Get active workout types and map them to labels
        $types = \App\Models\Workout::select('type')->distinct()->whereNotNull('type')->pluck('type')->toArray();
        foreach ($types as $type) {
            $this->workoutTypes[$type] = self::$typeLabels[$type] ?? ucwords(str_replace('_', ' ', $type));
        }

        $this->loadData();
    }

    public function updatedSelectedType()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->currentUser->refresh();
        $this->unlockedTitles = $this->currentUser->getUnlockedTitles();

        // Fetch logged-in user's XP per workout type
        $this->currentUserXp = \App\Models\WorkoutLog::where('user_id', $this->currentUser->id)
            ->join('workouts', 'workout_logs.workout_id', '=', 'workouts.id')
            ->groupBy('workouts.type')
            ->select('workouts.type', \Illuminate\Support\Facades\DB::raw('SUM(workout_logs.xp_earned) as total_xp'))
            ->pluck('total_xp', 'workouts.type')
            ->toArray();

        if (empty($this->selectedType)) {
            $this->users = User::orderBy('xp_total', 'desc')->get();
        } else {
            $selectedType = $this->selectedType;
            $this->users = User::select('users.*')
                ->selectSub(function ($query) use ($selectedType) {
                    $query->from('workout_logs')
                        ->join('workouts', 'workout_logs.workout_id', '=', 'workouts.id')
                        ->whereColumn('workout_logs.user_id', 'users.id')
                        ->where('workouts.type', $selectedType)
                        ->selectRaw('COALESCE(SUM(workout_logs.xp_earned), 0)');
                }, 'filtered_xp')
                ->orderBy('filtered_xp', 'desc')
                ->orderBy('xp_total', 'desc')
                ->get();
        }
    }

    /**
     * Equip a title if it is unlocked.
     */
    public function equipTitle($title)
    {
        if ($title === '' || in_array($title, $this->unlockedTitles)) {
            $this->currentUser->title = $title === '' ? null : $title;
            $this->currentUser->save();
            
            $this->loadData();
            $this->dispatch('toast', variant: 'success', text: 'Gelar/Title profil berhasil diubah.');
        } else {
            $this->dispatch('toast', variant: 'error', text: 'Gelar ini masih terkunci.');
        }
    }

    public function render()
    {
        return view('livewire.leaderboard')
            ->layout('layouts.app', ['title' => 'Leaderboard & Achievements']);
    }
}
