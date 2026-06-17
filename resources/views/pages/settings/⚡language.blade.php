<?php

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Language settings')] class extends Component {
    public string $locale = 'en';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->locale = Auth::user()->locale ?? 'en';
    }

    /**
     * Update the language setting.
     */
    public function updateLanguage(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'locale' => ['required', 'string', 'in:en,id'],
        ]);

        $user->locale = $validated['locale'];
        $user->save();

        Session::put('locale', $this->locale);
        \Illuminate\Support\Facades\App::setLocale($this->locale);

        Flux::toast(variant: 'success', text: __('Language settings updated.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Language settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Language')" :subheading="__('Update the language settings for your account')">
        <form wire:submit="updateLanguage" class="my-6 w-full space-y-6">
            <!-- Language Selection -->
            <flux:select wire:model="locale" :label="__('Language / Bahasa')">
                <flux:select.option value="en">English</flux:select.option>
                <flux:select.option value="id">Bahasa Indonesia</flux:select.option>
            </flux:select>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full cursor-pointer" data-test="update-language-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
