<div class="space-y-6">
    
    <!-- Grid for Rankings and Title Milestones -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Leaderboard Rankings (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            🏆 Global Leaderboard
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Peringkat berdasarkan akumulasi XP pengguna.</p>
                    </div>
                    
                    <div class="w-full sm:w-64">
                        <flux:select wire:model.live="selectedType" class="cursor-pointer">
                            <flux:select.option value="">Semua Olahraga ({{ number_format($currentUser->xp_total) }} XP)</flux:select.option>
                            @foreach($workoutTypes as $type => $label)
                                <flux:select.option value="{{ $type }}">{{ $label }} ({{ number_format($currentUserXp[$type] ?? 0) }} XP)</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-400 font-semibold text-xs uppercase tracking-wider">
                                <th class="py-3 px-4 text-center w-16">Rank</th>
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Tier & Level</th>
                                <th class="py-3 px-4 text-right">
                                    {{ $selectedType ? ($workoutTypes[$selectedType] ?? ucwords(str_replace('_', ' ', $selectedType))) . ' XP' : 'Total XP' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/50 dark:divide-zinc-800/50">
                             @foreach($users as $index => $u)
                                @php
                                    $rank = $index + 1;
                                    $isMe = $u->id === $currentUser->id;
                                    $rankVisual = match($rank) {
                                        1 => '👑 🥇',
                                        2 => '🥈',
                                        3 => '🥉',
                                        default => $rank,
                                    };
                                @endphp
                                <tr class="hover:bg-zinc-100/50 dark:hover:bg-zinc-950/20 transition {{ $isMe ? 'bg-indigo-500/5 dark:bg-indigo-500/5 font-semibold border-l-2 border-indigo-500' : '' }}">
                                    <td class="py-4 px-4 text-center font-bold text-zinc-700 dark:text-zinc-300">
                                        {!! $rankVisual !!}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-9 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 font-bold text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700/50">
                                                {{ $u->initials() }}
                                            </div>
                                            <div>
                                                <span class="text-zinc-900 dark:text-white flex items-center gap-1.5">
                                                    {{ $u->name }}
                                                    @if($isMe)
                                                        <span class="text-[10px] bg-indigo-500/10 text-indigo-400 px-1.5 py-0.2 rounded border border-indigo-500/20">Kamu</span>
                                                    @endif
                                                </span>
                                                @if($u->title)
                                                    <span class="text-[10px] text-amber-500 font-medium">🏆 {{ $u->title }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-col">
                                            <span class="text-zinc-800 dark:text-zinc-200 text-xs font-semibold">Tier: {{ $u->getTier() }}</span>
                                            <span class="text-zinc-400 text-[10px]">Level {{ $u->level }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-right font-mono font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ number_format($selectedType ? $u->filtered_xp : $u->xp_total) }} XP
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Title Milestone Unlocks (1 col) -->
        <div class="space-y-6">
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white mb-2 flex items-center gap-2">
                    🎖️ Title Milestones
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6">Capai level tertentu untuk membuka gelar bergengsi profil Anda.</p>

                @php
                    $milestones = [
                        ['level' => 10, 'title' => 'Awakened', 'desc' => 'Membangkitkan kesadaran berolahraga secara teratur.'],
                        ['level' => 20, 'title' => 'Relentless', 'desc' => 'Tanpa henti dan pantang menyerah menghadapi lelah.'],
                        ['level' => 40, 'title' => 'Iron Body', 'desc' => 'Melatih otot tubuh hingga memiliki ketahanan bagai besi.'],
                        ['level' => 60, 'title' => 'Unbreakable', 'desc' => 'Mencapai ketangguhan mental dan fisik yang tidak tergoyahkan.'],
                        ['level' => 100, 'title' => 'Limit Breaker', 'desc' => 'Melampaui batas kemampuan manusia biasa. Legenda hidup.'],
                    ];
                @endphp

                <div class="space-y-4">
                    <!-- Option to equip No Title -->
                    <div class="flex items-center justify-between p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white/20 dark:bg-zinc-950/20">
                        <div>
                            <h4 class="text-xs font-bold text-zinc-500">Tanpa Gelar (Polos)</h4>
                            <p class="text-[10px] text-zinc-400 mt-0.5">Sembunyikan gelar pada profil Anda.</p>
                        </div>
                        @if(is_null($currentUser->title))
                            <span class="text-xs font-bold text-emerald-500 flex items-center gap-1">
                                ✓ Aktif
                            </span>
                        @else
                            <flux:button wire:click="equipTitle('')" variant="subtle" size="sm" class="cursor-pointer">
                                Pasang
                            </flux:button>
                        @endif
                    </div>

                    @foreach($milestones as $m)
                        @php
                            $isUnlocked = $currentUser->level >= $m['level'];
                            $isActive = $currentUser->title === $m['title'];
                        @endphp
                        <div class="relative overflow-hidden rounded-xl border p-4 transition {{ $isUnlocked ? 'border-zinc-200 dark:border-zinc-800 bg-white/30 dark:bg-zinc-900/30' : 'border-zinc-100 dark:border-zinc-900/30 bg-zinc-50/50 dark:bg-zinc-900/10 opacity-60' }}">
                            
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-extrabold text-sm {{ $isUnlocked ? 'text-amber-500' : 'text-zinc-500' }}">
                                            {{ $m['title'] }}
                                        </h3>
                                        <span class="text-[9px] bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-1.5 py-0.2 rounded font-mono">
                                            LVL {{ $m['level'] }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1 leading-normal">{{ $m['desc'] }}</p>
                                </div>

                                <!-- Action / Status -->
                                @if($isUnlocked)
                                    @if($isActive)
                                        <span class="text-xs font-bold text-emerald-500 flex items-center gap-1">
                                            ✓ Aktif
                                        </span>
                                    @else
                                        <flux:button wire:click="equipTitle('{{ $m['title'] }}')" variant="subtle" size="sm" class="cursor-pointer">
                                            Pasang
                                        </flux:button>
                                    @endif
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-600 flex items-center gap-1 font-semibold">
                                        🔒 Locked
                                    </span>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
