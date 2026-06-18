<?php

namespace App\Livewire;

use App\Models\ManualLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManualLogs extends Component
{
    public $logs = [];

    // Form fields
    public $activityName = '';

    public $duration = '';

    public $note = '';

    protected $rules = [
        'activityName' => 'required|string|min:3|max:100',
        'duration' => 'required|integer|min:1|max:1440',
        'note' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->logs = ManualLog::where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function submitLog()
    {
        $this->validate();

        ManualLog::create([
            'user_id' => Auth::id(),
            'activity_name' => $this->activityName,
            'duration' => $this->duration,
            'note' => $this->note ?: null,
        ]);

        $this->reset(['activityName', 'duration', 'note']);
        $this->loadData();

        $this->dispatch('toast', variant: 'success', text: 'Catatan latihan manual berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.manual-logs')
            ->layout('layouts.app', ['title' => 'Manual Logs']);
    }
}
