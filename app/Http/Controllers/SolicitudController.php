<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Grupo;
use App\Models\Practica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    /**
     * Show the solicitud form.
     */
    public function index()
    {
        return view('usuarios.solicitud.solicitud');
    }

    /**
     * Search for materias (autocomplete).
     */
    public function buscarMaterias(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $materias = Actividad::where('materia', 'LIKE', "%{$query}%")
            ->distinct()
            ->limit(10)
            ->pluck('materia');

        return response()->json($materias);
    }

    /**
     * Search for grupos (autocomplete).
     */
    public function buscarGrupos(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $grupos = Grupo::where('semestre', 'LIKE', "%{$query}%")
            ->orWhere('grupo', 'LIKE', "%{$query}%")
            ->orWhere('carrera', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($grupo) {
                return [
                    'id' => $grupo->id,
                    'nombre' => "{$grupo->semestre} {$grupo->grupo} - {$grupo->carrera}"
                ];
            });

        return response()->json($grupos);
    }

    /**
     * Store a new solicitud.
     */
    public function store(Request $request)
    {
        try {
            // Validación básica (ningún campo es obligatorio)
            $request->validate([
                'current_password' => 'required|current_password',
                'materia' => 'nullable|string|max:50',
                'grupo_id' => 'nullable|exists:grupos,id',
                'fecha_inicio' => 'nullable|date',
                'fecha_final' => 'nullable|date|after_or_equal:fecha_inicio',
                'horas_requeridas' => 'nullable|integer|min:0|max:168',
                'numero_actividad' => 'nullable|integer|min:0',
                'competencia' => 'nullable|string|max:255',
                'atributo' => 'nullable|string|max:255',
                'materiales' => 'nullable|array',
                'herramientas' => 'nullable|array',
                'notas' => 'nullable|string|max:255'
            ]);

            DB::beginTransaction();

            // Crear o buscar actividad
            $actividad = null;
            if ($request->filled('materia') || $request->filled('competencia') || $request->filled('atributo')) {
                $actividad = Actividad::firstOrCreate([
                    'materia' => $request->materia,
                    'numero' => $request->numero_actividad,
                    'competencia' => $request->competencia,
                    'atributo' => $request->atributo
                ]);
            }

            // Crear práctica
            $practica = Practica::create([
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_final' => $request->fecha_final,
                'horas_requeridas' => $request->horas_requeridas,
                'materiales' => $request->materiales,
                'herramientas' => $request->herramientas,
                'estatus' => 'Pendiente',
                'fecha_solicitud' => now(),
                'notas' => $request->notas,
                'id_autorizante' => null,
                'id_solicitante' => Auth::id(),
                'id_actividad' => $actividad ? $actividad->id : null,
                'id_grupo' => $request->grupo_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Solicitud enviada correctamente.',
                'practica' => $practica,
                'limpiar_password' => true
            ]);

        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Debes iniciar sesión para realizar una solicitud.'
            ], 401);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar solicitud: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => '❌ Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}