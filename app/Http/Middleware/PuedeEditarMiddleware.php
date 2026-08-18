<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PrivilegiosHelper;

class PuedeEditarMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!PrivilegiosHelper::puedeEditar(Auth::user())) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}