<?php

namespace App\Livewire;

use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Services\WorkoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Workouts extends Component
{
    public $workouts = [];

    public $history = [];

    // Workout session state
    public $showModal = false;

    public $activeWorkout = null;

    public $reps = 0;

    public $duration = 0; // in seconds

    public $xpProjected = 0;

    protected $listeners = ['tick' => 'incrementDuration'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->workouts = Workout::all();
        $this->history = WorkoutLog::with('workout')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Open workout log modal and reset stopwatch state.
     */
    public function selectWorkout($workoutId)
    {
        $this->activeWorkout = Workout::findOrFail($workoutId);
        $this->reps = 0;
        $this->duration = 0;
        $this->xpProjected = 0;
        $this->showModal = true;
    }

    /**
     * Triggered by client-side event to increment stopwatch seconds.
     */
    public function incrementDuration()
    {
        $this->duration++;
        $this->calculateProjectedXp();
    }

    /**
     * Triggered on reps input update.
     */
    public function updatedReps()
    {
        $this->calculateProjectedXp();
    }

    /**
     * Compute real-time XP projection.
     */
    public function calculateProjectedXp()
    {
        if (! $this->activeWorkout) {
            $this->xpProjected = 0;

            return;
        }

        $multiplier = $this->activeWorkout->getDifficultyMultiplier();
        $durationInMinutes = $this->duration / 60.0;

        $this->xpProjected = (int) round(($this->reps * 0.5 + $durationInMinutes * 0.2) * $multiplier);
        $this->xpProjected = max(1, $this->xpProjected);
    }

    /**
     * Increment reps by count.
     */
    public function addReps($count)
    {
        $this->reps = max(0, $this->reps + $count);
        $this->calculateProjectedXp();
    }

    /**
     * Complete and log workout session.
     */
    public function submitWorkout()
    {
        if ($this->reps <= 0 && $this->duration <= 0) {
            $this->dispatch('toast', variant: 'warning', text: 'Silakan masukkan reps atau durasi latihan.');

            return;
        }

        $user = Auth::user();
        $workoutService = new WorkoutService;

        $result = $workoutService->completeWorkout(
            $user,
            $this->activeWorkout->id,
            (int) $this->reps,
            (int) $this->duration
        );

        $this->showModal = false;
        $this->activeWorkout = null;
        $this->loadData();

        // Dispatch status toast
        $msg = "Workout logged successfully! Got +{$result['total_xp_gained']} XP.";
        if ($result['leveled_up']) {
            $msg .= " 🎉 LEVEL UP! You reached Level {$result['new_level']}!";
        }

        $this->dispatch('toast', variant: 'success', text: $msg);
        $this->dispatch('workout-logged'); // Refresh dashboard values if embedded
    }

    public function cancelWorkout()
    {
        $this->showModal = false;
        $this->activeWorkout = null;
    }

    public function render()
    {
        return view('livewire.workouts')
            ->layout('layouts.app', ['title' => 'Workouts']);
    }
}
