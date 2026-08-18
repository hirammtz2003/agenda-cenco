<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\User;
use App\Models\Alumno;
use App\Models\AlumnoBeca;
use App\Helpers\PrivilegiosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{
    // Verificar permisos de edición
    private function puedeEditar()
    {
        $user = Auth::user();
        return $user && PrivilegiosHelper::puedeEditar($user);
    }

    // Verificar permisos de consulta
    private function puedeConsultar()
    {
        $user = Auth::user();
        return $user && (PrivilegiosHelper::consultaGeneral($user) || PrivilegiosHelper::puedeEditar($user));
    }

    public function index()
    {
        if (!$this->puedeConsultar()) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }

        $usuarioActual = Auth::user();
        $puedeEditar = $this->puedeEditar();
        $puedeConsultar = $this->puedeConsultar();

        return view('administrador.config-grup.grupos', compact('puedeEditar', 'puedeConsultar', 'usuarioActual'));
    }

    // Listar grupos con cantidad de alumnos
    public function listar(Request $request)
    {
        if (!$this->puedeConsultar()) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $query = Grupo::with(['asesor', 'tutor']);

        // Filtros
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('semestre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('grupo', 'LIKE', "%{$busqueda}%")
                  ->orWhere('carrera', 'LIKE', "%{$busqueda}%")
                  ->orWhereHas('asesor', function($q2) use ($busqueda) {
                      $q2->where('nombre', 'LIKE', "%{$busqueda}%")
                         ->orWhere('apellido1', 'LIKE', "%{$busqueda}%");
                  })
                  ->orWhereHas('tutor', function($q2) use ($busqueda) {
                      $q2->where('nombre', 'LIKE', "%{$busqueda}%")
                         ->orWhere('apellido1', 'LIKE', "%{$busqueda}%");
                  });
            });
        }

        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        if ($request->filled('grupo')) {
            $query->where('grupo', $request->grupo);
        }

        if ($request->filled('carrera')) {
            $query->where('carrera', $request->carrera);
        }

        $grupos = $query->get();
        $total = $grupos->count();

        // Formatear respuesta con cantidad de alumnos
        $gruposFormateados = $grupos->map(function($grupo) {
            return [
                'id' => $grupo->id,
                'semestre' => $grupo->semestre,
                'grupo' => $grupo->grupo,
                'carrera' => $grupo->carrera,
                'id_asesor' => $grupo->id_asesor,
                'nombre_asesor' => $grupo->asesor ? $grupo->asesor->getNombreCompletoAttribute() : 'No asignado',
                'id_tutor' => $grupo->id_tutor,
                'nombre_tutor' => $grupo->tutor ? $grupo->tutor->getNombreCompletoAttribute() : 'No asignado',
                'cantidad_alumnos' => $grupo->alumnos()->where('estatus', true)->count(), // Solo alumnos activos
            ];
        });

        return response()->json([
            'success' => true,
            'grupos' => $gruposFormateados,
            'total' => $total
        ]);
    }

    // Buscar usuarios (Asesores y Tutores)
    public function buscarUsuarios(Request $request)
    {
        if (!$this->puedeEditar()) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $request->validate([
            'tipo' => 'required|in:asesor,tutor',
            'busqueda' => 'nullable|string'
        ]);

        $query = User::where('estatus', true);

        if ($request->tipo === 'asesor') {
            $query->where('tipo', 'Docente');
        } else {
            $query->where('tipo', 'Trabajo Social');
        }

        if ($request->filled('busqueda') && strlen($request->busqueda) >= 2) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido1', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido2', 'LIKE', "%{$busqueda}%")
                  ->orWhere('num_empleado', 'LIKE', "%{$busqueda}%");
            });
        } else {
            // Si no hay búsqueda, limitar a 10
            $query->limit(10);
        }

        $usuarios = $query->get()->map(function($user) {
            return [
                'id' => $user->id,
                'nombre_completo' => $user->getNombreCompletoAttribute(),
                'num_empleado' => $user->num_empleado,
                'tipo' => $user->tipo,
            ];
        });

        return response()->json([
            'success' => true,
            'usuarios' => $usuarios
        ]);
    }

    // Guardar grupo (crear o editar)
    public function guardar(Request $request)
    {
        if (!$this->puedeEditar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para editar.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
            'id' => 'nullable|exists:grupos,id',
            'semestre' => 'required|in:1°,2°,3°,4°,5°,6°',
            'grupo' => 'required|in:A,B',
            'carrera' => 'required|string',
            'id_asesor' => 'nullable|exists:users,id',
            'id_tutor' => 'nullable|exists:users,id',
        ]);

        // VALIDACIÓN 1: Verificar que el ASESOR no esté asignado a otro grupo (1 a 1)
        if ($request->filled('id_asesor')) {
            $asesorExistente = Grupo::where('id_asesor', $request->id_asesor);
            
            // Si estamos editando un grupo, excluir el grupo actual de la verificación
            if ($request->filled('id')) {
                $asesorExistente->where('id', '!=', $request->id);
            }
            
            if ($asesorExistente->exists()) {
                $asesor = User::find($request->id_asesor);
                $nombreAsesor = $asesor ? $asesor->getNombreCompletoAttribute() : 'El asesor';
                return response()->json([
                    'success' => false,
                    'message' => "⚠️ {$nombreAsesor} ya está asignado como asesor de otro grupo. Un docente solo puede ser asesor de UN grupo a la vez."
                ]);
            }
        }
        
        // VALIDACIÓN 2: Verificar que el TUTOR no esté asignado a este mismo grupo (evitar duplicado en el mismo grupo)
        // PERO un tutor SÍ puede estar en múltiples grupos diferentes
        if ($request->filled('id_tutor')) {
            $tutorEnMismoGrupo = Grupo::where('id_tutor', $request->id_tutor)
                ->where('semestre', $request->semestre)
                ->where('grupo', $request->grupo)
                ->where('carrera', $request->carrera);
            
            // Si estamos editando un grupo, excluir el grupo actual
            if ($request->filled('id')) {
                $tutorEnMismoGrupo->where('id', '!=', $request->id);
            }
            
            // Solo verificamos que no haya el mismo tutor en el MISMO grupo específico
            // NO verificamos en otros grupos diferentes
            if ($tutorEnMismoGrupo->exists()) {
                $tutor = User::find($request->id_tutor);
                $nombreTutor = $tutor ? $tutor->getNombreCompletoAttribute() : 'El tutor';
                return response()->json([
                    'success' => false,
                    'message' => "⚠️ {$nombreTutor} ya está asignado como tutor de este mismo grupo. Un grupo solo puede tener un tutor."
                ]);
            }
        }

        // VALIDACIÓN 3: Verificar unicidad del grupo (semestre + grupo + carrera)
        $query = Grupo::where('semestre', $request->semestre)
            ->where('grupo', $request->grupo)
            ->where('carrera', $request->carrera);

        if ($request->filled('id')) {
            $query->where('id', '!=', $request->id);
        }

        if ($query->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un grupo con este semestre, grupo y carrera.'
            ]);
        }

        try {
            if ($request->filled('id')) {
                $grupo = Grupo::findOrFail($request->id);
                $grupo->update($request->only(['semestre', 'grupo', 'carrera', 'id_asesor', 'id_tutor']));
                $mensaje = 'Grupo actualizado correctamente.';
            } else {
                $grupo = Grupo::create($request->only(['semestre', 'grupo', 'carrera', 'id_asesor', 'id_tutor']));
                $mensaje = 'Grupo creado correctamente.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'grupo' => $grupo
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar grupo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
    // Obtener un grupo por ID
    public function obtener($id)
    {
        if (!$this->puedeConsultar()) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $grupo = Grupo::with(['asesor', 'tutor'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'grupo' => [
                'id' => $grupo->id,
                'semestre' => $grupo->semestre,
                'grupo' => $grupo->grupo,
                'carrera' => $grupo->carrera,
                'id_asesor' => $grupo->id_asesor,
                'nombre_asesor' => $grupo->asesor ? $grupo->asesor->getNombreCompletoAttribute() : null,
                'id_tutor' => $grupo->id_tutor,
                'nombre_tutor' => $grupo->tutor ? $grupo->tutor->getNombreCompletoAttribute() : null,
                'cantidad_alumnos' => $grupo->alumnos()->where('estatus', true)->count(),
            ]
        ]);
    }

    // Eliminar grupo
    public function eliminar(Request $request, $id)
    {
        if (!$this->puedeEditar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para eliminar.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $grupo = Grupo::findOrFail($id);

        // Verificar si tiene alumnos asignados
        $cantidadAlumnos = $grupo->alumnos()->count();
        if ($cantidadAlumnos > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el grupo porque tiene {$cantidadAlumnos} alumnos asignados. Primero reasigne o dé de baja los alumnos."
            ]);
        }

        $grupo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grupo eliminado correctamente.',
            'limpiar_password' => true  // Indicador para limpiar el campo de contraseña
        ]);
    }

    // Migrar grupos al semestre siguiente
    public function migrarGrupos(Request $request)
    {
        if (!$this->puedeEditar()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar migraciones.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $resultados = [
            'migrados' => 0,
            'graduados' => 0,
            'errores' => [],
            'detalles' => []
        ];

        // Obtener todos los alumnos activos con grupo asignado
        $alumnos = Alumno::with(['grupo'])
            ->where('estatus', true)
            ->whereNotNull('id_grupo')
            ->get();

        if ($alumnos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay alumnos activos con grupo asignado para migrar.'
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($alumnos as $alumno) {
                $grupoActual = $alumno->grupo;
                
                if (!$grupoActual) {
                    $resultados['errores'][] = "Alumno {$alumno->nombre_completo} (ID: {$alumno->id}) no tiene grupo válido.";
                    continue;
                }

                $semestreActual = $grupoActual->semestre;
                $numeroSemestre = (int) str_replace('°', '', $semestreActual);
                
                // Caso 1: Está en 6° semestre → Graduación
                if ($numeroSemestre === 6) {
                    // Eliminar todas las becas asociadas
                    $becasEliminadas = AlumnoBeca::where('id_alumno', $alumno->id)->delete();
                    
                    // Desasociar del grupo y marcar como inactivo
                    $alumno->id_grupo = null;
                    $alumno->estatus = false;
                    $alumno->save();
                    
                    $resultados['graduados']++;
                    $resultados['detalles'][] = "✓ {$alumno->nombre_completo} - GRADUADO (6° semestre, becas eliminadas)";
                    
                    Log::info("Alumno graduado: {$alumno->nombre_completo} (ID: {$alumno->id})");
                    continue;
                }
                
                // Caso 2: Está en semestre 1° a 5° → Buscar grupo siguiente
                $siguienteSemestre = ($numeroSemestre + 1) . '°';
                
                $nuevoGrupo = Grupo::where('semestre', $siguienteSemestre)
                    ->where('grupo', $grupoActual->grupo)
                    ->where('carrera', $grupoActual->carrera)
                    ->first();
                
                if ($nuevoGrupo) {
                    $alumno->id_grupo = $nuevoGrupo->id;
                    $alumno->save();
                    
                    $resultados['migrados']++;
                    $resultados['detalles'][] = "✓ {$alumno->nombre_completo} - {$semestreActual} {$grupoActual->grupo} → {$siguienteSemestre} {$nuevoGrupo->grupo}";
                    
                    Log::info("Alumno migrado: {$alumno->nombre_completo} de {$semestreActual} a {$siguienteSemestre}");
                } else {
                    $resultados['errores'][] = "✗ {$alumno->nombre_completo} - No se encontró grupo destino para {$siguienteSemestre} {$grupoActual->grupo} {$grupoActual->carrera}";
                }
            }
            
            DB::commit();
            
            $mensaje = "Migración completada. ";
            $mensaje .= "Alumnos migrados: {$resultados['migrados']}. ";
            $mensaje .= "Alumnos graduados: {$resultados['graduados']}. ";
            
            if (count($resultados['errores']) > 0) {
                $mensaje .= " Errores: " . count($resultados['errores']);
            }
            
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'resultados' => $resultados
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en migración de grupos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error durante la migración: ' . $e->getMessage(),
                'resultados' => $resultados
            ], 500);
        }
    }
}