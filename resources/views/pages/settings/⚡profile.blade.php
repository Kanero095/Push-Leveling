<?php

use App\Concerns\ProfileValidationRules;
/* @chisel-email-verification */
use Illuminate\Contracts\Auth\MustVerifyEmail;
/* @end-chisel-email-verification */
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $user_level = 'beginner';
    public string $title = '';
    
    // Profile photo upload property
    public $photo;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->user_level = $user->user_level ?? 'beginner';
        $this->title = $user->title ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => $this->nameRules(),
            'email' => $this->emailRules($user->id),
            'phone' => ['nullable', 'string', 'max:20'],
            'user_level' => ['required', 'string', 'in:beginner,intermediate,advanced'],
            'title' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:1024'], // Max 1MB
        ]);

        // Guard against equipping locked titles
        if (!empty($validated['title']) && !in_array($validated['title'], $user->getUnlockedTitles())) {
            $validated['title'] = null;
        }

        $user->fill($validated);

        // Process profile picture upload
        if ($this->photo) {
            // Delete old photo if it exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Store new photo
            $path = $this->photo->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        $this->reset('photo');

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Delete the user's profile picture.
     */
    public function deleteProfilePhoto(): void
    {
        $user = Auth::user();
        
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
            Flux::toast(variant: 'success', text: __('Profile photo removed.'));
        }
        
        $this->reset('photo');
    }

    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }

    #[Computed]
    public function unlockedTitles(): array
    {
        return Auth::user()->getUnlockedTitles();
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your profile picture, name, email, and workout focus')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            
            <!-- Profile Photo Upload -->
            <div class="flex items-center gap-4" x-data="{ uploading: false }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false">
                <div class="relative flex size-20 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-inner">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="size-full object-cover">
                    @elseif (Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" class="size-full object-cover">
                    @else
                        <span class="text-2xl font-bold text-zinc-400 dark:text-zinc-500">{{ Auth::user()->initials() }}</span>
                    @endif
                    
                    <!-- Uploading Overlay -->
                    <div x-show="uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-[10px] font-bold" x-cloak>
                        Uploading...
                    </div>
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <flux:label>{{ __('Profile Picture') }}</flux:label>
                    <input type="file" wire:model="photo" id="photo-input" class="hidden" accept="image/*" />
                    <div class="flex items-center gap-2">
                        <flux:button x-on:click="document.getElementById('photo-input').click()" variant="subtle" size="sm" class="cursor-pointer">
                            {{ __('Choose Image') }}
                        </flux:button>
                        @if ($photo || Auth::user()->profile_photo_path)
                            <flux:button wire:click="deleteProfilePhoto" variant="danger" size="sm" class="cursor-pointer">
                                {{ __('Remove') }}
                            </flux:button>
                        @endif
                    </div>
                    <flux:error name="photo" />
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                {{-- @chisel-email-verification --}}
                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
                {{-- @end-chisel-email-verification --}}
            </div>

            <!-- Phone Number -->
            <flux:input wire:model="phone" :label="__('Phone Number')" type="text" placeholder="e.g. 08123456789" />

            <!-- User Level Classification -->
            <flux:select wire:model="user_level" :label="__('Workout Level / Classification')">
                <flux:select.option value="beginner">Beginner</flux:select.option>
                <flux:select.option value="intermediate">Intermediate</flux:select.option>
                <flux:select.option value="advanced">Advanced</flux:select.option>
            </flux:select>

            <!-- Equipped Title -->
            <flux:select wire:model="title" :label="__('Equipped Gelar / Title')">
                <flux:select.option value="">(None)</flux:select.option>
                @foreach($this->unlockedTitles as $t)
                    <flux:select.option value="{{ $t }}">{{ $t }}</flux:select.option>
                @endforeach
            </flux:select>



            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full cursor-pointer" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- @chisel-email-verification --}}
        @if ($this->showDeleteUser)
        {{-- @end-chisel-email-verification --}}
            <livewire:pages::settings.delete-user-form />
        {{-- @chisel-email-verification --}}
        @endif
        {{-- @end-chisel-email-verification --}}
    </x-pages::settings.layout>
</section>
