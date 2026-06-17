<div class="space-y-6">
    <!-- Welcome Header / User Profile Summary -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-zinc-900 via-neutral-900 to-zinc-950 p-6 text-white border border-zinc-800 shadow-2xl">
        <div class="absolute -right-16 -top-16 size-48 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 size-48 rounded-full bg-emerald-500/10 blur-3xl"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <!-- Left: Avatar, Title, Name, Tier -->
            <div class="flex items-center gap-4">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="size-16 rounded-2xl object-cover shadow-lg border border-zinc-800" />
                @else
                    <div class="flex size-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-2xl font-bold shadow-lg shadow-indigo-500/30">
                        {{ $user->initials() }}
                    </div>
                @endif
                <div>
                    @if($user->title)
                        <span class="inline-flex items-center rounded-md bg-amber-500/10 px-2.5 py-0.5 text-xs font-semibold text-amber-400 border border-amber-500/20 mb-1">
                            🏆 {{ $user->title }}
                        </span>
                    @endif
                    <h1 class="text-2xl font-bold tracking-tight">{{ $user->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center rounded-full bg-indigo-500/20 px-2.5 py-0.5 text-xs font-medium text-indigo-300">
                            Tier: {{ $user->getTier() }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-medium text-emerald-300 capitalize">
                            {{ $user->user_level }} Mode
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Level and XP Progress -->
            <div class="flex-1 max-w-md w-full md:text-right">
                <div class="flex justify-between items-baseline mb-2 md:justify-end md:gap-3">
                    <span class="text-sm font-semibold text-zinc-400">XP Progress</span>
                    <span class="text-3xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">
                        LVL {{ $user->level }}
                    </span>
                </div>
                
                @php $xp = $user->xp_progress; @endphp
                <div class="relative w-full h-3 bg-zinc-800 rounded-full overflow-hidden border border-zinc-700/50 shadow-inner">
                    <div class="absolute top-0 left-0 h-full rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-[0_0_8px_rgba(99,102,241,0.5)] transition-all duration-500" style="width: {{ $xp['percentage'] }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-zinc-400 mt-1.5">
                    <span>{{ number_format($xp['current']) }} / {{ number_format($xp['target']) }} XP</span>
                    <span>{{ $xp['percentage'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Daily Missions & Hell Mode -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Perkembangan Latihan Chart Card (Reps) -->
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200/50 dark:border-zinc-800/50 shadow-xl rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            📊 Perkembangan Latihan (Reps)
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Pantau total repetisi (reps) olahraga Anda secara berkala.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <!-- Timeframe Filter -->
                        <div class="flex items-center bg-zinc-100/80 dark:bg-zinc-800/80 p-0.5 rounded-lg border border-zinc-200/50 dark:border-zinc-700/50 shadow-inner">
                            <button wire:click="$set('timeframe', 'weekly')" class="px-2.5 py-1 text-xs font-bold rounded-md transition duration-200 {{ $timeframe === 'weekly' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">Mingguan</button>
                            <button wire:click="$set('timeframe', 'monthly')" class="px-2.5 py-1 text-xs font-bold rounded-md transition duration-200 {{ $timeframe === 'monthly' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">Bulanan</button>
                            <button wire:click="$set('timeframe', 'yearly')" class="px-2.5 py-1 text-xs font-bold rounded-md transition duration-200 {{ $timeframe === 'yearly' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">Tahunan</button>
                        </div>

                        <!-- Workout Type Filter Select -->
                        <div class="relative w-full sm:w-auto">
                            <select wire:model.live="selectedType" class="w-full sm:w-auto text-xs font-bold rounded-lg bg-zinc-100/80 dark:bg-zinc-800/80 border border-zinc-200/50 dark:border-zinc-700/50 text-zinc-800 dark:text-zinc-200 px-2.5 py-1.5 pr-8 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200 cursor-pointer shadow-sm">
                                <option value="all">🏋️ Semua Olahraga</option>
                                @foreach($this->workoutTypes as $type => $label)
                                    <option value="{{ $type }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Chart.js Container with Alpine.js -->
                <div x-data="{
                    init() {
                        const ctx = document.getElementById('workoutRepsChart').getContext('2d');
                        
                        // Create modern gradient fill
                        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
                        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
                        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

                        window.workoutRepsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @js($chartLabels),
                                datasets: [{
                                    label: 'Reps Dilakukan',
                                    data: @js($chartRepsValues),
                                    borderColor: '#6366f1',
                                    borderWidth: 2.5,
                                    backgroundColor: gradient,
                                    fill: true,
                                    tension: 0.35,
                                    pointBackgroundColor: '#6366f1',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 1.5,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#4f46e5',
                                    pointHoverBorderColor: '#ffffff',
                                    pointHoverBorderWidth: 2,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        backgroundColor: 'rgba(24, 24, 27, 0.95)',
                                        titleColor: '#ffffff',
                                        titleFont: { weight: 'bold', family: 'Inter, sans-serif' },
                                        bodyColor: '#e4e4e7',
                                        bodyFont: { family: 'Inter, sans-serif' },
                                        borderColor: 'rgba(63, 63, 70, 0.5)',
                                        borderWidth: 1,
                                        padding: 10,
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return '💪 ' + context.parsed.y + ' reps';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            color: '#71717a',
                                            font: { family: 'Inter, sans-serif', size: 10, weight: '500' }
                                        }
                                    },
                                    y: {
                                        grid: { color: 'rgba(113, 113, 122, 0.12)' },
                                        ticks: {
                                            color: '#71717a',
                                            font: { family: 'Inter, sans-serif', size: 10, weight: '500' },
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    }
                }"
                x-on:chart-updated.window="
                    const data = $event.detail[0] || $event.detail;
                    const chart = window.workoutRepsChart;
                    if (chart && data) {
                        const labels = data.labels || $event.detail.labels;
                        const repsValues = data.repsValues || $event.detail.repsValues;
                        if (labels) chart.data.labels = labels;
                        if (repsValues) chart.data.datasets[0].data = repsValues;
                        chart.update();
                    }
                "
                wire:ignore class="relative h-64 sm:h-72 w-full mt-2">
                    <canvas id="workoutRepsChart"></canvas>
                </div>
            </div>

            <!-- Perolehan XP Chart Card -->
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200/50 dark:border-zinc-800/50 shadow-xl rounded-2xl p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            ✨ Perolehan XP
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Pantau perolehan total XP olahraga Anda secara berkala.</p>
                    </div>
                </div>

                <!-- Chart.js Container with Alpine.js -->
                <div x-data="{
                    init() {
                        const ctx = document.getElementById('workoutXpChart').getContext('2d');
                        
                        // Create modern gradient fill (emerald color)
                        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                        window.workoutXpChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @js($chartLabels),
                                datasets: [{
                                    label: 'XP Diperoleh',
                                    data: @js($chartXpValues),
                                    borderColor: '#10b981',
                                    borderWidth: 2.5,
                                    backgroundColor: gradient,
                                    fill: true,
                                    tension: 0.35,
                                    pointBackgroundColor: '#10b981',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 1.5,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointHoverBackgroundColor: '#059669',
                                    pointHoverBorderColor: '#ffffff',
                                    pointHoverBorderWidth: 2,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        backgroundColor: 'rgba(24, 24, 27, 0.95)',
                                        titleColor: '#ffffff',
                                        titleFont: { weight: 'bold', family: 'Inter, sans-serif' },
                                        bodyColor: '#e4e4e7',
                                        bodyFont: { family: 'Inter, sans-serif' },
                                        borderColor: 'rgba(63, 63, 70, 0.5)',
                                        borderWidth: 1,
                                        padding: 10,
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return '🔥 ' + context.parsed.y + ' XP';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            color: '#71717a',
                                            font: { family: 'Inter, sans-serif', size: 10, weight: '500' }
                                        }
                                    },
                                    y: {
                                        grid: { color: 'rgba(113, 113, 122, 0.12)' },
                                        ticks: {
                                            color: '#71717a',
                                            font: { family: 'Inter, sans-serif', size: 10, weight: '500' },
                                            precision: 0
                                        }
                                    }
                                }
                            }
                        });
                    }
                }" 
                x-on:chart-updated.window="
                    const data = $event.detail[0] || $event.detail;
                    const chart = window.workoutXpChart;
                    if (chart && data) {
                        const labels = data.labels || $event.detail.labels;
                        const xpValues = data.xpValues || $event.detail.xpValues;
                        if (labels) chart.data.labels = labels;
                        if (xpValues) chart.data.datasets[0].data = xpValues;
                        chart.update();
                    }
                "
                wire:ignore class="relative h-64 sm:h-72 w-full mt-2">
                    <canvas id="workoutXpChart"></canvas>
                </div>
            </div>
            
            <!-- Daily Missions -->
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200/50 dark:border-zinc-800/50 shadow-xl rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            🎯 Daily Missions
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Misi diperbarui setiap hari pada 00:00. Selesaikan untuk tambahan bonus XP!</p>
                    </div>
                    <span class="text-xs bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-md font-medium border border-indigo-100 dark:border-indigo-900/50">
                        Target Tetap
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($this->dailyMissions as $um)
                        @php
                            $completed = $um->is_completed;
                            $percentage = $um->progress_percentage;
                            $typeIcon = match($um->mission->type) {
                                'push_up' => '💪',
                                'sit_up' => '🧘',
                                'squat' => '🦵',
                                'run' => '🏃',
                                default => '🎯',
                            };
                            $typeLabel = match($um->mission->type) {
                                'push_up' => 'Push-ups',
                                'sit_up' => 'Sit-ups',
                                'squat' => 'Squats',
                                'run' => 'Run Distance',
                                default => $um->mission->name,
                            };
                            $unit = $um->mission->type === 'run' ? 'km' : 'reps';
                        @endphp
                        <div class="relative overflow-hidden rounded-xl border {{ $completed ? 'border-emerald-500/20 bg-emerald-50/10 dark:bg-emerald-950/5' : 'border-zinc-200 dark:border-zinc-800 bg-white/20 dark:bg-zinc-900/20' }} p-4 transition duration-300 shadow-sm hover:shadow-md">
                            
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-2xl">{{ $typeIcon }}</span>
                                    <div>
                                        <h3 class="font-bold text-zinc-800 dark:text-zinc-200 text-sm leading-tight">{{ $typeLabel }}</h3>
                                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-semibold tracking-wide uppercase">Mission Type</span>
                                    </div>
                                </div>
                                @if($completed)
                                    <span class="inline-flex size-6 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30 text-xs font-bold">
                                        ✓
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-indigo-500 dark:text-indigo-400">+{{ $um->mission->base_xp }} XP</span>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div class="relative w-full h-2.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden mt-3 shadow-inner">
                                <div class="absolute top-0 left-0 h-full rounded-full bg-gradient-to-r {{ $completed ? 'from-emerald-400 to-teal-500' : 'from-indigo-500 to-purple-500' }} transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>

                            <div class="flex justify-between items-center text-xs mt-2 text-zinc-600 dark:text-zinc-400">
                                <span class="font-medium">Progress: {{ $um->current_progress }} / {{ $um->target_snapshot }} {{ $unit }}</span>
                                <span class="font-bold">{{ $percentage }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Hell Mode Weekly Card -->
            <div class="relative overflow-hidden rounded-2xl border border-red-500/20 dark:border-red-950 bg-gradient-to-b from-red-500/5 via-zinc-900/10 to-zinc-950/20 p-6 shadow-xl backdrop-blur-sm">
                <!-- Glowing red aura background -->
                <div class="absolute -right-20 -top-20 size-48 rounded-full bg-red-500/10 blur-3xl"></div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 relative z-10">
                    <div>
                        <h2 class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                            💀 Hell Mode (Weekly Challenge)
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Syarat: Selesaikan semua misi harian &ge; 5 hari dalam seminggu (Senin - Sabtu).</p>
                    </div>

                    <!-- Readiness Badge -->
                    @if($weeklyProgress->hell_mode_ready)
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-500/20 px-3 py-1 text-xs font-bold text-red-400 border border-red-500/30 animate-pulse">
                            🔥 READY FOR TOMORROW
                        </span>
                    @elseif($weeklyProgress->hell_mode_used)
                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-500/20 px-3 py-1 text-xs font-bold text-orange-400 border border-orange-500/30">
                            💀 HELL MODE ACTIVE TODAY
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-zinc-200 dark:bg-zinc-800 px-3 py-1 text-xs font-semibold text-zinc-600 dark:text-zinc-400 border border-zinc-300 dark:border-zinc-700">
                            🔒 NOT READY ({{ $weeklyProgress->completed_days }}/5 Days)
                        </span>
                    @endif
                </div>

                <!-- Weekly Tracker Progress Tracker -->
                <div class="grid grid-cols-7 gap-2 mb-6">
                    @php
                        // Map days of the week starting from Monday (1) to Sunday (7)
                        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                        $completedDays = $weeklyProgress->completed_days;
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $isSunday = $i === 6;
                            // Visual indication of days counted
                            // If complete days matches or exceed current slot index (approximate representation)
                            $isActive = $i < $completedDays;
                        @endphp
                        <div class="flex flex-col items-center justify-center p-2 rounded-lg border {{ $isActive ? 'border-red-500/30 bg-red-500/10 text-red-400' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-100/40 dark:bg-zinc-900/30 text-zinc-400' }}">
                            <span class="text-[10px] uppercase font-bold tracking-wider leading-none">{{ $days[$i] }}</span>
                            <span class="text-lg mt-1 leading-none">{{ $isActive ? '🔥' : '◯' }}</span>
                        </div>
                    @endfor
                </div>

                <div class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed border-t border-zinc-200/50 dark:border-zinc-800/50 pt-4">
                    <strong>Aturan Hell Mode:</strong> Target dikali dua (**x2**) pada keesokan hari setelah status berubah menjadi **Ready**. Setelah Hell Mode digunakan, target kembali normal dan tidak akan muncul kembali di minggu yang sama. Hari Minggu tidak dapat memicu Hell Mode.
                </div>
            </div>

        </div>

        <!-- Right: AI Guide Assistant & Developer Sandbox -->
        <div class="space-y-6">
            
            <!-- AI Guide Assistant Card -->
            @php
                $moodBorder = match($aiMood) {
                    'celebratory' => 'border-emerald-500/40 shadow-[0_0_15px_rgba(16,185,129,0.15)] dark:bg-emerald-950/10',
                    'warning' => 'border-red-500/40 shadow-[0_0_15px_rgba(239,68,68,0.15)] dark:bg-red-950/10',
                    'pumped' => 'border-indigo-500/40 shadow-[0_0_15px_rgba(99,102,241,0.15)] dark:bg-indigo-950/10',
                    default => 'border-zinc-200 dark:border-zinc-800 dark:bg-zinc-900/40',
                };
                $avatarBg = match($aiMood) {
                    'celebratory' => 'from-emerald-400 to-teal-500',
                    'warning' => 'from-red-500 to-orange-600',
                    'pumped' => 'from-indigo-500 to-purple-600',
                    default => 'from-zinc-500 to-neutral-600',
                };
            @endphp
            <div class="backdrop-blur-md bg-white/40 border rounded-2xl p-6 transition duration-300 {{ $moodBorder }}">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br text-lg shadow-md {{ $avatarBg }}">
                        🤖
                    </div>
                    <div>
                        <h3 class="font-extrabold text-zinc-900 dark:text-white leading-tight">AI Workout Guide</h3>
                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-widest">Rule-Based Advisor</span>
                    </div>
                </div>
                
                <div class="relative bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-100 dark:border-zinc-800/80 rounded-xl p-4">
                    <div class="absolute -top-2 left-4 size-3 bg-zinc-50 dark:bg-zinc-950/40 border-l border-t border-zinc-100 dark:border-zinc-800/80 rotate-45"></div>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed italic">
                        "{!! $aiRecommendation !!}"
                    </p>
                </div>

                <div class="flex justify-end mt-4">
                    <span class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-2 py-0.5 rounded font-mono">
                        System: Dynamic Rule-Engine
                    </span>
                </div>
            </div>

            @if($user->is_admin)
            <!-- Developer Simulation Sandbox -->
            <div class="backdrop-blur-md bg-white/40 dark:bg-zinc-900/20 border border-amber-500/10 dark:border-amber-950/50 rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">🛠️</span>
                    <h3 class="font-bold text-zinc-900 dark:text-white text-sm">Developer Simulation Sandbox</h3>
                </div>
                
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4 leading-normal">
                    Gunakan tombol di bawah ini untuk mensimulasikan berbagai kondisi workout & habit dengan cepat untuk mempermudah evaluasi fitur.
                </p>

                <div class="space-y-2.5">
                    <flux:button wire:click="simulateMissionCompletion" variant="subtle" class="w-full text-left justify-start cursor-pointer hover:bg-zinc-200/50">
                        ⚡ Selesaikan Semua Misi Hari Ini
                    </flux:button>
                    
                    <flux:button wire:click="toggleHellModeReady" variant="subtle" class="w-full text-left justify-start cursor-pointer hover:bg-zinc-200/50">
                        👹 Force Hell Mode: Ready (Bukan Minggu)
                    </flux:button>

                    <flux:button wire:click="simulateNewDay" variant="subtle" class="w-full text-left justify-start cursor-pointer hover:bg-zinc-200/50">
                        🔄 Simulasi Hari Baru (Generate Misi)
                    </flux:button>

                    <flux:button wire:click="triggerDailyReset" variant="subtle" class="w-full text-left justify-start cursor-pointer hover:bg-zinc-200/50 text-red-500 dark:text-red-400">
                        🗑️ Reset Progress Hari Ini ke 0%
                    </flux:button>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>
