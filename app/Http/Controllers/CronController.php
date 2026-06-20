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
            'mail:diagnose',
        ];

        if (! in_array($command, $allowedCommands)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Command not allowed.',
            ], 400);
        }

        try {
            Log::info("Running artisan command via webhook: {$command}");

            // Set up command parameters (e.g. for mail:diagnose)
            $parameters = [];
            if ($command === 'mail:diagnose' && $request->has('email')) {
                $parameters['--email'] = $request->query('email');
            }

            // Run command synchronously in-process
            $exitCode = Artisan::call($command, $parameters);
            $output = Artisan::output();

            Log::info("Artisan command {$command} finished with exit code {$exitCode}. Output: " . trim($output));

            return response()->json([
                'status' => 'success',
                'command' => $command,
                'exit_code' => $exitCode,
                'output' => $output,
                'message' => 'Command executed successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to run artisan command {$command} via webhook: ".$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Execution failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
