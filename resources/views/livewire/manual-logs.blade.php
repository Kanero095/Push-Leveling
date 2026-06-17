<div class="space-y-6">
    
    <!-- Warning Alert banner -->
    <div class="rounded-xl border border-amber-500/20 bg-amber-500/10 p-4 shadow-sm backdrop-blur-sm">
        <div class="flex items-start gap-3">
            <span class="text-2xl leading-none">⚠️</span>
            <div>
                <h3 class="font-bold text-amber-800 dark:text-amber-400 text-sm">Catatan Latihan Manual</h3>
                <p class="text-xs text-amber-700 dark:text-amber-500/90 leading-relaxed mt-1">
                    Latihan manual di bawah ini **TIDAK mempengaruhi** sistem XP, Leveling, Tier, Daily Mission, maupun Hell Mode. Fitur ini disediakan murni sebagai catatan jurnal latihan pribadi Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- Layout: Grid Form and History -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Form Card -->
        <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xl h-fit">
            <h2 class="text-base font-bold text-zinc-900 dark:text-white mb-4">
                📝 Catat Latihan Baru
            </h2>

            <form wire:submit="submitLog" class="space-y-4">
                <flux:input 
                    wire:model="activityName" 
                    :label="__('Nama Aktivitas')" 
                    type="text" 
                    required 
                    placeholder="Contoh: Berenang, Angkat Beban" 
                />

                <flux:input 
                    wire:model="duration" 
                    :label="__('Durasi (Menit)')" 
                    type="number" 
                    required 
                    placeholder="Misal: 45" 
                />

                <flux:textarea 
                    wire:model="note" 
                    :label="__('Catatan (Opsional)')" 
                    placeholder="Bagaimana perasaanmu setelah latihan ini?" 
                    rows="3" 
                />

                <flux:button variant="primary" type="submit" class="w-full cursor-pointer justify-center">
                    Simpan Catatan
                </flux:button>
            </form>
        </div>

        <!-- Right: History List (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="backdrop-blur-md bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xl">
                <h2 class="text-base font-bold text-zinc-900 dark:text-white mb-4">
                    📅 Riwayat Jurnal Manual
                </h2>

                @if(count($logs) === 0)
                    <div class="text-center py-12 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <span class="text-4xl">🗒️</span>
                        <p class="text-sm text-zinc-500 mt-2">Belum ada catatan manual. Mulai mendokumentasikan aktivitas fisikmu!</p>
                    </div>
                @else
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($logs as $index => $log)
                                @php
                                    $isLast = $index === count($logs) - 1;
                                @endphp
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$isLast)
                                            <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700/50">
                                                    🏋️
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm font-bold text-zinc-900 dark:text-white flex justify-between items-baseline gap-2">
                                                        <span>{{ $log->activity_name }}</span>
                                                        <span class="text-xs font-normal text-zinc-400 dark:text-zinc-500">
                                                            {{ $log->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <p class="mt-0.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                                                        Durasi: {{ $log->duration }} menit
                                                    </p>
                                                </div>
                                                @if($log->note)
                                                    <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 bg-white/20 dark:bg-zinc-950/20 rounded-lg p-3 border border-zinc-200/50 dark:border-zinc-800/80">
                                                        <p>{{ $log->note }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
