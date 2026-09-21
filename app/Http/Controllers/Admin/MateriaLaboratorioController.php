<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\Laboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MateriaLaboratorioController extends Controller
{
    // ============ VISTA PRINCIPAL ============
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        return view('administrador.config-materia-lab.materias');
    }

    // ============ MATERIAS ============

    public function listarMaterias(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $query = Materia::with('laboratorio');

            if ($request->filled('modulo')) {
                $query->where('modulo', $request->modulo);
            }

            if ($request->filled('submodulo')) {
                $query->where('submodulo', $request->submodulo);
            }

            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                      ->orWhereHas('laboratorio', function ($q2) use ($busqueda) {
                          $q2->where('nombre', 'LIKE', "%{$busqueda}%");
                      });
                });
            }

            $materias = $query->orderBy('nombre')->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'nombre' => $m->nombre,
                    'descripcion' => $m->descripcion,
                    'modulo' => $m->modulo,
                    'submodulo' => $m->submodulo,
                    'id_laboratorio' => $m->id_laboratorio,
                    'laboratorio_nombre' => $m->laboratorio ? $m->laboratorio->nombre : null,
                ];
            });

            return response()->json([
                'success' => true,
                'materias' => $materias,
                'total' => $materias->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al listar materias: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function guardarMateria(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'id' => 'nullable|exists:materias,id',
                'nombre' => 'required|string|max:50',
                'descripcion' => 'required|string|max:255',
                'modulo' => ['nullable', Rule::in(['I', 'II', 'III', 'IV', 'V', 'No aplica'])],
                'submodulo' => ['nullable', Rule::in(['1', '2', '3', 'No aplica'])],
                'id_laboratorio' => 'nullable|exists:laboratorios,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            // Verificar unicidad de nombre (opcional)
            $query = Materia::where('nombre', $request->nombre);
            if ($request->filled('id')) {
                $query->where('id', '!=', $request->id);
            }
            if ($query->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Ya existe una materia con este nombre.'
                ]);
            }

            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'modulo' => $request->modulo,
                'submodulo' => $request->submodulo,
                'id_laboratorio' => $request->id_laboratorio,
            ];

            if ($request->filled('id')) {
                $materia = Materia::findOrFail($request->id);
                $materia->update($data);
                $mensaje = '✅ Materia actualizada correctamente.';
            } else {
                $materia = Materia::create($data);
                $mensaje = '✅ Materia creada correctamente.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'materia' => $materia,
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar materia: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function obtenerMateria($id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $materia = Materia::with('laboratorio')->find($id);

            if (!$materia) {
                return response()->json(['success' => false, 'message' => 'Materia no encontrada'], 404);
            }

            return response()->json([
                'success' => true,
                'materia' => [
                    'id' => $materia->id,
                    'nombre' => $materia->nombre,
                    'descripcion' => $materia->descripcion,
                    'modulo' => $materia->modulo,
                    'submodulo' => $materia->submodulo,
                    'id_laboratorio' => $materia->id_laboratorio,
                    'laboratorio_nombre' => $materia->laboratorio ? $materia->laboratorio->nombre : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminarMateria(Request $request, $id)
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

            $materia = Materia::findOrFail($id);
            $materia->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Materia eliminada correctamente.',
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ LABORATORIOS ============

    public function listarLaboratorios(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $query = Laboratorio::query();

            if ($request->filled('busqueda')) {
                $query->where('nombre', 'LIKE', "%{$request->busqueda}%");
            }

            $laboratorios = $query->orderBy('nombre')->get();

            return response()->json([
                'success' => true,
                'laboratorios' => $laboratorios,
                'total' => $laboratorios->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function guardarLaboratorio(Request $request)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'id' => 'nullable|exists:laboratorios,id',
                'nombre' => 'required|string|max:30',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $query = Laboratorio::where('nombre', $request->nombre);
            if ($request->filled('id')) {
                $query->where('id', '!=', $request->id);
            }
            if ($query->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Ya existe un laboratorio con este nombre.'
                ]);
            }

            if ($request->filled('id')) {
                $lab = Laboratorio::findOrFail($request->id);
                $lab->update(['nombre' => $request->nombre]);
                $mensaje = '✅ Laboratorio actualizado correctamente.';
            } else {
                $lab = Laboratorio::create(['nombre' => $request->nombre]);
                $mensaje = '✅ Laboratorio creado correctamente.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'laboratorio' => $lab,
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function obtenerLaboratorio($id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
            }

            $lab = Laboratorio::find($id);
            if (!$lab) {
                return response()->json(['success' => false, 'message' => 'Laboratorio no encontrado'], 404);
            }

            return response()->json(['success' => true, 'laboratorio' => $lab]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function eliminarLaboratorio(Request $request, $id)
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

            $lab = Laboratorio::findOrFail($id);
            $lab->delete();

            return response()->json([
                'success' => true,
                'message' => '✅ Laboratorio eliminado correctamente.',
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}