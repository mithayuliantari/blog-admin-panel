<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ⛔️ Block non-admin users from accessing Filament

        Filament::serving(function () {
            /** @var User|null $user */
            $user = Auth::user();

            if (! $user?->isAdmin()) {
                abort(403);
            }
        });

        // Force HTTPS untuk Filament assets
        if (app()->environment('production')) {
            FilamentAsset::register([
                // Register any custom assets here if needed
            ]);
        }
    }
}
