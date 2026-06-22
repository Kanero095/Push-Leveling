    <?php

    use App\Models\Notification;
    use App\Models\User;
    use App\Models\UserMission;
    use App\Services\DailyResetService;
    use Carbon\Carbon;
    use Illuminate\Foundation\Inspiring;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Schedule;

    // Inspiring quote command
    Artisan::command('inspire', function () {
        $this->comment(Inspiring::quote());
    })->purpose('Display an inspiring quote');

    // Command: Generate Daily Missions
    Artisan::command('missions:generate', function () {
        $this->info('Generating daily missions...');
        $service = new DailyResetService;
        $service->generateMissionsForAllUsers(Carbon::today());
        $this->info('Daily missions generated successfully!');
    })->purpose('Generate daily missions for all users');

    // Command: Morning Notifications (07:00)
    Artisan::command('notifications:morning', function () {
        $this->info('Sending morning notifications...');
        $users = User::all();

        foreach ($users as $user) {
            $message = "Halo {$user->name}! Mulai harimu dengan sehat. Selesaikan daily mission kamu hari ini untuk mendapatkan XP dan naik level!";

            $emailStatus = 'sent';
            // Send mail log
            try {
                Mail::raw($message, function ($mail) use ($user) {
                    $mail->to($user->email)->subject('Workout Tracker - Mulai Harimu!');
                });
            } catch (\Throwable $e) {
                $emailStatus = 'failed';
                Log::error("Failed to send morning mail to {$user->email}: ".$e->getMessage());
            }

            // Log to database
            Notification::create([
                'user_id' => $user->id,
                'type' => 'morning',
                'message' => $message,
                'status' => $emailStatus,
            ]);
        }

        $this->info('Morning notifications sent.');
    })->purpose('Send morning workout reminders to all users');

    // Command: Evening Notifications (21:00)
    Artisan::command('notifications:evening', function () {
        $this->info('Checking and sending evening reminders...');
        $users = User::all();

        foreach ($users as $user) {
            // Check if user has uncompleted missions today
            $hasUncompleted = UserMission::where('user_id', $user->id)
                ->whereDate('date', Carbon::today())
                ->where('is_completed', false)
                ->exists();

            if ($hasUncompleted) {
                $message = "Halo {$user->name}! Jangan lupa selesaikan daily mission kamu malam ini sebelum reset. Tetap konsisten!";

                $emailStatus = 'sent';
                // Send mail log
                try {
                    Mail::raw($message, function ($mail) use ($user) {
                        $mail->to($user->email)->subject('Workout Tracker - Pengingat Misi Malam');
                    });
                } catch (\Throwable $e) {
                    $emailStatus = 'failed';
                    Log::error("Failed to send evening mail to {$user->email}: ".$e->getMessage());
                }

                // Log to database
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'evening',
                    'message' => $message,
                    'status' => $emailStatus,
                ]);
            }
        }

        $this->info('Evening reminders processed.');
    })->purpose('Send evening reminders to users with incomplete tasks');

    // Command: Mail Diagnostics
    Artisan::command('mail:diagnose {--email= : Email tujuan untuk tes pengiriman}', function () {
        $this->info('=== DIAGNOSTIK KONFIGURASI EMAIL ===');
        
        $mailer = config('mail.default');
        $this->line("Mailer Default: " . ($mailer ?: 'NULL'));
        
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $encryption = config('mail.mailers.smtp.encryption');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->line("SMTP Host: " . ($host ?: 'NULL'));
        $this->line("SMTP Port: " . ($port ?: 'NULL'));
        $this->line("SMTP Encryption: " . ($encryption ?: 'NULL'));
        $this->line("SMTP Username: " . ($username ?: 'NULL'));
        
        if (empty($password)) {
            $this->error("SMTP Password: KOSONG");
        } else {
            $maskedPassword = strlen($password) > 4 
                ? substr($password, 0, 2) . str_repeat('*', strlen($password) - 4) . substr($password, -2)
                : '****';
            $this->info("SMTP Password: Terisi ({$maskedPassword}, panjang: " . strlen($password) . ")");
        }
        
        // Brevo configuration
        $brevoKey = config('services.brevo.key') ?: env('BREVO_API_KEY');
        if (empty($brevoKey)) {
            $this->error("Brevo API Key: KOSONG");
        } else {
            $maskedKey = strlen($brevoKey) > 10 
                ? substr($brevoKey, 0, 5) . str_repeat('*', strlen($brevoKey) - 10) . substr($brevoKey, -5)
                : '**********';
            $this->info("Brevo API Key: Terisi ({$maskedKey}, panjang: " . strlen($brevoKey) . ")");
        }

        $this->line("From Address: " . ($fromAddress ?: 'NULL'));
        $this->line("From Name: " . ($fromName ?: 'NULL'));
        
        $this->info("\n=== TES KONEKSI JARINGAN (fsockopen) ===");
        
        // Check SMTP connection if configured or default mailer is SMTP
        if (!empty($host) && !empty($port)) {
            $this->line("Menghubungkan ke SMTP Server {$host}:{$port}...");
            $startTime = microtime(true);
            $socket = @fsockopen($host, $port, $errno, $errstr, 5.0);
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            if ($socket) {
                $this->info("Koneksi TCP SMTP Berhasil! Terkoneksi dalam {$duration}ms.");
                fclose($socket);
            } else {
                $this->error("Gagal terhubung ke host SMTP! Error #{$errno}: {$errstr} (Waktu tunggu: {$duration}ms)");
                $this->error("Catatan: Render memblokir port 25. Pastikan menggunakan port 587 (TLS) atau 465 (SSL).");
            }
        } else {
            $this->line("Detail SMTP tidak lengkap, melewati tes koneksi SMTP.");
        }
        
        // Always check Brevo API connection (port 443 HTTPS)
        $this->line("Menghubungkan ke Brevo API (api.brevo.com:443)...");
        $startTime = microtime(true);
        $socket = @fsockopen('api.brevo.com', 443, $errno, $errstr, 5.0);
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        if ($socket) {
            $this->info("Koneksi ke Brevo API Berhasil! Terkoneksi dalam {$duration}ms.");
            fclose($socket);
        } else {
            $this->error("Gagal terhubung ke Brevo API! Error #{$errno}: {$errstr} (Waktu tunggu: {$duration}ms)");
        }
        
        $targetEmail = $this->option('email') ?: $fromAddress;
        
        if (empty($targetEmail)) {
            $this->error("\nEmail tujuan tes pengiriman tidak ditentukan dan From Address kosong. Uji coba pengiriman email dilewati.");
            return;
        }

        $this->info("\n=== TES PENGIRIMAN EMAIL UJI COBA ===");
        $this->line("Mencoba mengirim email ke: {$targetEmail}...");
        
        try {
            Mail::raw("Halo! Ini adalah email diagnostik dari aplikasi Push Leveling di lingkungan " . app()->environment() . " pada " . now()->toDateTimeString() . ".", function ($message) use ($targetEmail) {
                $message->to($targetEmail)
                        ->subject("Diagnostik Email Push Leveling: " . app()->environment());
            });
            $this->info("Email uji coba BERHASIL dikirim tanpa error!");
        } catch (\Throwable $e) {
            $this->error("Email uji coba GAGAL dikirim!");
            $this->error("Exception Class: " . get_class($e));
            $this->error("Pesan Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " (Baris " . $e->getLine() . ")");
            $this->error("Stack Trace:\n" . substr($e->getTraceAsString(), 0, 1000) . "\n... truncated ...");
        }
    })->purpose('Diagnose application mail configuration and connectivity');

    // --- Scheduling Definitions ---

    Schedule::command('missions:generate')->dailyAt('00:00');
    Schedule::command('notifications:morning')->dailyAt('06:00');
    Schedule::command('notifications:evening')->dailyAt('20:00');
