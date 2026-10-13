<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use App\Models\Materia;
use App\Models\Practica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistorialController extends Controller
{
    /**
     * Vista principal del historial.
     */
    public function index()
    {
        // Cualquier usuario autenticado puede ver la vista
        return view('administrador.historial-solicitudes.historial');
    }

    /**
     * Lista las prácticas con filtros.
     * Admin: ve todas.
     * Docente: solo las que pertenecen a sus horarios (id_maestro = Auth::id()).
     */
    public function listar(Request $request)
    {
        try {
            $user = Auth::user();
            $esAdmin = $user->isAdmin();

            $query = Practica::with([
                'horario.grupo',
                'horario.maestro',
                'horario.materia',
                'horario.laboratorio',
            ]);

            // 🔒 Si NO es admin, solo ve sus propias prácticas
            if (!$esAdmin) {
                $query->whereHas('horario', function ($q) use ($user) {
                    $q->where('id_maestro', $user->id);
                });
            }

            // Filtro: búsqueda general
            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhereHas('horario.maestro', function ($q2) use ($busqueda) {
                          $q2->where('nombre', 'LIKE', "%{$busqueda}%")
                             ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
                             ->orWhere('apellido2', 'LIKE', "%{$busqueda}%");
                      })
                      ->orWhereHas('horario.grupo', function ($q2) use ($busqueda) {
                          $q2->where('semestre', 'LIKE', "%{$busqueda}%")
                             ->orWhere('grupo', 'LIKE', "%{$busqueda}%")
                             ->orWhere('carrera', 'LIKE', "%{$busqueda}%");
                      });
                });
            }

            // Filtro: laboratorio
            if ($request->filled('id_laboratorio')) {
                $query->whereHas('horario', function ($q) use ($request) {
                    $q->where('id_laboratorio', $request->id_laboratorio);
                });
            }

            // Filtro: materia
            if ($request->filled('id_materia')) {
                $query->whereHas('horario', function ($q) use ($request) {
                    $q->where('id_materia', $request->id_materia);
                });
            }

            // Filtro: estatus
            if ($request->filled('estatus')) {
                $query->where('estatus', $request->estatus);
            }

            // Orden: primero las más nuevas
            $query->orderBy('fecha_solicitud', 'desc')->orderBy('id', 'desc');

            $practicas = $query->get()->map(function ($p) {
                $horario = $p->horario;
                return [
                    'id' => $p->id,
                    'docente' => $horario && $horario->maestro ? $horario->maestro->nombre_completo : '—',
                    'grupo' => $horario && $horario->grupo
                        ? "{$horario->grupo->semestre} {$horario->grupo->grupo} - {$horario->grupo->carrera}"
                        : '—',
                    'laboratorio' => $horario && $horario->laboratorio ? $horario->laboratorio->nombre : '—',
                    'materia' => $horario && $horario->materia ? $horario->materia->nombre : '—',
                    //'nombre_practica' => $p->nombre ?? '—',
                    //'no_actividad' => $p->no_actividad ?? '—',
                    'estatus' => $p->estatus,
                    'fecha_solicitud' => $p->fecha_solicitud
                        ? $p->fecha_solicitud->format('d/m/Y H:i')
                        : '—',
                    'fecha_reserva' => $p->fecha_reserva
                        ? $p->fecha_reserva->format('d/m/Y')
                        : '—',
                ];
            });

            return response()->json([
                'success' => true,
                'practicas' => $practicas,
                'total' => $practicas->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al listar historial: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Detalles de una práctica (para el modal).
     * Admin: cualquiera.
     * Docente: solo si es de un horario suyo.
     */
    public function detalles($id)
    {
        try {
            $user = Auth::user();
            $p = Practica::with([
                'horario.grupo',
                'horario.maestro',
                'horario.materia',
                'horario.laboratorio',
            ])->find($id);

            if (!$p) {
                return response()->json(['success' => false, 'message' => 'Práctica no encontrada'], 404);
            }

            // 🔒 Si NO es admin, validar que la práctica sea suya
            if (!$user->isAdmin()) {
                if (!$p->horario || $p->horario->id_maestro !== $user->id) {
                    return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
                }
            }

            $horario = $p->horario;

            return response()->json([
                'success' => true,
                'practica' => [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'no_actividad' => $p->no_actividad,
                    'competencia' => $p->competencia,
                    'atributo' => $p->atributo,
                    'materiales' => $p->materiales ?? [],
                    'herramientas' => $p->herramientas ?? [],
                    'notas' => $p->notas,
                    'estatus' => $p->estatus,
                    'docente' => $horario && $horario->maestro ? $horario->maestro->nombre_completo : '—',
                    'grupo' => $horario && $horario->grupo
                        ? "{$horario->grupo->semestre} {$horario->grupo->grupo} - {$horario->grupo->carrera}"
                        : '—',
                    'materia' => $horario && $horario->materia ? $horario->materia->nombre : '—',
                    'laboratorio' => $horario && $horario->laboratorio ? $horario->laboratorio->nombre : '—',
                    'fecha_reserva' => $p->fecha_reserva ? $p->fecha_reserva->format('d/m/Y') : '—',
                    'fecha_solicitud' => $p->fecha_solicitud ? $p->fecha_solicitud->format('d/m/Y H:i') : '—',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener detalles: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza el estatus de una práctica.
     * Solo Admin (por ahora).
     */
    public function actualizarEstatus(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'id' => 'required|exists:practicas,id',
                'estatus' => 'required|in:Reservada,Cumplida,No cumplida',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $practica = Practica::findOrFail($request->id);
            $practica->estatus = $request->estatus;
            $practica->save();

            return response()->json([
                'success' => true,
                'message' => '✅ Estatus actualizado correctamente.',
                'estatus' => $practica->estatus,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estatus: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una práctica.
     * Admin: cualquiera.
     * Docente: solo las suyas.
     */
    public function eliminar(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $practica = Practica::with('horario')->find($id);

            if (!$practica) {
                return response()->json(['success' => false, 'message' => 'Práctica no encontrada'], 404);
            }

            // 🔒 Si NO es admin, validar que sea suya
            if (!$user->isAdmin()) {
                if (!$practica->horario || $practica->horario->id_maestro !== $user->id) {
                    return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
                }
            }

            $practica->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Reserva eliminada correctamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar práctica: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Lista laboratorios para el filtro.
     */
    public function listarLaboratorios()
    {
        try {
            $laboratorios = Laboratorio::orderBy('nombre')->get();
            return response()->json(['success' => true, 'laboratorios' => $laboratorios]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Lista materias por laboratorio para el filtro.
     */
    public function listarMateriasPorLaboratorio(Request $request)
    {
        try {
            $idLaboratorio = $request->get('id_laboratorio');
            $query = Materia::query();
            if ($idLaboratorio) {
                $query->where('id_laboratorio', $idLaboratorio);
            }
            $materias = $query->orderBy('nombre')->get(['id', 'nombre']);
            return response()->json(['success' => true, 'materias' => $materias]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}