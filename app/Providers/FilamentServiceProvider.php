<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Setting global Filament (berlaku untuk semua panel)
        Filament::serving(function () {
            // Contoh: atur warna global Filament
            Filament::registerTheme(mix('css/filament.css'));

            // Contoh: pasang middleware global ke semua panel
            // (Biasanya jarang, karena panel punya middleware masing-masing)
        });
    }
}
