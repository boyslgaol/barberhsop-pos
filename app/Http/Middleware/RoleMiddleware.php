<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Jika tidak ada role yang dispesifikasikan, izinkan akses
        if (empty($roles)) {
            return $next($request);
        }
        
        // Cek apakah role user termasuk dalam roles yang diizinkan
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }
        
        // Jika tidak memiliki akses
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}