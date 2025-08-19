<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Providers\FilamentServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Daftarkan provider hanya saat aplikasi berjalan di web atau console
        if (! $this->app->runningInConsole()) {
            $this->app->register(FilamentServiceProvider::class);
        }
    }

    public function boot(): void
    {
        // Force HTTPS di production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Fix proxy headers untuk Railway
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }
    }
}
