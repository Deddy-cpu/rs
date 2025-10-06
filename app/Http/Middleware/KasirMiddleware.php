<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has kasir, pendaftaran, dokter, or admin role
        if (Auth::check() && in_array(Auth::user()->role, ['kasir', 'pendaftaran', 'dokter', 'admin'])) {
            return $next($request);
        }

        // If not authorized, redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke sistem kasir!');
    }
}
