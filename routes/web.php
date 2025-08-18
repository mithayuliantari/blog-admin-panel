<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Debug route untuk cek user dan role
Route::get('/debug-auth', function () {
    $user = Auth::user();

    return response()->json([
        'authenticated' => Auth::check(),
        'user' => $user ? [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role ?? 'NO ROLE SET',
            'can_access_admin' => $user->role === 'admin', 
        ] : null,
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
    ]);
});

Route::get('/make-admin/{email}', function ($email) {
    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $user->update(['role' => 'admin']);

    return response()->json([
        'message' => 'User updated to admin',
        'user' => [
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);
});

