<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->tipo === 'Administrador') {
            return $next($request);
        }
        
        return redirect()->route('welcome')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}