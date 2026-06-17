<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Pengaturan Aplikasi') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Pengaturan Aplikasi')" :subheading="__('Atur logo dan nama aplikasi secara instan.')">
        <form wire:submit="saveSettings" class="my-6 w-full space-y-6">
            <!-- App Name Field -->
            <flux:input 
                wire:model="appName" 
                :label="__('Nama Aplikasi')" 
                type="text" 
                required 
                placeholder="Masukkan nama aplikasi..." 
                class="w-full"
            />

            <!-- App Logo Upload Field -->
            <div class="space-y-2" x-data="{ uploading: false }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false">
                <flux:label>{{ __('Logo Aplikasi') }}</flux:label>
                
                <div class="flex items-center gap-4">
                    <div class="relative flex size-20 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-inner">
                        @if ($appLogo)
                            <img src="{{ $appLogo->temporaryUrl() }}" class="size-full object-cover">
                        @elseif ($currentLogo)
                            <img src="{{ asset('storage/' . $currentLogo) }}" class="size-full object-cover">
                        @else
                            <div class="flex flex-col items-center text-zinc-400 dark:text-zinc-500">
                                <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Uploading Overlay -->
                        <div x-show="uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-[10px] font-bold" x-cloak>
                            Uploading...
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <input type="file" wire:model="appLogo" id="logo-input" class="hidden" accept="image/*" />
                        <div class="flex items-center gap-2">
                            <flux:button x-on:click="document.getElementById('logo-input').click()" variant="subtle" size="sm" class="cursor-pointer">
                                {{ __('Pilih Gambar') }}
                            </flux:button>
                            
                            @if ($appLogo || $currentLogo)
                                <flux:button wire:click="deleteLogo" variant="danger" size="sm" class="cursor-pointer">
                                    {{ __('Hapus') }}
                                </flux:button>
                            @endif
                        </div>
                        <flux:text class="text-[10px] text-zinc-400 dark:text-zinc-500">PNG, JPG, SVG maks 2MB</flux:text>
                        <flux:error name="appLogo" />
                    </div>
                </div>
            </div>

            <!-- App Background Image Upload Field -->
            <div class="space-y-2" x-data="{ uploading: false }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false">
                <flux:label>{{ __('Gambar Latar Aplikasi') }}</flux:label>
                
                <div class="flex items-center gap-4">
                    <div class="relative flex w-40 h-20 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-inner">
                        @if ($appBgImage)
                            <img src="{{ $appBgImage->temporaryUrl() }}" class="size-full object-contain">
                        @elseif ($currentBgImage)
                            <img src="{{ asset('storage/' . $currentBgImage) }}" class="size-full object-contain">
                        @else
                            <div class="flex flex-col items-center text-zinc-400 dark:text-zinc-500">
                                <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Uploading Overlay -->
                        <div x-show="uploading" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-[10px] font-bold" x-cloak>
                            Uploading...
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <input type="file" wire:model="appBgImage" id="bg-image-input" class="hidden" accept="image/*" />
                        <div class="flex items-center gap-2">
                            <flux:button x-on:click="document.getElementById('bg-image-input').click()" variant="subtle" size="sm" class="cursor-pointer">
                                {{ __('Pilih Gambar') }}
                            </flux:button>
                            
                            @if ($appBgImage || $currentBgImage)
                                <flux:button wire:click="deleteBgImage" variant="danger" size="sm" class="cursor-pointer">
                                    {{ __('Hapus') }}
                                </flux:button>
                            @endif
                        </div>
                        <flux:text class="text-[10px] text-zinc-400 dark:text-zinc-500">PNG, JPG, JPEG maks 5MB</flux:text>
                        <flux:error name="appBgImage" />
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50 p-4 space-y-3" x-data="{ appNamePreview: @entangle('appName') }">
                <flux:label>{{ __('Preview Tampilan') }}</flux:label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Sidebar Mockup -->
                    <div class="flex items-center gap-2.5 bg-white dark:bg-zinc-900 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 shadow-sm">
                        <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground overflow-hidden">
                            @if ($appLogo)
                                <img src="{{ $appLogo->temporaryUrl() }}" class="size-full object-cover">
                            @elseif ($currentLogo)
                                <img src="{{ asset('storage/' . $currentLogo) }}" class="size-full object-cover">
                            @else
                                <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                            @endif
                        </div>
                        <div class="font-semibold text-sm text-zinc-900 dark:text-white" x-text="appNamePreview || 'Laravel'"></div>
                    </div>

                    <!-- Login Card Mockup -->
                    <div class="flex flex-col items-center justify-center bg-white dark:bg-zinc-900 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 shadow-sm text-center">
                        <div class="flex aspect-square size-10 items-center justify-center rounded-xl bg-accent-content text-accent-foreground overflow-hidden mb-1">
                            @if ($appLogo)
                                <img src="{{ $appLogo->temporaryUrl() }}" class="size-full object-cover">
                            @elseif ($currentLogo)
                                <img src="{{ asset('storage/' . $currentLogo) }}" class="size-full object-cover">
                            @else
                                <x-app-logo-icon class="size-6 fill-current text-white dark:text-black" />
                            @endif
                        </div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-white" x-text="appNamePreview || 'Laravel'"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <flux:button variant="primary" type="submit" class="w-full cursor-pointer justify-center">
                {{ __('Simpan Perubahan') }}
            </flux:button>
        </form>
    </x-pages::settings.layout>
</section>
