<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'num_empleado' => 'required|integer',
            'password' => 'required|string',
        ]);

        // Obtener información de bloqueo
        $lockoutUntil = session('login_lockout_until');
        $currentTime = now();
        
        // Verificar si sigue bloqueado
        if ($lockoutUntil && $currentTime->lessThan($lockoutUntil)) {
            $secondsRemaining = $currentTime->diffInSeconds($lockoutUntil);
            $minutesRemaining = floor($secondsRemaining / 60);
            $secondsRemaining = $secondsRemaining % 60;
            
            return back()->withErrors([
                'login' => "Ha excedido el número de intentos. Intente nuevamente en {$minutesRemaining} minutos y {$secondsRemaining} segundos."
            ]);
        }
        
        // Si expiró el bloqueo, limpiar
        if ($lockoutUntil) {
            session()->forget('login_attempts');
            session()->forget('login_lockout_until');
        }

        // Obtener intentos actuales
        $attempts = session('login_attempts', 0);
        
        // Si ya alcanzó el límite de intentos, bloquear inmediatamente
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            // Bloquear por LOCKOUT_MINUTES minutos
            $lockoutUntil = $currentTime->addMinutes(self::LOCKOUT_MINUTES);
            session(['login_lockout_until' => $lockoutUntil]);
            session()->forget('login_attempts');
            
            $minutesRemaining = self::LOCKOUT_MINUTES;
            
            return back()->withErrors([
                'login' => "Ha excedido el número de intentos. Intente nuevamente en {$minutesRemaining} minutos."
            ]);
        }

        $credentials = [
            'num_empleado' => $request->num_empleado,
            'password' => $request->password,
            'estatus' => true
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            session()->forget('login_attempts');
            session()->forget('login_lockout_until');
            $request->session()->regenerate();
            
            if (Auth::user()->pw_temporal) {
                return redirect()->route('mi-perfil')->with('force_password_change', true);
            }
            
            return redirect()->intended(route('welcome'));
        }

        // Incrementar intentos
        $newAttempts = $attempts + 1;
        session(['login_attempts' => $newAttempts]);
        
        // Si después de incrementar alcanzó el límite, mostrar bloqueo inmediato
        if ($newAttempts >= self::MAX_LOGIN_ATTEMPTS) {
            $lockoutUntil = $currentTime->addMinutes(self::LOCKOUT_MINUTES);
            session(['login_lockout_until' => $lockoutUntil]);
            session()->forget('login_attempts');
            
            $minutesRemaining = self::LOCKOUT_MINUTES;
            
            throw ValidationException::withMessages([
                'login' => "Ha excedido el número de intentos. Intente nuevamente en {$minutesRemaining} minutos."
            ]);
        }
        
        $remaining = self::MAX_LOGIN_ATTEMPTS - $newAttempts;
        
        throw ValidationException::withMessages([
            'login' => "Usuario o contraseña incorrectos. {$remaining} intentos restantes."
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}