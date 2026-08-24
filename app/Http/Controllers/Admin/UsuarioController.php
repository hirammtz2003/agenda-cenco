<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\PasswordHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordTemporalMail;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    

    public function index()
    {
        return view('administrador.administracion-usuarios.index');
    }

        public function create()
    {
        // Solo administradores pueden registrar usuarios
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores.');
        }
        
        return view('administrador.administracion-usuarios.registro');
    }

    public function store(Request $request)
    {
        // Verificar que el usuario actual es administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        $request->validate([
            'nombre' => 'required|string|max:30',
            'apellido1' => 'required|string|max:20',
            'apellido2' => 'nullable|string|max:20',
            'email_personal' => 'required|email|max:50|unique:users',
            'email_institucional' => 'required|email|max:50|unique:users',
            'telefono' => 'required|string|size:10',
            'num_empleado' => 'required|integer|unique:users',
            'tipo' => ['required', Rule::in(['Administrador', 'Docente'])],
            'current_password' => 'required|current_password',
        ]);

        $passwordTemp = PasswordHelper::generateTemporaryPassword();

        $user = User::create([
            'nombre' => $request->nombre,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'email_personal' => $request->email_personal,
            'email_institucional' => $request->email_institucional,
            'telefono' => $request->telefono,
            'num_empleado' => $request->num_empleado,
            'password' => Hash::make($passwordTemp),
            'pw_temporal' => true,
            'tipo' => $request->tipo,
            'estatus' => true,
        ]);

        try {
            Mail::to($request->email_personal)->send(new PasswordTemporalMail($user, $passwordTemp));
            $mensaje = "Usuario creado exitosamente. Se ha enviado la contraseña temporal al correo.";
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de creación: ' . $e->getMessage());
            $mensaje = "Usuario creado, pero NO se pudo enviar el correo. Contraseña temporal: {$passwordTemp} (entregar manualmente)";
        }

        return redirect()->route('admin.usuarios.registro')->with('success', $mensaje);
    }

    public function consulta(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        $query = User::query();

        // Filtro por búsqueda
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido2', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email_personal', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email_institucional', 'LIKE', "%{$busqueda}%")
                  ->orWhere('num_empleado', 'LIKE', "%{$busqueda}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por estatus (activos por defecto)
        $mostrarInactivos = $request->boolean('mostrar_inactivos');
        if (!$mostrarInactivos) {
            $query->where('estatus', true);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'id_asc');
        switch ($orden) {
            case 'nombre_asc':
                $query->orderBy('nombre')->orderBy('apellido1');
                break;
            case 'apellido_asc':
                $query->orderBy('apellido1')->orderBy('nombre');
                break;
            case 'num_empleado_asc':
                $query->orderBy('num_empleado');
                break;
            default:
                $query->orderBy('id', 'asc');
                break;
        }

        $usuarios = $query->get();
        $totalEncontrados = $usuarios->count();

        return view('administrador.administracion-usuarios.consulta-edicion', 
            compact('usuarios', 'totalEncontrados'));
    }

    public function updateBulk(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
            'usuarios' => 'required|array',
            'usuarios.*.id' => 'required|exists:users,id',
            'usuarios.*.nombre' => 'required|string|max:30',
            'usuarios.*.apellido1' => 'required|string|max:20',
            'usuarios.*.apellido2' => 'nullable|string|max:20',
            'usuarios.*.email_personal' => 'required|email|max:50',
            'usuarios.*.email_institucional' => 'required|email|max:50',
            'usuarios.*.telefono' => 'required|string|size:10',
            'usuarios.*.num_empleado' => 'required|integer',
        ]);

        $updated = [];
        $errors = [];

        foreach ($request->usuarios as $data) {
            try {
                $user = User::findOrFail($data['id']);
                
                // Verificar unicidad de emails y num_empleado
                $emailPersonalExists = User::where('email_personal', $data['email_personal'])
                    ->where('id', '!=', $user->id)->exists();
                $emailInstExists = User::where('email_institucional', $data['email_institucional'])
                    ->where('id', '!=', $user->id)->exists();
                $numEmpleadoExists = User::where('num_empleado', $data['num_empleado'])
                    ->where('id', '!=', $user->id)->exists();

                if ($emailPersonalExists || $emailInstExists || $numEmpleadoExists) {
                    $errors[] = "El usuario {$data['nombre']} tiene datos duplicados.";
                    continue;
                }

                $user->update([
                    'nombre' => $data['nombre'],
                    'apellido1' => $data['apellido1'],
                    'apellido2' => $data['apellido2'],
                    'email_personal' => $data['email_personal'],
                    'email_institucional' => $data['email_institucional'],
                    'telefono' => $data['telefono'],
                    'num_empleado' => $data['num_empleado'],
                ]);

                $updated[] = $user->id;
            } catch (\Exception $e) {
                $errors[] = "Error al actualizar usuario ID {$data['id']}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'errors' => $errors,
            'message' => count($updated) . ' usuarios actualizados correctamente.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $user = User::findOrFail($id);
        
        // No permitir eliminarse a sí mismo
        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'No puedes eliminar tu propio usuario.'], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente.'
        ]);
    }

    public function seguridad()
    {
        return view('administrador.administracion-usuarios.seguridad-privilegios');
    }

    // Búsqueda de usuarios para la vista de seguridad
    public function buscarParaSeguridad(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }

        $request->validate([
            'busqueda' => 'required|string|min:2'
        ]);

        $busqueda = $request->busqueda;
        
        $usuarios = User::where(function($query) use ($busqueda) {
            $query->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido2', 'LIKE', "%{$busqueda}%")
                  ->orWhere('num_empleado', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email_personal', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email_institucional', 'LIKE', "%{$busqueda}%");
        })->limit(10)->get();

        return response()->json([
            'success' => true,
            'usuarios' => $usuarios->map(function($user) {
                return [
                    'id' => $user->id,
                    'nombre_completo' => $user->getNombreCompletoAttribute(),
                    'num_empleado' => $user->num_empleado,
                    'email_personal' => $user->email_personal,
                    'email_institucional' => $user->email_institucional,
                    'tipo' => $user->tipo,
                    'estatus' => $user->estatus,
                    'pw_temporal' => $user->pw_temporal
                ];
            })
        ]);
    }

    // Obtener datos completos de un usuario específico
    public function obtenerUsuarioSeguridad($id)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }

        $user = User::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'usuario' => [
                'id' => $user->id,
                'nombre_completo' => $user->getNombreCompletoAttribute(),
                'num_empleado' => $user->num_empleado,
                'email_personal' => $user->email_personal,
                'email_institucional' => $user->email_institucional,
                'tipo' => $user->tipo,
                'estatus' => $user->estatus,
                'pw_temporal' => $user->pw_temporal
            ]
        ]);
    }

    // Guardar cambios de seguridad
    public function guardarSeguridad(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
            'user_id' => 'required|exists:users,id',
            'tipo' => 'sometimes|in:Administrador,Directivo,Docente,Trabajo Social',
            'estatus' => 'sometimes|boolean',
            'generar_password_temporal' => 'sometimes|boolean',
            'email_destino' => 'required_if:generar_password_temporal,true|email|nullable'
        ]);

        $user = User::findOrFail($request->user_id);
        $cambios = [];
        $mensajes = [];

        // Actualizar tipo si viene en la solicitud
        if ($request->has('tipo') && $request->tipo !== $user->tipo) {
            $user->tipo = $request->tipo;
            $cambios[] = 'tipo de usuario';
        }

        // Actualizar estatus
        if ($request->has('estatus') && $request->estatus != $user->estatus) {
            $user->estatus = $request->estatus;
            $cambios[] = 'estatus';
        }

        // Generar nueva contraseña temporal
        $passwordEnviada = null;
        if ($request->boolean('generar_password_temporal')) {
            $passwordTemp = PasswordHelper::generateTemporaryPassword();
            $user->password = Hash::make($passwordTemp);
            $user->pw_temporal = true;
            $cambios[] = 'contraseña temporal';
            $passwordEnviada = $passwordTemp;
            
            // Enviar email
            try {
                Mail::to($request->email_destino)->send(new PasswordTemporalMail($user, $passwordTemp));
                $mensajes[] = "Email enviado a {$request->email_destino}";
            } catch (\Exception $e) {
                \Log::error('Error al enviar email: ' . $e->getMessage());
                $mensajes[] = "No se pudo enviar el email, pero la contraseña se generó: {$passwordTemp}";
            }
        }

        if (empty($cambios)) {
            return response()->json([
                'success' => false,
                'message' => 'No se realizaron cambios'
            ]);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cambios guardados correctamente: ' . implode(', ', $cambios),
            'detalles' => $mensajes,
            'usuario' => [
                'tipo' => $user->tipo,
                'estatus' => $user->estatus,
                'pw_temporal' => $user->pw_temporal
            ]
        ]);
    }
}