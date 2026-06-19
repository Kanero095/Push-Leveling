<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Run the scheduler or a specific artisan command securely.
     */
    public function run(Request $request)
    {
        $cronSecret = env('CRON_SECRET');

        // Ensure CRON_SECRET is configured in .env
        if (empty($cronSecret)) {
            Log::warning('Cron Webhook attempt failed: CRON_SECRET is not configured in .env');

            return response()->json([
                'status' => 'error',
                'message' => 'CRON_SECRET is not configured on the server.',
            ], 500);
        }

        // Validate secret token
        $token = $request->query('token');
        if ($token !== $cronSecret) {
            Log::warning('Cron Webhook unauthorized access attempt with token: '.($token ?? 'NULL'));

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        // Determine command to run
        $command = $request->query('command', 'schedule:run');

        // Whitelist allowed commands for security
        $allowedCommands = [
            'schedule:run',
            'notifications:morning',
            'notifications:evening',
            'missions:generate',
        ];

        if (! in_array($command, $allowedCommands)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Command not allowed.',
            ], 400);
        }

        try {
            Log::info("Dispatching artisan command to background via webhook: {$command}");

            $phpBinary = '"' . PHP_BINARY . '"';
            $artisanPath = '"' . base_path('artisan') . '"';
            $fullCommand = "{$phpBinary} {$artisanPath} {$command}";

            // Run asynchronously based on OS
            if (substr(php_uname(), 0, 7) == "Windows") {
                pclose(popen("start /B \"\" {$fullCommand} >> " . storage_path('logs/laravel.log') . " 2>&1", "r"));
            } else {
                exec("{$fullCommand} >> " . storage_path('logs/laravel.log') . " 2>&1 &");
            }

            return response()->json([
                'status' => 'success',
                'command' => $command,
                'message' => 'Command successfully dispatched to background.',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to run artisan command {$command} via webhook: ".$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Execution failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
