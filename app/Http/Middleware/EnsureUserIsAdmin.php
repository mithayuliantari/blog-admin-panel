<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Debug log setiap akses
        Log::info('[EnsureUserIsAdmin] Middleware dijalankan', [
            'user_id' => optional($user)->id,
            'role'    => optional($user)->role,
            'path'    => $request->path(),
        ]);

        // Jika user tidak login atau bukan admin, abort 403
        if (! $user || $user->role !== 'admin') {
            Log::warning('[EnsureUserIsAdmin] User tanpa akses mencoba masuk', [
                'user_id' => optional($user)->id,
                'role'    => optional($user)->role,
                'path'    => $request->path(),
            ]);

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
