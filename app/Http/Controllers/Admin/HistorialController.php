<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use App\Models\Materia;
use App\Models\Practica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistorialController extends Controller
{
    // Vista principal del historial.

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
            $this->actualizarEstatusAutomatico();

            $user = Auth::user();
            $esAdmin = $user->isAdmin();

            $query = Practica::with([
                'horario.grupo',
                'horario.maestro',
                'horario.materia',
                'horario.laboratorio',
            ]);

            if (!$esAdmin) {
                $query->whereHas('horario', function ($q) use ($user) {
                    $q->where('id_maestro', $user->id);
                });
            }

            // Filtros
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

            if ($request->filled('id_laboratorio')) {
                $query->whereHas('horario', function ($q) use ($request) {
                    $q->where('id_laboratorio', $request->id_laboratorio);
                });
            }
            if ($request->filled('id_materia')) {
                $query->whereHas('horario', function ($q) use ($request) {
                    $q->where('id_materia', $request->id_materia);
                });
            }
            if ($request->filled('estatus')) {
                $query->where('estatus', $request->estatus);
            }

            $query->orderBy('fecha_solicitud', 'desc')->orderBy('id', 'desc');
            $practicas = $query->get();

            // Agrupar por "práctica lógica"
            $grupos = $practicas->groupBy(function ($p) {
                return implode('|', [
                    $p->nombre,
                    $p->no_actividad,
                    $p->competencia,
                    $p->atributo,
                    $p->notas,
                    $p->fecha_solicitud ? $p->fecha_solicitud->format('Y-m-d H:i:s') : '',
                    $p->horario ? $p->horario->id_grupo : 0,
                    $p->horario ? $p->horario->id_maestro : 0,
                    $p->horario ? $p->horario->id_materia : 0,
                    $p->horario ? $p->horario->id_laboratorio : 0,
                ]);
            });

            $resultado = $grupos->map(function ($grupo) {
                $primera = $grupo->first();
                $horario = $primera->horario;

                // Calcular estatus general
                $estatuses = $grupo->pluck('estatus')->unique();
                if ($estatuses->contains('Reservada')) {
                    $estatusGeneral = 'Reservada';
                } elseif ($estatuses->contains('No cumplida')) {
                    $estatusGeneral = 'No cumplida';
                } else {
                    $estatusGeneral = 'Cumplida';
                }

                // Horas de la práctica (ordenadas por fecha)
                $horas = $grupo->sortBy(function ($p) {
                    return $p->fecha_reserva ? $p->fecha_reserva->format('Y-m-d') . ' ' . ($p->horario ? $p->horario->hora : 0) : '';
                })->values()->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'hora' => $p->horario ? $p->horario->hora : null,
                        'hora_legible' => $p->horario ? $p->horario->hora_legible : '—',
                        'dia_legible' => $p->horario ? $p->horario->dia_legible : '—',
                        'fecha_reserva' => $p->fecha_reserva ? $p->fecha_reserva->format('d/m/Y') : '—',
                        'fecha_reserva_raw' => $p->fecha_reserva ? $p->fecha_reserva->format('Y-m-d') : null,
                        'estatus' => $p->estatus,
                    ];
                });

                return [
                    'id' => $primera->id,
                    'ids' => $grupo->pluck('id')->toArray(),
                    'docente' => $horario && $horario->maestro ? $horario->maestro->nombre_completo : '—',
                    'grupo' => $horario && $horario->grupo
                        ? \App\Helpers\CarreraHelper::abreviar("{$horario->grupo->semestre} {$horario->grupo->grupo} - {$horario->grupo->carrera}")
                        : '—',
                    'laboratorio' => $horario && $horario->laboratorio ? $horario->laboratorio->nombre : '—',
                    'materia' => $horario && $horario->materia ? $horario->materia->nombre : '—',
                    'nombre_practica' => $primera->nombre ?? '—',
                    'no_actividad' => $primera->no_actividad ?? '—',
                    'estatus' => $estatusGeneral,
                    'fecha_solicitud' => $primera->fecha_solicitud ? $primera->fecha_solicitud->format('d/m/Y H:i') : '—',
                    'horas' => $horas,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'practicas' => $resultado,
                'total' => $resultado->count(),
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
            $this->actualizarEstatusAutomatico();

            $user = Auth::user();
            $practica = Practica::with([
                'horario.grupo',
                'horario.maestro',
                'horario.materia',
                'horario.laboratorio',
            ])->find($id);

            if (!$practica) {
                return response()->json(['success' => false, 'message' => 'Práctica no encontrada'], 404);
            }

            if (!$user->isAdmin()) {
                if (!$practica->horario || $practica->horario->id_maestro !== $user->id) {
                    return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
                }
            }

            // Encontrar todas las horas de la misma práctica (mismo grupo de agrupación)
            $grupo = Practica::with(['horario'])
                ->where('nombre', $practica->nombre)
                ->where('no_actividad', $practica->no_actividad)
                ->where('competencia', $practica->competencia)
                ->where('atributo', $practica->atributo)
                ->where('fecha_solicitud', $practica->fecha_solicitud)
                ->whereHas('horario', function ($q) use ($practica) {
                    $q->where('id_grupo', $practica->horario->id_grupo)
                    ->where('id_maestro', $practica->horario->id_maestro)
                    ->where('id_materia', $practica->horario->id_materia)
                    ->where('id_laboratorio', $practica->horario->id_laboratorio);
                })
                ->get();

            $horas = $grupo->sortBy(function ($p) {
                return $p->fecha_reserva ? $p->fecha_reserva->format('Y-m-d') . ' ' . ($p->horario ? $p->horario->hora : 0) : '';
            })->values()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'hora' => $p->horario ? $p->horario->hora : null,
                    'hora_legible' => $p->horario ? $p->horario->hora_legible : '—',
                    'dia_legible' => $p->horario ? $p->horario->dia_legible : '—',
                    'fecha_reserva' => $p->fecha_reserva ? $p->fecha_reserva->format('d/m/Y') : '—',
                    'estatus' => $p->estatus,
                ];
            });

            $horario = $practica->horario;

            return response()->json([
                'success' => true,
                'practica' => [
                    'id' => $practica->id,
                    'nombre' => $practica->nombre,
                    'no_actividad' => $practica->no_actividad,
                    'competencia' => $practica->competencia,
                    'atributo' => $practica->atributo,
                    'materiales' => $practica->materiales ?? [],
                    'herramientas' => $practica->herramientas ?? [],
                    'notas' => $practica->notas,
                    'docente' => $horario && $horario->maestro ? $horario->maestro->nombre_completo : '—',
                    'grupo' => $horario && $horario->grupo
                        ? "{$horario->grupo->semestre} {$horario->grupo->grupo} - {$horario->grupo->carrera}"
                        : '—',
                    'materia' => $horario && $horario->materia ? $horario->materia->nombre : '—',
                    'laboratorio' => $horario && $horario->laboratorio ? $horario->laboratorio->nombre : '—',
                    'fecha_solicitud' => $practica->fecha_solicitud ? $practica->fecha_solicitud->format('d/m/Y H:i') : '—',
                    'horas' => $horas,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza el estatus de una práctica.
     * Solo Admin (por ahora).
     */
    public function actualizarEstatusHora(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'cambios' => 'required|array',
                'cambios.*.id' => 'required|exists:practicas,id',
                'cambios.*.estatus' => 'required|in:Reservada,Cumplida,No cumplida',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $validator->errors()->all())
                ], 422);
            }

            foreach ($request->cambios as $cambio) {
                $practica = Practica::findOrFail($cambio['id']);
                $practica->estatus = $cambio['estatus'];
                $practica->save();
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Estatus actualizados correctamente.',
            ]);
        } catch (\Exception $e) {
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

    // Lista laboratorios para el filtro.

    public function listarLaboratorios()
    {
        try {
            $laboratorios = Laboratorio::orderBy('nombre')->get();
            return response()->json(['success' => true, 'laboratorios' => $laboratorios]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Lista materias por laboratorio para el filtro.
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

    private function actualizarEstatusAutomatico()
    {
        $ahora = Carbon::now();
        $hoy = $ahora->format('Y-m-d');
        $minutosAhora = $ahora->hour * 60 + $ahora->minute;

        $horasFin = [
            1 => 8 * 60 + 50, 2 => 9 * 60 + 40, 3 => 10 * 60 + 30,
            4 => 11 * 60 + 50, 5 => 12 * 60 + 40, 6 => 13 * 60 + 30,
            7 => 14 * 60 + 20, 8 => 15 * 60 + 0,
        ];

        // Días anteriores
        Practica::where('estatus', 'Reservada')
            ->where('fecha_reserva', '<', $hoy)
            ->update(['estatus' => 'Cumplida']);

        // Hoy, horas terminadas
        $deHoy = Practica::where('estatus', 'Reservada')
            ->where('fecha_reserva', $hoy)
            ->with('horario')
            ->get();

        foreach ($deHoy as $p) {
            if (!$p->horario) continue;
            $horaFin = $horasFin[$p->horario->hora] ?? null;
            if ($horaFin === null) continue;
            if ($minutosAhora >= $horaFin) {
                $p->estatus = 'Cumplida';
                $p->save();
            }
        }
    }
}