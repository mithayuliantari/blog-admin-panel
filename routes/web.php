<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
            'middleware' => $route->middleware(),
        ];
    });
});


Route::middleware('auth')->get('/make-admin/{email}', function ($email) {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (! $user->isAdmin()) {
        abort(403);
    }

    $target = User::where('email', $email)->first();

    if (! $target) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $target->update(['role' => 'admin']);

    return response()->json([
        'message' => 'User updated to admin',
        'user' => [
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);
});

Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello from Railway!',
        'env' => app()->environment(),
    ]);
});

