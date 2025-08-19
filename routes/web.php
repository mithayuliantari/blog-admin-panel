<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/healthz', fn () => response()->json(['status' => 'ok']));

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Route debug sementara untuk cek user dan session
Route::get('/debug-user', function () {
    $user = Auth::user();

    if ($user) {
        return response()->json([
            'logged_in' => true,
            'user_id'   => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'role'      => $user->role,
        ]);
    }

    return response()->json([
        'logged_in' => false,
        'message'   => 'User belum login',
    ]);
});

// Route ping sederhana untuk debug session
Route::get('/ping', function () {
    return [
        'user'    => Auth::user(),
        'session' => session()->all(),
    ];
});

// Route admin manual di luar Filament, pakai middleware auth + EnsureUserIsAdmin
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/test', function () {
            return "Halo Admin!";
        });

        // Bisa ditambahkan route admin lain di sini
});
