<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kalau belum login, biarkan lewat (biar bisa ke /admin/login)
        if (!$request->user()) {
            return $next($request);
        }

        // Cek role atau kondisi admin
        if ($request->user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        return $next($request);
    }
}
