<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip kalau halaman login atau proses login/logout
        if ($request->is('admin/login') || $request->is('admin/logout')) {
            return $next($request);
        }

        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Pastikan user adalah admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
