<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kalau belum login, lanjut biar bisa ke halaman login
        if (!$request->user()) {
            Log::info('[EnsureUserIsAdmin] Guest mencoba akses: ' . $request->path());
            return $next($request);
        }

        // Kalau URL mengarah ke /admin tapi role bukan admin → forbidden
        if ($request->is('admin*') && $request->user()->role !== 'admin') {
            Log::warning('[EnsureUserIsAdmin] User tanpa akses mencoba masuk.', [
                'user_id' => $request->user()->id,
                'role'    => $request->user()->role,
                'path'    => $request->path(),
            ]);
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Kalau lolos → catat siapa yang berhasil masuk
        Log::info('[EnsureUserIsAdmin] Akses diizinkan.', [
            'user_id' => $request->user()->id,
            'role'    => $request->user()->role,
            'path'    => $request->path(),
        ]);
        return $next($request);
    }
}
