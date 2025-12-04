<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware estándar que redirige al home si un invitado intenta acceder a rutas web protegidas.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ?string ...$guards)
    {
        $guards = $guards ?: [null];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
