<?php

namespace App\Livewire;

use App\Models\UserMission;
use App\Models\WeeklyProgress;
use App\Models\Workout;
use App\Models\WorkoutLog;
use App\Services\DailyResetService;
use App\Services\WorkoutService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;

    public $weeklyProgress;

    public $aiRecommendation = '';

    public $aiMood = 'normal'; // normal, pumped, warning, celebratory

    public $timeframe = 'weekly';

    public $selectedType = 'all';

    public $chartLabels = [];

    public $chartRepsValues = [];

    public $chartXpValues = [];

    public function mount()
    {
        $this->user = Auth::user();
        $this->loadData();
        $this->updateChartData();
    }

    public function loadData()
    {
        $this->user->refresh();

        // 1. Ensure daily missions exist
        $workoutService = new WorkoutService;
        $workoutService->ensureMissionsExistForToday($this->user);

        // 2. Fetch weekly progress
        $weekStart = Carbon::today()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->weeklyProgress = WeeklyProgress::firstOrCreate(
            ['user_id' => $this->user->id, 'week_start' => $weekStart],
            [
                'completed_days' => 0,
                'hell_mode_ready' => false,
                'hell_mode_used' => false,
            ]
        );

        // 3. Generate AI Guide recommendation
        $this->generateAiRecommendation();
    }

    public function getDailyMissionsProperty()
    {
        return UserMission::with('mission')
            ->where('user_id', $this->user->id)
            ->whereDate('date', Carbon::today())
            ->get();
    }

    public function generateAiRecommendation()
    {
        $classification = strtolower($this->user->user_level);
        $level = $this->user->level;
        $tier = $this->user->getTier();

        $missions = $this->dailyMissions;
        $completedCount = $missions->where('is_completed', true)->count();
        $allCompleted = $completedCount === $missions->count() && $missions->count() > 0;

        $isHellModeActive = false;
        if ($missions->isNotEmpty()) {
            $pushUpMission = $missions->firstWhere('mission.type', 'push_up');
            if ($pushUpMission && $pushUpMission->target_snapshot > 100) {
                $isHellModeActive = true;
            }
        }

        if ($isHellModeActive) {
            $this->aiMood = 'warning';
            $this->aiRecommendation = '💀 HELL MODE AKTIF! Target latihan hari ini dilipatgandakan (2x). Ini adalah ujian konsistensi sejati. Pusatkan pikiranmu, lakukan pemanasan secara menyeluruh, dan taklukkan batas kemampuanmu hari ini!';

            return;
        }

        if ($allCompleted) {
            $this->aiMood = 'celebratory';
            $this->aiRecommendation = '🎉 LUAR BIASA! Kamu telah menyelesaikan semua Daily Mission hari ini! Tubuhmu sedang berkembang pesat. Istirahatlah dengan cukup, konsumsi protein, dan mari kita mendominasi lagi esok hari!';

            return;
        }

        $workoutTips = [
            'beginner' => 'Karena kamu diklasifikasikan sebagai **Beginner**, kami sarankan melakukan **Knee Push-ups** dan **Light Jogging** terlebih dahulu. Fokus pada form gerakan yang benar daripada kecepatan.',
            'intermediate' => 'Sebagai user **Intermediate**, kamu siap menghadapi tantangan penuh. Coba lakukan **Standard Push-ups** dengan tempo lambat (3 detik turun, 1 detik naik) untuk memaksimalkan kekuatan otot.',
            'advanced' => 'Bagi petarung kelas **Advanced**, tantang dirimu dengan **Diamond/Pistol Squats** dan **Sprint Interval Training**. Jaga intensitas tinggi untuk mendorong melampaui batas lamamu!',
        ];

        $tip = $workoutTips[$classification] ?? $workoutTips['beginner'];
        $xpProgress = $this->user->xp_progress;
        $xpNeeded = $xpProgress['target'] - $xpProgress['current'];

        if ($xpNeeded < 80) {
            $this->aiMood = 'pumped';
            $this->aiRecommendation = "🔥 KAMU SUDAH DEKAT! Hanya butuh {$xpNeeded} XP lagi untuk naik ke Level ".($level + 1).'. Selesaikan satu set latihan lagi sekarang untuk klaim kenaikan levelmu! '.$tip;
        } else {
            $this->aiMood = 'normal';
            $this->aiRecommendation = "Selamat datang kembali, Champion. Tingkat tier kamu saat ini adalah **{$tier}**. Hari ini kamu telah menyelesaikan {$completedCount}/4 misi harian. ".$tip;
        }
    }

    public function simulateMissionCompletion()
    {
        if (! $this->user || ! $this->user->is_admin) {
            abort(403, 'Unauthorized');
        }

        $missions = $this->dailyMissions;
        foreach ($missions as $userMission) {
            $userMission->current_progress = $userMission->target_snapshot;
            $userMission->is_completed = true;
            $userMission->save();
        }

        $workoutService = new WorkoutService;
        $workoutService->updateWeeklyProgress($this->user);

        $this->loadData();
        $this->dispatch('toast', variant: 'success', text: 'Simulated completing all missions for today!');
    }

    public function toggleHellModeReady()
    {
        if (! $this->user || ! $this->user->is_admin) {
            abort(403, 'Unauthorized');
        }

        $this->weeklyProgress->hell_mode_ready = ! $this->weeklyProgress->hell_mode_ready;
        if ($this->weeklyProgress->hell_mode_ready) {
            $this->weeklyProgress->hell_mode_used = false;
        }
        $this->weeklyProgress->save();

        $this->loadData();
        $status = $this->weeklyProgress->hell_mode_ready ? 'READY' : 'NOT READY';
        $this->dispatch('toast', variant: 'info', text: "Hell Mode status set to: {$status}");
    }

    public function triggerDailyReset()
    {
        if (! $this->user || ! $this->user->is_admin) {
            abort(403, 'Unauthorized');
        }

        $missions = $this->dailyMissions;
        foreach ($missions as $userMission) {
            $userMission->current_progress = 0;
            $userMission->is_completed = false;
            $userMission->save();
        }

        $workoutService = new WorkoutService;
        $workoutService->updateWeeklyProgress($this->user);

        $this->loadData();
        $this->dispatch('toast', variant: 'info', text: 'Today\'s mission progress reset to 0.');
    }

    public function simulateNewDay()
    {
        if (! $this->user || ! $this->user->is_admin) {
            abort(403, 'Unauthorized');
        }

        $today = Carbon::today();
        UserMission::where('user_id', $this->user->id)
            ->whereDate('date', $today)
            ->delete();

        $resetService = new DailyResetService;
        $resetService->generateMissionsForUser($this->user, $today);

        $this->loadData();
        $this->dispatch('toast', variant: 'success', text: 'Simulated a new day! Missions generated.');
    }

    public function updatedTimeframe()
    {
        $this->updateChartData();
    }

    public function updatedSelectedType()
    {
        $this->updateChartData();
    }

    public function getWorkoutTypesProperty()
    {
        $types = Workout::select('type')
            ->whereNotNull('type')
            ->distinct()
            ->pluck('type')
            ->toArray();

        $typeLabels = [
            'push_up' => '💪 Push-up',
            'sit_up' => '🧘 Sit-up',
            'squat' => '🦵 Squat',
            'run' => '🏃 Lari / Jogging',
            'swim' => '🏊 Berenang',
            'jump_rope' => '🪢 Lompat Tali',
            'pull_up' => '🧗 Pull-up',
            'cycle' => '🚴 Bersepeda',
            'plank' => '🛡️ Plank',
            'lunge' => '🦵 Lunge',
            'burpee' => '💥 Burpee',
            'bench_press' => '🏋️ Bench Press',
            'deadlift' => '🏋️ Deadlift',
            'bicep_curl' => '💪 Bicep Curl',
            'shoulder_press' => '🏋️ Shoulder Press',
            'back_pull' => '🏋️ Lat Pulldown',
        ];

        $result = [];
        foreach ($types as $type) {
            $result[$type] = $typeLabels[$type] ?? str_replace('_', ' ', ucwords($type, '_'));
        }

        return $result;
    }

    public function updateChartData()
    {
        $data = $this->getChartData();
        $this->chartLabels = $data['labels'];
        $this->chartRepsValues = $data['reps_values'];
        $this->chartXpValues = $data['xp_values'];

        $this->dispatch('chart-updated',
            labels: $this->chartLabels,
            repsValues: $this->chartRepsValues,
            xpValues: $this->chartXpValues
        );
    }

    private function getChartData()
    {
        $query = WorkoutLog::where('user_id', $this->user->id);

        if ($this->selectedType !== 'all') {
            $query->whereHas('workout', function ($q) {
                $q->where('type', $this->selectedType);
            });
        }

        $labels = [];
        $reps_values = [];
        $xp_values = [];

        if ($this->timeframe === 'weekly') {
            $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $logs = $query->whereBetween('created_at', [$start, $end])->get();

            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i);
                $labels[] = $this->getIndonesianDayName($date->dayOfWeekIso);

                $dayLogs = $logs->filter(function ($log) use ($date) {
                    return Carbon::parse($log->created_at)->isSameDay($date);
                });
                $reps_values[] = $dayLogs->sum('reps');
                $xp_values[] = $dayLogs->sum('xp_earned');
            }
        } elseif ($this->timeframe === 'monthly') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $daysInMonth = Carbon::now()->daysInMonth;

            $logs = $query->whereBetween('created_at', [$start, $end])->get();

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = $start->copy()->addDays($i - 1);
                $labels[] = $i;

                $dayLogs = $logs->filter(function ($log) use ($date) {
                    return Carbon::parse($log->created_at)->isSameDay($date);
                });
                $reps_values[] = $dayLogs->sum('reps');
                $xp_values[] = $dayLogs->sum('xp_earned');
            }
        } elseif ($this->timeframe === 'yearly') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();

            $logs = $query->whereBetween('created_at', [$start, $end])->get();

            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $monthNames[$m - 1];

                $monthLogs = $logs->filter(function ($log) use ($m) {
                    return Carbon::parse($log->created_at)->month === $m;
                });
                $reps_values[] = $monthLogs->sum('reps');
                $xp_values[] = $monthLogs->sum('xp_earned');
            }
        }

        return [
            'labels' => $labels,
            'reps_values' => $reps_values,
            'xp_values' => $xp_values,
        ];
    }

    private function getIndonesianDayName($dayNum)
    {
        return match ($dayNum) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => '',
        };
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
