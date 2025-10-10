<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToRoleDashboard
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Redirect to role-specific dashboard
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'dokter':
                    return redirect()->route('dokter.dashboard');
                case 'pendaftaran':
                    return redirect()->route('pendaftaran.dashboard');
                case 'kasir':
                    return redirect()->route('kasir.dashboard');
                default:
                    // For 'kosong' role or any other role, stay on main dashboard
                    break;
            }
        }

        return $next($request);
    }
}
