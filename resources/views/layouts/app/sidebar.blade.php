<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bolt" :href="route('workouts')" :current="request()->routeIs('workouts')" wire:navigate>
                        {{ __('Workouts') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard" :href="route('manual-logs')" :current="request()->routeIs('manual-logs')" wire:navigate>
                        {{ __('Manual Logs') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="trophy" :href="route('leaderboard')" :current="request()->routeIs('leaderboard')" wire:navigate>
                        {{ __('Leaderboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="bell" :href="route('notifications')" :current="request()->routeIs('notifications')" wire:navigate>
                        {{ __('Notifications') }}
                        @php
                            $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <flux:badge size="sm" variant="danger" class="ml-auto animate-pulse">
                                {{ $unreadCount }}
                            </flux:badge>
                        @endif
                    </flux:sidebar.item>
                </flux:sidebar.group>

            </flux:sidebar.nav>

            <flux:spacer />



            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile Bottom Navigation Bar -->
        <div class="fixed bottom-0 left-0 right-0 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/95 dark:bg-zinc-900/95 backdrop-blur-md h-16 flex items-center justify-around lg:hidden z-30 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] dark:shadow-[0_-4px_12px_rgba(0,0,0,0.3)] px-1">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center {{ request()->routeIs('dashboard') ? 'text-zinc-900 dark:text-white font-semibold' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-400' }} transition-all" wire:navigate>
                <div class="flex items-center justify-center h-6">
                    <flux:icon.home class="size-5" />
                </div>
                <span class="text-[9px] min-[375px]:text-[10px] leading-none tracking-tight mt-1 text-center whitespace-nowrap overflow-hidden text-ellipsis max-w-[62px] min-[375px]:max-w-none">
                    {{ __('Dashboard') }}
                </span>
            </a>

            <!-- Workouts -->
            <a href="{{ route('workouts') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center {{ request()->routeIs('workouts') ? 'text-zinc-900 dark:text-white font-semibold' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-400' }} transition-all" wire:navigate>
                <div class="flex items-center justify-center h-6">
                    <flux:icon.bolt class="size-5" />
                </div>
                <span class="text-[9px] min-[375px]:text-[10px] leading-none tracking-tight mt-1 text-center whitespace-nowrap overflow-hidden text-ellipsis max-w-[62px] min-[375px]:max-w-none">
                    {{ __('Workouts') }}
                </span>
            </a>

            <!-- Manual Logs -->
            <a href="{{ route('manual-logs') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center {{ request()->routeIs('manual-logs') ? 'text-zinc-900 dark:text-white font-semibold' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-400' }} transition-all" wire:navigate>
                <div class="flex items-center justify-center h-6">
                    <flux:icon.clipboard class="size-5" />
                </div>
                <span class="text-[9px] min-[375px]:text-[10px] leading-none tracking-tight mt-1 text-center whitespace-nowrap overflow-hidden text-ellipsis max-w-[62px] min-[375px]:max-w-none">
                    {{ __('Manual Logs') }}
                </span>
            </a>

            <!-- Leaderboard -->
            <a href="{{ route('leaderboard') }}" class="flex flex-col items-center justify-center flex-1 h-full py-1 text-center {{ request()->routeIs('leaderboard') ? 'text-zinc-900 dark:text-white font-semibold' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-400' }} transition-all" wire:navigate>
                <div class="flex items-center justify-center h-6">
                    <flux:icon.trophy class="size-5" />
                </div>
                <span class="text-[9px] min-[375px]:text-[10px] leading-none tracking-tight mt-1 text-center whitespace-nowrap overflow-hidden text-ellipsis max-w-[62px] min-[375px]:max-w-none">
                    {{ __('Leaderboard') }}
                </span>
            </a>

            <!-- Profile (Dropdown) -->
            <flux:dropdown position="top" align="end" class="flex flex-col items-center justify-center flex-1 h-full cursor-pointer">
                <button class="flex flex-col items-center justify-center w-full h-full py-1 {{ request()->routeIs('profile.edit') || request()->routeIs('admin.settings') ? 'text-zinc-900 dark:text-white font-semibold' : 'text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-400' }} transition-all">
                    <div class="flex items-center justify-center h-6">
                        <flux:avatar
                            :src="auth()->user()->avatar_url"
                            :initials="auth()->user()->avatar_url ? null : auth()->user()->initials()"
                            class="size-6 border border-zinc-200 dark:border-zinc-700"
                        />
                    </div>
                    <span class="text-[9px] min-[375px]:text-[10px] leading-none tracking-tight mt-1 text-center whitespace-nowrap overflow-hidden text-ellipsis max-w-[62px] min-[375px]:max-w-none">
                        {{ __('Profile') }}
                    </span>
                </button>

                <flux:menu class="min-w-48">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :src="auth()->user()->avatar_url"
                                    :initials="auth()->user()->avatar_url ? null : auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('notifications')" icon="bell" wire:navigate>
                            {{ __('Notifications') }}
                            @php
                                $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <flux:badge size="sm" variant="danger" class="ml-auto">
                                    {{ $unreadCount }}
                                </flux:badge>
                            @endif
                        </flux:menu.item>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
