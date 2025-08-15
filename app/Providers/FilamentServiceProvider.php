<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Force HTTPS untuk Filament assets
        if (app()->environment('production')) {
            FilamentAsset::register([
                // Register any custom assets here if needed
            ]);
        }
    }
}
