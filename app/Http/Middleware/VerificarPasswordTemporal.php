<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarPasswordTemporal
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->pw_temporal) {
            // Permitir solo acceso a mi-perfil y logout
            if (!$request->routeIs('mi-perfil') && 
                !$request->routeIs('profile.update') && 
                !$request->routeIs('logout')) {
                return redirect()->route('mi-perfil')
                    ->with('force_password_change', true);
            }
        }

        return $next($request);
    }
}