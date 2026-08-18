<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\PasswordHelper;
use App\Mail\PasswordTemporalMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecuperacionController extends Controller
{
    private const MAX_RECOVERY_ATTEMPTS = 3;
    private const RECOVERY_LOCKOUT_MINUTES = 15;

    public function validar(Request $request)
    {
        // Verificar si está bloqueado
        $lockoutUntil = session('recovery_lockout_until');
        $currentTime = now();
        
        if ($lockoutUntil && $currentTime->lessThan($lockoutUntil)) {
            $secondsRemaining = $currentTime->diffInSeconds($lockoutUntil);
            $minutesRemaining = floor($secondsRemaining / 60);
            $secondsRemaining = $secondsRemaining % 60;
            
            return response()->json([
                'success' => false,
                'message' => "Ha excedido el número de intentos. Intente nuevamente en {$minutesRemaining} minutos y {$secondsRemaining} segundos.",
                'tipo' => 'error',
                'bloqueado' => true,
                'minutes' => $minutesRemaining,
                'seconds' => $secondsRemaining
            ]);
        }
        
        // Si expiró el bloqueo, limpiar
        if ($lockoutUntil) {
            session()->forget('recovery_attempts');
            session()->forget('recovery_lockout_until');
        }

        // Obtener intentos actuales
        $attempts = session('recovery_attempts', 0);
        
        // Si ya alcanzó el límite, bloquear inmediatamente
        if ($attempts >= self::MAX_RECOVERY_ATTEMPTS) {
            $lockoutUntil = $currentTime->addMinutes(self::RECOVERY_LOCKOUT_MINUTES);
            session(['recovery_lockout_until' => $lockoutUntil]);
            session()->forget('recovery_attempts');
            
            return response()->json([
                'success' => false,
                'message' => "Ha excedido el número de intentos. Intente nuevamente en " . self::RECOVERY_LOCKOUT_MINUTES . " minutos.",
                'tipo' => 'error',
                'bloqueado' => true,
                'minutes' => self::RECOVERY_LOCKOUT_MINUTES,
                'seconds' => 0
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'num_empleado' => 'required|integer',
        ]);

        // Buscar usuario que coincida con ambos campos
        $user = User::where('email_personal', $request->email)
                    ->where('num_empleado', $request->num_empleado)
                    ->first();

        if (!$user) {
            // Incrementar intentos
            $newAttempts = $attempts + 1;
            session(['recovery_attempts' => $newAttempts]);
            
            // Si después de incrementar alcanzó el límite, mostrar bloqueo inmediato
            if ($newAttempts >= self::MAX_RECOVERY_ATTEMPTS) {
                $lockoutUntil = $currentTime->addMinutes(self::RECOVERY_LOCKOUT_MINUTES);
                session(['recovery_lockout_until' => $lockoutUntil]);
                session()->forget('recovery_attempts');
                
                return response()->json([
                    'success' => false,
                    'message' => "Ha excedido el número de intentos. Intente nuevamente en " . self::RECOVERY_LOCKOUT_MINUTES . " minutos.",
                    'tipo' => 'error',
                    'bloqueado' => true,
                    'minutes' => self::RECOVERY_LOCKOUT_MINUTES,
                    'seconds' => 0
                ]);
            }
            
            $remaining = self::MAX_RECOVERY_ATTEMPTS - $newAttempts;
            
            return response()->json([
                'success' => false,
                'message' => "Usuario inválido. {$remaining} intentos restantes.",
                'tipo' => 'error'
            ]);
        }

        // Usuario válido - reiniciar intentos y generar nueva contraseña
        session()->forget('recovery_attempts');
        session()->forget('recovery_lockout_until');
        
        $passwordTemp = PasswordHelper::generateTemporaryPassword();
        
        $user->password = bcrypt($passwordTemp);
        $user->pw_temporal = true;
        $user->save();

        try {
            Mail::to($user->email_personal)->send(new PasswordTemporalMail($user, $passwordTemp));
            $mensaje = 'Usuario válido. Revise su email para obtener su nueva contraseña temporal.';
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de recuperación: ' . $e->getMessage());
            $mensaje = 'Usuario válido. Ocurrió un error al enviar el email. Contacte al administrador. Contraseña temporal: ' . $passwordTemp;
        }

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'tipo' => 'success'
        ]);
    }
}