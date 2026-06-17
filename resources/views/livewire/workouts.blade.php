<div class="space-y-6" x-data="{ 
    timerInterval: null,
    running: false,
    duration: 0,
    reps: 0,
    multiplier: 1.0,
    projectedXp: 0,
    
    startTimer() {
        if (this.running) return;
        this.running = true;
        this.timerInterval = setInterval(() => {
            this.duration++;
            this.calculateXp();
        }, 1000);
    },
    
    stopTimer() {
        this.running = false;
        clearInterval(this.timerInterval);
    },
    
    resetTimer() {
        this.stopTimer();
        this.duration = 0;
        this.calculateXp();
    },
    
    adjustReps(amount) {
        this.reps = Math.max(0, this.reps + amount);
        this.calculateXp();
    },
    
    calculateXp() {
        let durationMinutes = this.duration / 60.0;
        let base = (this.reps * 0.5) + (durationMinutes * 0.2);
        this.projectedXp = Math.max(1, Math.round(base * this.multiplier));
    },

    formatTime(secs) {
        let m = Math.floor(secs / 60).toString().padStart(2, '0');
        let s = (secs % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }
}" x-on:workout-logged.window="resetTimer(); duration = 0; reps = 0; projectedXp = 0;">
    
    <!-- Top Layout: Title and History Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Exercises List (2 cols) -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- Category: Beginner -->
            <div>
                <h2 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Beginner Workouts (1.0x XP)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(collect($workouts)->where('difficulty', 'beginner') as $workout)
                        <div class="backdrop-blur-md bg-white/40 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between hover:shadow-md transition">
                            <div>
                                <span class="inline-flex items-center rounded-md bg-green-500/10 px-2 py-0.5 text-xs font-semibold text-green-600 dark:text-green-400 border border-green-500/10 mb-2 capitalize">
                                    {{ $workout->difficulty }}
                                </span>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base leading-tight">{{ __($workout->name) }}</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed line-clamp-2">{{ __($workout->description) }}</p>
                            </div>
                            <flux:button 
                                wire:click="selectWorkout({{ $workout->id }})" 
                                x-on:click="reps = 0; duration = 0; multiplier = {{ $workout->getDifficultyMultiplier() }}; projectedXp = 1; startTimer();"
                                variant="primary" 
                                size="sm" 
                                class="mt-4 w-full cursor-pointer justify-center"
                            >
                                Start Workout
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Category: Intermediate -->
            <div>
                <h2 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Intermediate Workouts (1.5x XP)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(collect($workouts)->where('difficulty', 'intermediate') as $workout)
                        <div class="backdrop-blur-md bg-white/40 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between hover:shadow-md transition">
                            <div>
                                <span class="inline-flex items-center rounded-md bg-blue-500/10 px-2 py-0.5 text-xs font-semibold text-blue-600 dark:text-blue-400 border border-blue-500/10 mb-2 capitalize">
                                    {{ $workout->difficulty }}
                                </span>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base leading-tight">{{ __($workout->name) }}</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed line-clamp-2">{{ __($workout->description) }}</p>
                            </div>
                            <flux:button 
                                wire:click="selectWorkout({{ $workout->id }})" 
                                x-on:click="reps = 0; duration = 0; multiplier = {{ $workout->getDifficultyMultiplier() }}; projectedXp = 1; startTimer();"
                                variant="primary" 
                                size="sm" 
                                class="mt-4 w-full cursor-pointer justify-center"
                            >
                                Start Workout
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Category: Advanced -->
            <div>
                <h2 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Advanced Workouts (2.0x XP)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(collect($workouts)->where('difficulty', 'advanced') as $workout)
                        <div class="backdrop-blur-md bg-white/40 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 flex flex-col justify-between hover:shadow-md transition">
                            <div>
                                <span class="inline-flex items-center rounded-md bg-red-500/10 px-2 py-0.5 text-xs font-semibold text-red-600 dark:text-red-400 border border-red-500/10 mb-2 capitalize">
                                    {{ $workout->difficulty }}
                                </span>
                                <h3 class="font-bold text-zinc-900 dark:text-white text-base leading-tight">{{ __($workout->name) }}</h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 leading-relaxed line-clamp-2">{{ __($workout->description) }}</p>
                            </div>
                            <flux:button 
                                wire:click="selectWorkout({{ $workout->id }})" 
                                x-on:click="reps = 0; duration = 0; multiplier = {{ $workout->getDifficultyMultiplier() }}; projectedXp = 1; startTimer();"
                                variant="primary" 
                                size="sm" 
                                class="mt-4 w-full cursor-pointer justify-center"
                            >
                                Start Workout
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- History Panel (1 col) -->
        <div class="space-y-6">
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white mb-4 flex items-center gap-2">
                    📋 Workout History Logs
                </h2>
                
                @if(count($history) === 0)
                    <div class="text-center py-8">
                        <span class="text-3xl text-zinc-400">📭</span>
                        <p class="text-sm text-zinc-500 mt-2">Belum ada riwayat workout. Mulai latihan pertamamu sekarang!</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                        @foreach($history as $log)
                            @php
                                $unit = $log->workout->type === 'run' ? 'km' : 'reps';
                            @endphp
                            <div class="p-3 bg-white/20 dark:bg-zinc-950/20 border border-zinc-200/50 dark:border-zinc-800/80 rounded-xl">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-zinc-800 dark:text-zinc-200 text-sm leading-tight">{{ __($log->workout->name) }}</h4>
                                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5 block">
                                            {{ $log->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center rounded bg-indigo-50 dark:bg-indigo-950 px-2 py-0.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-200/20">
                                        +{{ $log->xp_earned }} XP
                                    </span>
                                </div>
                                <div class="flex gap-4 mt-2 text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                    <span>Reps: <strong class="text-zinc-900 dark:text-white">{{ $log->reps }} {{ $unit }}</strong></span>
                                    <span>Duration: <strong class="text-zinc-900 dark:text-white">{{ floor($log->duration / 60) }}m {{ $log->duration % 60 }}s</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Live Workout Modal Dialog -->
    @if($showModal && $activeWorkout)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm transition-opacity" wire:ignore.self>
            <div class="relative w-full max-w-4xl overflow-hidden rounded-3xl bg-zinc-900 border border-zinc-800 shadow-2xl p-6 md:p-8 flex flex-col md:flex-row gap-6">
                <!-- Glowing layout borders -->
                <div class="absolute -top-20 -left-20 size-48 rounded-full bg-indigo-500/10 blur-3xl"></div>

                <!-- Left: Video Embed Iframe -->
                <div class="flex-1 min-h-[200px] md:min-h-[350px] bg-black rounded-2xl overflow-hidden relative border border-zinc-800">
                    @if($activeWorkout->embed_url)
                        <iframe class="absolute inset-0 size-full" src="{{ $activeWorkout->embed_url }}?autoplay=0&loop=1&playlist={{ last(explode('/', $activeWorkout->embed_url)) }}" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>
                    @else
                        <div class="flex flex-col items-center justify-center size-full text-zinc-600">
                            <span class="text-4xl">📹</span>
                            <p class="text-xs mt-2">No Video Tutorial available</p>
                        </div>
                    @endif
                </div>

                <!-- Right: Stopwatch & Reps Control Panel -->
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Header Details -->
                        <div class="flex justify-between items-start gap-4 mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white leading-tight">{{ __($activeWorkout->name) }}</h3>
                                <p class="text-xs text-zinc-400 mt-1 uppercase tracking-wider font-semibold">Live Tracking Session</p>
                            </div>
                            <span class="bg-indigo-500/20 text-indigo-400 text-xs px-2.5 py-1 rounded font-bold border border-indigo-500/30">
                                XP Multiplier: {{ $activeWorkout->getDifficultyMultiplier() }}x
                            </span>
                        </div>

                        <!-- Timer Widget -->
                        <div class="bg-zinc-950/60 border border-zinc-800 rounded-2xl p-4 text-center mb-4">
                            <span class="text-4xl font-mono font-black text-transparent bg-clip-text bg-gradient-to-r from-zinc-100 to-zinc-400 tracking-wider" x-text="formatTime(duration)">
                                00:00
                            </span>
                            <div class="flex justify-center gap-3 mt-3">
                                <button type="button" x-on:click="startTimer" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20">
                                    Start
                                </button>
                                <button type="button" x-on:click="stopTimer" class="px-4 py-1.5 rounded-lg text-xs font-bold text-zinc-300 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700">
                                    Pause
                                </button>
                                <button type="button" x-on:click="resetTimer" class="px-4 py-1.5 rounded-lg text-xs font-bold text-red-400 bg-red-950/30 hover:bg-red-950/60 border border-red-900/30">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <!-- Reps Counters -->
                        @php
                            $label = $activeWorkout->type === 'run' ? 'Distance (KM)' : 'Reps Counter';
                            $inc1 = $activeWorkout->type === 'run' ? 0.5 : 1;
                            $inc5 = $activeWorkout->type === 'run' ? 1.0 : 5;
                            $inc10 = $activeWorkout->type === 'run' ? 5.0 : 10;
                        @endphp
                        <div class="bg-zinc-950/60 border border-zinc-800 rounded-2xl p-4 text-center">
                            <span class="text-xs font-semibold text-zinc-400 tracking-wide uppercase block mb-1">{{ $label }}</span>
                            <div class="flex items-center justify-center gap-4">
                                <button type="button" x-on:click="adjustReps(-{{ $inc1 }})" class="size-9 rounded-full bg-zinc-800 text-lg font-bold text-zinc-200 hover:bg-zinc-700 border border-zinc-700">
                                    -
                                </button>
                                <input type="number" step="any" x-model.number="reps" x-on:input="calculateXp()" class="w-20 bg-zinc-900 border border-zinc-800 text-center text-2xl font-black text-white rounded-lg p-1" />
                                <button type="button" x-on:click="adjustReps({{ $inc1 }})" class="size-9 rounded-full bg-zinc-800 text-lg font-bold text-zinc-200 hover:bg-zinc-700 border border-zinc-700">
                                    +
                                </button>
                            </div>
                            <div class="flex justify-center gap-2 mt-3">
                                <button type="button" x-on:click="adjustReps({{ $inc5 }})" class="px-2.5 py-1 text-xs font-semibold text-zinc-400 bg-zinc-900 border border-zinc-800 rounded hover:bg-zinc-800">
                                    +{{ $inc5 }}
                                </button>
                                <button type="button" x-on:click="adjustReps({{ $inc10 }})" class="px-2.5 py-1 text-xs font-semibold text-zinc-400 bg-zinc-900 border border-zinc-800 rounded hover:bg-zinc-800">
                                    +{{ $inc10 }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- XP Projection Display & Submits -->
                    <div class="mt-6 border-t border-zinc-800 pt-4">
                        <div class="flex justify-between items-center mb-4 bg-gradient-to-r from-indigo-950/30 to-purple-950/30 p-3 rounded-xl border border-indigo-500/10">
                            <div>
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block">Estimated Reward</span>
                                <span class="text-sm font-bold text-indigo-300">Workout + Habit XP</span>
                            </div>
                            <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-400 shadow-glow" x-text="`+${projectedXp} XP`">
                                +1 XP
                            </span>
                        </div>

                        <div class="flex gap-3">
                            <flux:button 
                                x-on:click="stopTimer(); $wire.set('duration', duration); $wire.set('reps', reps); $wire.submitWorkout();"
                                variant="primary" 
                                class="flex-1 justify-center cursor-pointer font-bold"
                            >
                                Complete Workout
                            </flux:button>
                            <flux:button 
                                wire:click="cancelWorkout" 
                                x-on:click="stopTimer();" 
                                variant="subtle" 
                                class="cursor-pointer"
                            >
                                Cancel
                            </flux:button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
