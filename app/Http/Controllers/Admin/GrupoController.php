<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{
    public function index()
    {
        // Verificar que el usuario es administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        return view('administrador.config-grup.grupos');
    }

    // Listar grupos con cantidad de alumnos
    public function listar(Request $request)
    {
        // Verificar que el usuario es administrador
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $query = Grupo::query();

        // Filtros
        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        if ($request->filled('grupo')) {
            $query->where('grupo', $request->grupo);
        }

        if ($request->filled('carrera')) {
            $query->where('carrera', $request->carrera);
        }

        $grupos = $query->orderBy('semestre')->orderBy('grupo')->get();
        $total = $grupos->count();

        return response()->json([
            'success' => true,
            'grupos' => $grupos,
            'total' => $total
        ]);
    }

    // Guardar grupo (crear o editar)
    public function guardar(Request $request)
    {
        // Verificar que el usuario es administrador
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para editar.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
            'id' => 'nullable|exists:grupos,id',
            'semestre' => ['required', Rule::in(['1°', '2°', '3°', '4°', '5°', '6°'])],
            'grupo' => ['required', Rule::in(['A', 'B'])],
            'carrera' => ['required', Rule::in([
                'Soporte y Mantenimiento de Equipo de Cómputo',
                'Soporte y Gestión de Tecnologías Informáticas',
                'Enfermería General',
                'Ventas',
                'Diseño Gráfico Digital'
            ])],
        ]);

        // Verificar unicidad del grupo (semestre + grupo + carrera)
        $query = Grupo::where('semestre', $request->semestre)
            ->where('grupo', $request->grupo)
            ->where('carrera', $request->carrera);

        if ($request->filled('id')) {
            $query->where('id', '!=', $request->id);
        }

        if ($query->exists()) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Ya existe un grupo con este semestre, grupo y carrera.'
            ]);
        }

        try {
            if ($request->filled('id')) {
                $grupo = Grupo::findOrFail($request->id);
                $grupo->update($request->only(['semestre', 'grupo', 'carrera']));
                $mensaje = '✅ Grupo actualizado correctamente.';
            } else {
                $grupo = Grupo::create($request->only(['semestre', 'grupo', 'carrera']));
                $mensaje = '✅ Grupo creado correctamente.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'grupo' => $grupo,
                'limpiar_password' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al guardar grupo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener un grupo por ID
    public function obtener($id)
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Sin permisos'
                ], 403);
            }

            $grupo = Grupo::findOrFail($id);

            return response()->json([
                'success' => true,
                'grupo' => [
                    'id' => $grupo->id,
                    'semestre' => $grupo->semestre,
                    'grupo' => $grupo->grupo,
                    'carrera' => $grupo->carrera
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Grupo no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener grupo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener grupo: ' . $e->getMessage()
            ], 500);
        }
    }

    // Eliminar grupo
    public function eliminar(Request $request, $id)
    {
        // Verificar que el usuario es administrador
        if (!Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para eliminar.'], 403);
        }

        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $grupo = Grupo::findOrFail($id);
        $grupo->delete();

        return response()->json([
            'success' => true,
            'message' => '✅ Grupo eliminado correctamente.',
            'limpiar_password' => true
        ]);
    }
}