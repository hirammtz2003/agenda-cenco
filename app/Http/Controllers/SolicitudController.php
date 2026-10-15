<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\Materia;
use App\Models\Practica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    public function index()
    {
        return view('usuarios.solicitud.solicitud');
    }

    // ============ AUTOCOMPLETES / SELECTS DINÁMICOS ============

    public function listarLaboratorios()
    {
        try {
            $laboratorios = Laboratorio::orderBy('nombre')->get();
            return response()->json(['success' => true, 'laboratorios' => $laboratorios]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

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

    // ============ FECHA POR DEFECTO ============

    /**
     * Calcula la fecha por defecto (próximo día hábil).
     * Salta sábados, domingos y días inhábiles.
     */
    public function calcularFechaInicioDefault()
    {
        try {
            $fecha = Carbon::tomorrow();
            $diasInhabiles = Fecha::where('tipo', 'Día inhábil')->pluck('fecha')->map(function ($f) {
                return $f instanceof \Carbon\Carbon ? $f->format('Y-m-d') : (string) $f;
            })->toArray();

            // Máximo 30 iteraciones para evitar loops infinitos
            $intentos = 0;
            while ($intentos < 30) {
                $esFinDeSemana = $fecha->isWeekend();
                $esInhabil = in_array($fecha->format('Y-m-d'), $diasInhabiles);

                if (!$esFinDeSemana && !$esInhabil) {
                    break;
                }
                $fecha->addDay();
                $intentos++;
            }

            return response()->json([
                'success' => true,
                'fecha' => $fecha->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ BÚSQUEDA DE HORAS DISPONIBLES ============

    /**
     * Devuelve los horarios disponibles (registros en tabla horarios)
     * que coinciden con laboratorio, materia y son a partir de la fecha indicada.
     * Excluye días inhábiles y horarios ya reservados (a menos que hora_fija sea true).
     */
    public function buscarHorasDisponibles(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'id_laboratorio' => 'required|exists:laboratorios,id',
                'id_materia' => 'required|exists:materias,id',
                'fecha_inicio' => 'required|date',
                'horas_requeridas' => 'required|integer|min:1|max:168',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $validator->errors()->all())
                ], 422);
            }

            $fechaInicio = Carbon::parse($request->fecha_inicio);
            $diasInhabiles = Fecha::where('tipo', 'Día inhábil')->pluck('fecha')->map(function ($f) {
                return $f instanceof \Carbon\Carbon ? $f->format('Y-m-d') : (string) $f;
            })->toArray();

            // Obtener los horarios que coinciden con laboratorio y materia
            $horarios = Horario::with(['grupo', 'maestro', 'materia', 'laboratorio'])
                ->where('id_laboratorio', $request->id_laboratorio)
                ->where('id_materia', $request->id_materia)
                ->where('id_maestro', Auth::id())
                ->orderBy('dia')
                ->orderBy('hora')
                ->get();

            // Mapear días de la semana a Carbon::dayOfWeek (0=domingo, 1=lunes...)
            $diasMap = ['L' => 1, 'M' => 2, 'X' => 3, 'J' => 4, 'V' => 5];

            // Generar lista de fechas futuras (próximas 12 semanas)
            $fechaActual = $fechaInicio->copy();
            $fechasValidas = [];
            $semanasAdelante = 12;
            $fechaLimite = $fechaInicio->copy()->addWeeks($semanasAdelante);

            while ($fechaActual->lte($fechaLimite)) {
                $fechaStr = $fechaActual->format('Y-m-d');
                $diaSemana = $fechaActual->dayOfWeek;

                if (!in_array($fechaStr, $diasInhabiles) && $diaSemana >= 1 && $diaSemana <= 5) {
                    $fechasValidas[] = $fechaActual->copy();
                }
                $fechaActual->addDay();
            }

            // Construir la lista de "horas individuales disponibles"
            $resultado = [];
            foreach ($fechasValidas as $fecha) {
                $diaSemana = $fecha->dayOfWeek;

                foreach ($horarios as $h) {
                    // Verificar que el horario corresponda al día de la semana de esta fecha
                    if (($diasMap[$h->dia] ?? null) !== $diaSemana) {
                        continue;
                    }

                    // Si el horario NO es hora fija, verificar que no esté ya reservado
                    /*if (!$h->hora_fija) {
                        
                    }*/
                        // Siempre verificar que no esté reservada esa fecha + horario
                        $yaReservado = Practica::where('id_horario', $h->id)
                            ->where('fecha_reserva', $fecha->format('Y-m-d'))
                            ->whereIn('estatus', ['Reservada', 'Cumplida'])
                            ->exists();
                        if ($yaReservado) {
                            continue;
                        }
                    $resultado[] = [
                        'id_horario' => $h->id,
                        'fecha' => $fecha->format('Y-m-d'),
                        'fecha_legible' => $fecha->format('d/m/Y'),
                        'dia_semana' => $h->dia_legible,
                        'hora' => $h->hora,
                        'hora_legible' => $h->hora_legible,
                        'hora_fija' => (bool) $h->hora_fija,
                        'grupo_nombre' => $h->grupo ? "{$h->grupo->semestre} {$h->grupo->grupo} - {$h->grupo->carrera}" : null,
                        'maestro_nombre' => $h->maestro ? $h->maestro->nombre_completo : null,
                        'materia_nombre' => $h->materia ? $h->materia->nombre : null,
                        'laboratorio_nombre' => $h->laboratorio ? $h->laboratorio->nombre : null,
                    ];
                }
            }

            // Ordenar por fecha y hora
            usort($resultado, function ($a, $b) {
                if ($a['fecha'] === $b['fecha']) {
                    return $a['hora'] <=> $b['hora'];
                }
                return $a['fecha'] <=> $b['fecha'];
            });

            // Limitar a 2× las horas requeridas (para dar opciones al usuario)
            $limite = ((int) $request->horas_requeridas) * 2;
            $resultado = array_slice($resultado, 0, $limite);

            return response()->json([
                'success' => true,
                'horas' => $resultado,
                'total' => count($resultado)
            ]);
        } catch (\Exception $e) {
            Log::error('Error al buscar horas disponibles: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ GUARDAR SOLICITUD ============

    public function store(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'current_password' => 'required|current_password',
                'reservas' => 'required|array|min:1',
                'reservas.*.id_horario' => 'required|exists:horarios,id',
                'reservas.*.fecha' => 'required|date',
                'nombre' => 'nullable|string|max:50',
                'no_actividad' => 'nullable|integer',
                'competencia' => 'nullable|string|max:255',
                'atributo' => 'nullable|string|max:255',
                'materiales' => 'nullable|array',
                'herramientas' => 'nullable|array',
                'notas' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $validator->errors()->all())
                ], 422);
            }

            DB::beginTransaction();

            $creadas = 0;
            foreach ($request->reservas as $reserva) {
                // Verificar duplicado antes de insertar
                $existe = Practica::where('id_horario', $reserva['id_horario'])
                    ->where('fecha_reserva', $reserva['fecha'])
                    ->whereIn('estatus', ['Reservada', 'Cumplida'])
                    ->exists();
                if ($existe) {
                    continue;
                }

                Practica::create([
                    'nombre' => $request->nombre,
                    'no_actividad' => $request->no_actividad,
                    'competencia' => $request->competencia,
                    'atributo' => $request->atributo,
                    'materiales' => $request->materiales,
                    'herramientas' => $request->herramientas,
                    'estatus' => 'Reservada',
                    'fecha_solicitud' => now(),
                    'notas' => $request->notas,
                    'id_horario' => $reserva['id_horario'],
                    'fecha_reserva' => $reserva['fecha'],
                ]);
                $creadas++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "✅ Se crearon {$creadas} reserva(s) correctamente.",
                'creadas' => $creadas,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}