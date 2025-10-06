<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PendaftaranMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has pendaftaran or admin role
        if (Auth::check() && in_array(Auth::user()->role, ['pendaftaran', 'admin'])) {
            return $next($request);
        }

        // If not pendaftaran or admin, redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses sebagai staff pendaftaran!');
    }
}
