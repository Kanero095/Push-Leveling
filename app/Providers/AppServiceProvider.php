<?php

namespace App\Providers;

use App\Models\AppSetting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Dynamically load application settings
        try {
            if (Schema::hasTable('app_settings')) {
                $appName = AppSetting::get('app_name');
                if ($appName) {
                    config(['app.name' => $appName]);
                }
            }
        } catch (\Exception $e) {
            // Ignore database errors during migrations or commands
        }

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // Register custom mailer for Brevo API (port 443 HTTPS)
        $this->app['mail.manager']->extend('brevo', function ($config) {
            return (new \Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory)->create(
                new \Symfony\Component\Mailer\Transport\Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
