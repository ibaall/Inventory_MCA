<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Mail::extend('google_apps_script', function (array $config) {
            return new \App\Mail\Transport\GoogleAppsScriptTransport(
                $config['webapp_url'] ?? '',
                $config['token'] ?? ''
            );
        });
    }
}
