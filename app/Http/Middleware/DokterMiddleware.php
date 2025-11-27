<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DokterMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has dokter, perawat, or admin role
        // Perawat has the same access as dokter
        if (Auth::check() && in_array(Auth::user()->role, ['dokter', 'perawat', 'admin'])) {
            return $next($request);
        }

        // If not dokter, perawat, or admin, redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses sebagai dokter/perawat!');
    }
}
