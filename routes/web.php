<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});


// Debug route untuk cek user dan role
Route::get('/ping', fn() => [
    'user' => Auth::user(),
    'session' => session()->all(),
]);

// Kalau kamu mau bikin route admin manual di luar filament
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/test', function () {
            return "Halo Admin!";
        });
});

