<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-zinc-900 via-neutral-900 to-zinc-950 p-6 text-white border border-zinc-800 shadow-2xl">
        <div class="absolute -right-16 -top-16 size-48 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 size-48 rounded-full bg-emerald-500/10 blur-3xl"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight flex items-center gap-2">
                    🔔 {{ __('Notifikasi Saya') }}
                </h1>
                <p class="text-xs text-zinc-400 mt-1">
                    {{ __('Daftar pengingat misi harian, aktivitas, serta riwayat perkembangan akun Anda.') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <span class="inline-flex items-center rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-bold text-indigo-400 border border-indigo-500/30 animate-pulse">
                        {{ $unreadCount }} {{ __('Belum Dibaca') }}
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-zinc-800 px-3 py-1 text-xs font-semibold text-zinc-500 border border-zinc-700">
                        {{ __('Semua Dibaca') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    @if($notifications->isNotEmpty())
        <div class="flex justify-between items-center gap-4 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-200/50 dark:border-zinc-800/50 p-3 rounded-xl backdrop-blur-sm">
            <flux:button wire:click="markAllAsRead" size="sm" variant="subtle" class="cursor-pointer hover:bg-zinc-200/50" :disabled="$unreadCount === 0">
                <div class="flex items-center gap-1.5">
                    <flux:icon.check class="size-4" />
                    <span>{{ __('Tandai Semua Dibaca') }}</span>
                </div>
            </flux:button>

            <flux:button wire:click="clearAll" size="sm" variant="danger" class="cursor-pointer bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 border border-red-500/20">
                <div class="flex items-center gap-1.5">
                    <flux:icon.trash class="size-4" />
                    <span>{{ __('Hapus Semua') }}</span>
                </div>
            </flux:button>
        </div>
    @endif

    <!-- Notifications List -->
    <div class="space-y-4">
        @forelse($notifications as $notif)
            @php
                $isUnread = !$notif->is_read;
                $icon = match($notif->type) {
                    'morning' => '🌅',
                    'evening' => '🌙',
                    default => 'ℹ️',
                };
                $iconBg = match($notif->type) {
                    'morning' => 'from-amber-500/20 to-orange-500/10 text-amber-500 border-amber-500/20',
                    'evening' => 'from-indigo-500/20 to-purple-500/10 text-indigo-400 border-indigo-500/20',
                    default => 'from-zinc-500/20 to-neutral-500/10 text-zinc-400 border-zinc-700',
                };
            @endphp
            <div 
                class="group relative overflow-hidden rounded-xl border transition duration-300 shadow-sm hover:shadow-md flex gap-4 p-4 items-start {{ $isUnread ? 'border-indigo-500/30 bg-indigo-500/5 dark:bg-indigo-950/5' : 'border-zinc-200 dark:border-zinc-800 bg-white/40 dark:bg-zinc-900/10' }}"
            >
                <!-- Unread status highlight bar -->
                @if($isUnread)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                @endif

                <!-- Icon Container -->
                <div class="flex-shrink-0 flex size-10 items-center justify-center rounded-xl bg-gradient-to-br border shadow-sm {{ $iconBg }}">
                    <span class="text-xl">{{ $icon }}</span>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                            {{ $notif->type === 'morning' ? __('Pengingat Pagi') : ($notif->type === 'evening' ? __('Pengingat Malam') : __('Info')) }}
                        </span>
                        
                        <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 whitespace-nowrap">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-sm mt-1 leading-relaxed text-zinc-700 dark:text-zinc-300 {{ $isUnread ? 'font-semibold' : 'font-normal' }}">
                        {{ $notif->message }}
                    </p>

                    <!-- Status Display -->
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold tracking-wide {{ $notif->status === 'sent' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400' }}">
                            {{ strtoupper($notif->status) }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons (Hover states) -->
                <div class="flex-shrink-0 flex items-center gap-1 self-center">
                    @if($isUnread)
                        <flux:tooltip :content="__('Tandai telah dibaca')" position="top">
                            <flux:button 
                                wire:click="markAsRead({{ $notif->id }})" 
                                size="sm" 
                                variant="subtle" 
                                square
                                class="cursor-pointer hover:bg-indigo-500/20 text-indigo-500"
                            >
                                <flux:icon.check class="size-4" />
                            </flux:button>
                        </flux:tooltip>
                    @endif

                    <flux:tooltip :content="__('Hapus')" position="top">
                        <flux:button 
                            wire:click="deleteNotification({{ $notif->id }})" 
                            size="sm" 
                            variant="subtle" 
                            square
                            class="cursor-pointer hover:bg-red-500/20 hover:text-red-600 text-zinc-400 dark:text-zinc-500"
                        >
                            <flux:icon.trash class="size-4" />
                        </flux:button>
                    </flux:tooltip>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-12 px-4 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 bg-white/20 dark:bg-zinc-900/10 backdrop-blur-sm shadow-inner">
                <div class="inline-flex size-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500/10 to-purple-500/10 text-indigo-500 border border-indigo-500/10 shadow-sm mb-4">
                    <flux:icon.bell class="size-8" />
                </div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Kotak Masuk Kosong') }}</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-xs mx-auto">
                    {{ __('Semua aman! Tidak ada notifikasi pengingat latihan yang tersedia untuk saat ini.') }}
                </p>
            </div>
        @endforelse
    </div>
</div>
