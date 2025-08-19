<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});


// Debug route untuk cek user dan role
Route::get('/debug-routes', function () {
    return collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'methods' => $route->methods(),
        ];
    });
});


Route::get('/ping', function () {
    return response()->json(['status' => 'ok', 'time' => now()]);
});

// Debug untuk bikin admin user
Route::get('/make-admin/{email}', function ($email) {
    $user = User::where('email', $email)->first();
    if (! $user) {
        return "User dengan email {$email} tidak ditemukan.";
    }
    $user->update(['is_admin' => true]);
    return "User {$email} sudah dijadikan admin.";
});

