<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\PasswordHelper;
use App\Mail\PasswordChangeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
class MiPerfilController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Verificar si viene de login con contraseña temporal
        $forceChange = session('force_password_change', false) || $user->pw_temporal;
        
        return view('mi-perfil', compact('user', 'forceChange'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // LOG PARA DEPURACIÓN
        Log::info('=== INICIO DE ACTUALIZACIÓN DE PERFIL ===');
        Log::info('Usuario ID: ' . $user->id);
        Log::info('pw_temporal actual: ' . ($user->pw_temporal ? 'true' : 'false'));
        Log::info('Datos recibidos:', $request->all());

        $rules = [
            'email' => ['nullable', 'email', Rule::unique('users', 'email_personal')->ignore($user->id)],
        ];

        // SIEMPRE requerir contraseña actual si el usuario tiene pw_temporal activa
        if ($user->pw_temporal) {
            Log::info('CASO: Usuario con contraseña temporal - REQUIRIENDO CAMBIO OBLIGATORIO');
            $rules['current_password'] = ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    Log::warning('Contraseña temporal actual incorrecta');
                    $fail('La contraseña temporal actual es incorrecta.');
                } else {
                    Log::info('Contraseña temporal actual verificada correctamente');
                }
            }];
            $rules['new_password'] = ['required', 'string', 'min:5', 'confirmed'];
        } 
        // Si no tiene pw_temporal, solo requerir contraseña si quiere cambiarla
        elseif ($request->filled('new_password')) {
            Log::info('CASO: Usuario sin contraseña temporal - CAMBIO VOLUNTARIO');
            $rules['current_password'] = ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    Log::warning('Contraseña actual incorrecta');
                    $fail('La contraseña actual es incorrecta.');
                } else {
                    Log::info('Contraseña actual verificada correctamente');
                }
            }];
            $rules['new_password'] = ['required', 'string', 'min:5', 'confirmed'];
        }

        $request->validate($rules);

        $data = [];
        $cambios = [];

        if ($request->filled('email')) {
            $data['email_personal'] = $request->email;
            $cambios[] = 'email personal';
            Log::info('Se actualizará email personal a: ' . $request->email);
        }

        // Si se proporcionó nueva contraseña
        if ($request->filled('new_password')) {
            Log::info('PROCESANDO CAMBIO DE CONTRASEÑA');
            
            // Agregar datos de contraseña al array
            $data['password'] = Hash::make($request->new_password);
            $data['pw_temporal'] = false;
            
            Log::info('Datos de contraseña preparados');
        }

        if (!empty($data)) {
            Log::info('Datos a actualizar:', $data);
            
            // ACTUALIZACIÓN DIRECTA CON QUERY BUILDER (sin timestamps)
            $updated = \DB::table('users')
                ->where('id', $user->id)
                ->update($data);
            
            Log::info('Actualización por Query Builder - filas afectadas: ' . $updated);
            
            // REFRESCAR el modelo (recargar desde BD)
            $user->refresh();
            
            Log::info('Usuario actualizado. Nuevo pw_temporal (después de refresh): ' . ($user->pw_temporal ? 'true' : 'false'));
            
            $cambios[] = 'contraseña';
            
            // Enviar notificación de cambio de contraseña
            try {
                Mail::to($user->email_personal)->send(new PasswordChangeNotification($user));
                Log::info('Email de notificación enviado a: ' . $user->email_personal);
            } catch (\Exception $e) {
                Log::error('Error al enviar email de cambio: ' . $e->getMessage());
            }
        }

        if (!empty($cambios)) {
            $mensaje = 'Perfil actualizado correctamente.';
            if (in_array('contraseña', $cambios)) {
                // Limpiar la sesión de force_change
                session()->forget('force_password_change');
                Log::info('Redirigiendo a welcome');
                return redirect()->route('welcome')
                    ->with('success', $mensaje);
            }
            
            Log::info('Redirigiendo a mi-perfil');
            return redirect()->route('mi-perfil')
                ->with('success', $mensaje);
        }

        Log::info('No se realizaron cambios');
        return redirect()->route('mi-perfil')
            ->with('info', 'No se realizaron cambios.');
    }
}