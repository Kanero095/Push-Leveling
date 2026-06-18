<?php

namespace App\Livewire\Admin;

use App\Models\AppSetting;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $appName = '';

    public $appLogo;

    public $currentLogo = '';

    public $appBgImage;

    public $currentBgImage = '';

    protected $rules = [
        'appName' => 'required|string|min:3|max:50',
        'appLogo' => 'nullable|image|max:2048', // max 2MB
        'appBgImage' => 'nullable|image|max:5120', // max 5MB
    ];

    public function mount()
    {
        $this->appName = AppSetting::get('app_name', config('app.name', 'Laravel'));
        $this->currentLogo = AppSetting::get('app_logo', '');
        $this->currentBgImage = AppSetting::get('app_bg_image', '');
    }

    public function saveSettings()
    {
        $this->validate();

        // Save App Name
        AppSetting::set('app_name', $this->appName);
        config(['app.name' => $this->appName]);

        // Save App Logo
        if ($this->appLogo) {
            $logoPath = $this->appLogo->store('logo', 'public');
            AppSetting::set('app_logo', $logoPath);
            $this->currentLogo = $logoPath;
            $this->reset('appLogo');
        }

        // Save App Background Image
        if ($this->appBgImage) {
            $bgPath = $this->appBgImage->store('backgrounds', 'public');
            AppSetting::set('app_bg_image', $bgPath);
            $this->currentBgImage = $bgPath;
            $this->reset('appBgImage');
        }

        $this->dispatch('toast', variant: 'success', text: 'Pengaturan aplikasi berhasil disimpan.');
    }

    public function deleteLogo()
    {
        AppSetting::set('app_logo', '');
        $this->currentLogo = '';
        $this->dispatch('toast', variant: 'info', text: 'Logo aplikasi berhasil dihapus.');
    }

    public function deleteBgImage()
    {
        AppSetting::set('app_bg_image', '');
        $this->currentBgImage = '';
        $this->dispatch('toast', variant: 'info', text: 'Gambar latar belakang aplikasi berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin-settings')
            ->layout('layouts.app', ['title' => 'Pengaturan Aplikasi']);
    }
}
