<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::serving(function () {
            // Ambil semua file CSS di public/css
            $cssFiles = File::files(public_path('css'));

            foreach ($cssFiles as $file) {
                // Pastikan hanya file .css yang dimuat
                if ($file->getExtension() === 'css') {
                    Filament::registerStyles([
                        asset('css/' . $file->getFilename())
                    ]);
                }
            }
        });
    }
}
