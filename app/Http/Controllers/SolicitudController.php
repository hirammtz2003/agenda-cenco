<?php

namespace App\Http\Controllers;

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
            $request->validate([
                'current_password' => 'required|current_password',
                'id_horario' => 'nullable|exists:horarios,id',
                'nombre' => 'nullable|string|max:50',
                'no_actividad' => 'nullable|integer',
                'competencia' => 'nullable|string|max:255',
                'atributo' => 'nullable|string|max:255',
                'materiales' => 'nullable|array',
                'herramientas' => 'nullable|array',
                'notas' => 'nullable|string|max:255',
            ]);

            $practica = Practica::create([
                'nombre' => $request->nombre,
                'no_actividad' => $request->no_actividad,
                'competencia' => $request->competencia,
                'atributo' => $request->atributo,
                'materiales' => $request->materiales,
                'herramientas' => $request->herramientas,
                'estatus' => 'Reservada',
                'fecha_solicitud' => now(),
                'notas' => $request->notas,
                'id_horario' => $request->id_horario,
            ]);

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
            Log::error('Error al guardar solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}