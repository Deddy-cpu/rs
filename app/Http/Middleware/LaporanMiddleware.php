<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LaporanMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has admin or kasir role
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'kasir'])) {
            return $next($request);
        }

        // If not authorized, redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman laporan!');
    }
}


