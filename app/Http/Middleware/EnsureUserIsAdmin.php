<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Izinkan akses ke halaman login
        if (!$request->user()) {
            return $next($request);
        }

        if ($request->is('admin*') && $request->user()?->role !== 'admin') {
            abort(403);
        }
        return $next($request);
    }
}
