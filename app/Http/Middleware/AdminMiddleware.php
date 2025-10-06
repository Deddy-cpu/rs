<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;



class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // cek jika user sudah login dan memiliki role admin
      if (Auth::check() && Auth::user()->role === 'admin') {

            return $next($request);
        }

        // jika bukan admin, arahkan ke halaman lain, misalnya home
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses!');
    }
}
