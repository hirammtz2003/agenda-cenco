<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fecha;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Practica;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class HorarioController extends Controller
{
    // ============ VISTA PRINCIPAL ============
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        return view('administrador.carga-horarios.horarios');
    }

    // ============ AUTOCOMPLETES ============

    public function buscarGrupos(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $grupos = Grupo::where(function ($q) use ($query) {
                $q->where('semestre', 'LIKE', "%{$query}%")
                  ->orWhere('grupo', 'LIKE', "%{$query}%")
                  ->orWhere('carrera', 'LIKE', "%{$query}%")
                  ->orWhere('ciclo_escolar', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'nombre' => "{$g->semestre} {$g->grupo} - {$g->carrera}" . ($g->ciclo_escolar ? " ({$g->ciclo_escolar})" : '')
                ];
            });

        return response()->json($grupos);
    }

    public function buscarMaestros(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $maestros = User::where(function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('apellido1', 'LIKE', "%{$query}%")
                  ->orWhere('apellido2', 'LIKE', "%{$query}%")
                  ->orWhere('num_empleado', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'nombre' => $u->nombre_completo . " ({$u->num_empleado})"
                ];
            });

        return response()->json($maestros);
    }

    public function buscarMaterias(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $materias = Materia::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('descripcion', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'nombre' => $m->nombre
                ];
            });

        return response()->json($materias);
    }

    public function listarLaboratorios()
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $laboratorios = \App\Models\Laboratorio::orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'laboratorios' => $laboratorios
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listarMateriasPorLaboratorio(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $idLaboratorio = $request->get('id_laboratorio');

            $query = \App\Models\Materia::query();

            if ($idLaboratorio) {
                // Si se especifica laboratorio, mostrar solo materias de ese laboratorio
                $query->where('id_laboratorio', $idLaboratorio);
            }
            // Si no se especifica, se muestran todas las materias

            $materias = $query->orderBy('nombre')->get(['id', 'nombre']);

            return response()->json([
                'success' => true,
                'materias' => $materias
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ HORARIOS ============

    public function listarHorarios(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $query = Horario::with(['grupo', 'maestro', 'materia']);

            if ($request->filled('hora')) {
                $query->where('hora', $request->hora);
            }
            if ($request->filled('dia')) {
                $query->where('dia', $request->dia);
            }
            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function ($q) use ($busqueda) {
                    $q->whereHas('grupo', function ($q2) use ($busqueda) {
                        $q2->where('semestre', 'LIKE', "%{$busqueda}%")
                           ->orWhere('grupo', 'LIKE', "%{$busqueda}%")
                           ->orWhere('carrera', 'LIKE', "%{$busqueda}%");
                    })
                    ->orWhereHas('maestro', function ($q2) use ($busqueda) {
                        $q2->where('nombre', 'LIKE', "%{$busqueda}%")
                           ->orWhere('apellido1', 'LIKE', "%{$busqueda}%");
                    })
                    ->orWhereHas('materia', function ($q2) use ($busqueda) {
                        $q2->where('nombre', 'LIKE', "%{$busqueda}%");
                    });
                });
            }

            $horarios = $query->with(['grupo', 'maestro', 'materia', 'laboratorio'])
                            ->orderBy('dia')->orderBy('hora')->get()->map(function ($h) {
                return [
                    'id' => $h->id,
                    'hora' => $h->hora,
                    'hora_legible' => $h->hora_legible,
                    'dia' => $h->dia,
                    'dia_legible' => $h->dia_legible,
                    'hora_fija' => (bool) $h->hora_fija,
                    'id_grupo' => $h->id_grupo,
                    'grupo_nombre' => $h->grupo ? "{$h->grupo->semestre} {$h->grupo->grupo} - {$h->grupo->carrera}" : null,
                    'id_maestro' => $h->id_maestro,
                    'maestro_nombre' => $h->maestro ? $h->maestro->nombre_completo : null,
                    'id_materia' => $h->id_materia,
                    'materia_nombre' => $h->materia ? $h->materia->nombre : null,
                    'id_laboratorio' => $h->id_laboratorio,               // ← nuevo
                    'laboratorio_nombre' => $h->laboratorio ? $h->laboratorio->nombre : null,  // ← nuevo
                ];
            });

            return response()->json([
                'success' => true,
                'horarios' => $horarios,
                'total' => $horarios->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al listar horarios: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function guardarHorario(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'id' => 'nullable|exists:horarios,id',
                'hora' => ['required', 'integer', 'between:1,8'],
                'dia' => ['required', Rule::in(['L', 'M', 'X', 'J', 'V'])],
                'hora_fija' => 'nullable|boolean',
                'id_grupo' => 'required|exists:grupos,id',
                'id_maestro' => 'required|exists:users,id',
                'id_materia' => 'required|exists:materias,id',
                'id_laboratorio' => 'nullable|exists:laboratorios,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            // Verificar duplicados (hora + día + grupo)
            $query = Horario::where('hora', $request->hora)
                ->where('dia', $request->dia)
                ->where('id_grupo', $request->id_grupo);
            if ($request->filled('id')) {
                $query->where('id', '!=', $request->id);
            }
            if ($query->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Este grupo ya tiene una clase asignada en ese día y hora.'
                ]);
            }

            // Verificar que el maestro no esté ocupado
            $maestroOcupado = Horario::where('hora', $request->hora)
                ->where('dia', $request->dia)
                ->where('id_maestro', $request->id_maestro);
            if ($request->filled('id')) {
                $maestroOcupado->where('id', '!=', $request->id);
            }
            if ($maestroOcupado->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ El maestro ya tiene una clase asignada en ese día y hora.'
                ]);
            }

            /*DB::beginTransaction();

            $esHoraFija = $request->boolean('hora_fija');*/

            $data = [
                'hora' => $request->hora,
                'dia' => $request->dia,
                'hora_fija' => $request->boolean('hora_fija'),
                //'hora_fija' => $esHoraFija,
                'id_grupo' => $request->id_grupo,
                'id_maestro' => $request->id_maestro,
                'id_materia' => $request->id_materia,
                'id_laboratorio' => $request->id_laboratorio,
            ];

            if ($request->filled('id')) {
                $horario = Horario::findOrFail($request->id);
                $horario->update($data);
                $mensaje = '✅ Horario actualizado correctamente.';
            } else {
                $horario = Horario::create($data);
                $mensaje = '✅ Horario creado correctamente.';
            }

            // 🔥 LÓGICA DE PRÁCTICA AUTOMÁTICA
            /*if ($esHoraFija) {
                // Verificar si ya existe una práctica vinculada
                $practicaExistente = Practica::where('id_horario', $horario->id)->first();

                // Obtener datos del horario para prellenar la práctica
                $materia = \App\Models\Materia::find($request->id_materia);
                $nombreMateria = $materia ? $materia->nombre : null;

                $datosPractica = [
                    'nombre' => null,
                    'no_actividad' => null,
                    'competencia' => null,
                    'atributo' => null,
                    'materiales' => null,
                    'herramientas' => null,
                    'estatus' => 'Reservada',
                    'fecha_solicitud' => now(),
                    'notas' => null,
                    'id_horario' => $horario->id,
                ];

                if ($practicaExistente) {
                    // Si ya existe, solo actualizamos el nombre de la materia (por si cambió)
                    $practicaExistente->update([
                        'nombre' => $nombreMateria,
                    ]);
                    $mensaje .= ' Se actualizó la práctica vinculada.';
                } else {
                    // Crear nueva práctica
                    Practica::create($datosPractica);
                    $mensaje .= ' Se creó una práctica automáticamente por ser hora fija.';
                }
            } else {
                // Si ya no es hora fija, eliminar la práctica vinculada (si existe)
                $practicaExistente = Practica::where('id_horario', $horario->id)->first();
                if ($practicaExistente) {
                    $practicaExistente->delete();
                    $mensaje .= ' Se eliminó la práctica vinculada porque ya no es hora fija.';
                }
            }

            DB::commit();*/

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'horario' => $horario,
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            //DB::rollBack();
            Log::error('Error al guardar horario: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function obtenerHorario($id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $h = Horario::with(['grupo', 'maestro', 'materia', 'laboratorio'])->find($id);
            if (!$h) {
                return response()->json(['success' => false, 'message' => 'Horario no encontrado'], 404);
            }

            return response()->json([
                'success' => true,
                'horario' => [
                    'id' => $h->id,
                    'hora' => $h->hora,
                    'dia' => $h->dia,
                    'hora_fija' => (bool) $h->hora_fija,
                    'id_grupo' => $h->id_grupo,
                    'grupo_nombre' => $h->grupo ? "{$h->grupo->semestre} {$h->grupo->grupo} - {$h->grupo->carrera}" : null,
                    'id_maestro' => $h->id_maestro,
                    'maestro_nombre' => $h->maestro ? $h->maestro->nombre_completo : null,
                    'id_materia' => $h->id_materia,
                    'materia_nombre' => $h->materia ? $h->materia->nombre : null,
                    'id_laboratorio' => $h->id_laboratorio,
                    'laboratorio_nombre' => $h->laboratorio ? $h->laboratorio->nombre : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminarHorario(Request $request, $id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $horario = Horario::findOrFail($id);
            
            // La práctica se eliminará automáticamente por el onDelete('cascade')
            //$horario->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Horario eliminado correctamente.',
                //'message' => '✅ Horario eliminado correctamente (y su práctica vinculada si existía).',
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ DÍAS INHÁBILES ============

    public function listarDias(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $query = Fecha::query();
            
            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }
            if ($request->filled('busqueda')) {
                $query->where('fecha', 'LIKE', "%{$request->busqueda}%");
            }

            $fechas = $query->orderBy('fecha')->get()->map(function ($f) {
                return [
                    'id' => $f->id,
                    'fecha' => $f->fecha ? $f->fecha->format('Y-m-d') : null,
                    'tipo' => $f->tipo,
                ];
            });

            return response()->json([
                'success' => true,
                'dias' => $fechas,
                'total' => $fechas->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function guardarDia(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'id' => 'nullable|exists:fechas,id',
                'fecha' => 'required|date',
                'tipo' => 'required|in:Día inhábil,Inicio de semestre,Fin de semestre',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            // 🔒 Si es Inicio o Fin de semestre, solo puede existir uno
            if (in_array($request->tipo, ['Inicio de semestre', 'Fin de semestre'])) {
                $query = Fecha::where('tipo', $request->tipo);
                if ($request->filled('id')) {
                    $query->where('id', '!=', $request->id);
                }
                if ($query->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => "⚠️ Ya existe una fecha registrada como '{$request->tipo}'. Solo puede haber una."
                    ]);
                }
            } else {
                // Para Día inhábil, evitar duplicar la misma fecha exacta
                $query = Fecha::where('fecha', $request->fecha)->where('tipo', 'Día inhábil');
                if ($request->filled('id')) {
                    $query->where('id', '!=', $request->id);
                }
                if ($query->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ Esta fecha ya está registrada como día inhábil.'
                    ]);
                }
            }

            $data = [
                'fecha' => $request->fecha,
                'tipo' => $request->tipo,
            ];

            if ($request->filled('id')) {
                $fecha = Fecha::findOrFail($request->id);
                $fecha->update($data);
                $mensaje = '✅ Fecha actualizada correctamente.';
            } else {
                $fecha = Fecha::create($data);
                $mensaje = '✅ Fecha registrada correctamente.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'dia' => $fecha,
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function obtenerDia($id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $fecha = Fecha::find($id);
            if (!$fecha) {
                return response()->json(['success' => false, 'message' => 'Fecha no encontrada'], 404);
            }

            return response()->json([
                'success' => true,
                'dia' => [
                    'id' => $fecha->id,
                    'fecha' => $fecha->fecha ? $fecha->fecha->format('Y-m-d') : null,
                    'tipo' => $fecha->tipo,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminarDia(Request $request, $id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $dia = Fecha::findOrFail($id);
            $dia->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Día inhábil eliminado correctamente.',
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}