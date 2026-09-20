<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHouseholdExists
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->household) {
            auth()->logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->withErrors([
                'email' => 'Akun Anda tidak memiliki Household. Silakan hubungi administrator.'
            ]);
        }

        return $next($request);
    }
}
