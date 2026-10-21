<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fecha;
use App\Models\Laboratorio;
use App\Models\Practica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HorarioGeneralController extends Controller
{
    /**
     * Vista principal.
     */
    public function index()
    {
        return view('administrador.horario-general.horario-general');
    }

    /**
     * Devuelve los datos de la cuadrícula para una semana y laboratorio.
     * Formato: { semanas: { lunes: 'YYYY-MM-DD', viernes: 'YYYY-MM-DD', mesInicio: 'Octubre', mesFin: 'Octubre' }, dias: [...], horas: [...] }
     */
    public function datos(Request $request)
    {
        try {
            $user = Auth::user();
            $esAdmin = $user->isAdmin();

            // Fecha base (lunes de la semana). Si no se envía, usar la semana actual.
            $fechaBase = $request->filled('fecha')
                ? Carbon::parse($request->fecha)
                : Carbon::now();

            // Calcular lunes y viernes de esa semana
            $lunes = $fechaBase->copy()->startOfWeek(Carbon::MONDAY);
            $viernes = $lunes->copy()->addDays(4);

            // Datos de meses
            $mesInicio = $this->nombreMes($lunes->month);
            $mesFin = $this->nombreMes($viernes->month);
            $mesHeader = $mesInicio === $mesFin
                ? $mesInicio
                : "{$mesInicio} - {$mesFin}";

            // Días de la semana con su fecha
            $dias = [];
            $nombres = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
            for ($i = 0; $i < 5; $i++) {
                $dia = $lunes->copy()->addDays($i);
                $dias[] = [
                    'nombre' => $nombres[$i],
                    'numero' => $dia->day,
                    'fecha' => $dia->format('Y-m-d'),
                ];
            }

            // Días inhábiles en esta semana
            $diasInhabiles = Fecha::where('tipo', 'Día inhábil')
                ->whereBetween('fecha', [
                    $lunes->format('Y-m-d'),
                    $viernes->format('Y-m-d'),
                ])
                ->pluck('fecha')
                ->map(function ($f) {
                    return $f instanceof \Carbon\Carbon ? $f->format('Y-m-d') : (string) $f;
                })
                ->toArray();

            // Laboratorios
            $laboratorios = Laboratorio::orderBy('nombre')->get();

            // Por cada laboratorio, armar su cuadrícula
            $resultado = [];
            foreach ($laboratorios as $lab) {
                $cuadricula = $this->armarCuadricula($lab, $lunes, $dias, $diasInhabiles, $user, $esAdmin);
                $resultado[] = [
                    'laboratorio' => [
                        'id' => $lab->id,
                        'nombre' => $lab->nombre,
                    ],
                    'cuadricula' => $cuadricula,
                ];
            }

            return response()->json([
                'success' => true,
                'es_admin' => $esAdmin,
                'semana' => [
                    'lunes' => $lunes->format('Y-m-d'),
                    'viernes' => $viernes->format('Y-m-d'),
                    'mesHeader' => $mesHeader,
                ],
                'dias' => $dias,
                'diasInhabiles' => $diasInhabiles,
                'laboratorios' => $resultado,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en horario general: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Arma la cuadrícula de un laboratorio para la semana dada.
     * Retorna un array de 8 filas (horas 1-8), cada una con 5 columnas (días).
     */
    private function armarCuadricula($lab, $lunes, $dias, $diasInhabiles, $user, $esAdmin)
    {
        // Inicializar cuadrícula vacía
        $cuadricula = [];
        for ($h = 1; $h <= 8; $h++) {
            $fila = ['hora' => $h];
            foreach ($dias as $dia) {
                $fila[$dia['fecha']] = [
                    'estado' => 'vacio',
                    'docente' => null,
                    'materia' => null,
                    'grupo' => null,
                ];
            }
            $cuadricula[$h] = $fila;
        }

        // Marcar días inhábiles
        foreach ($dias as $dia) {
            if (in_array($dia['fecha'], $diasInhabiles)) {
                for ($h = 1; $h <= 8; $h++) {
                    $cuadricula[$h][$dia['fecha']] = [
                        'estado' => 'inhabil',
                        'docente' => null,
                        'materia' => null,
                        'grupo' => null,
                    ];
                }
            }
        }

        // Cargar las prácticas reservadas en ese laboratorio y semana
        $practicas = Practica::with([
            'horario.grupo',
            'horario.maestro',
            'horario.materia',
        ])
            ->whereHas('horario', function ($q) use ($lab) {
                $q->where('id_laboratorio', $lab->id);
            })
            ->whereBetween('fecha_reserva', [
                $lunes->format('Y-m-d'),
                $lunes->copy()->addDays(4)->format('Y-m-d'),
            ])
            ->whereIn('estatus', ['Reservada', 'Cumplida'])
            ->get();

        // Mapear cada práctica a su celda
        foreach ($practicas as $p) {
            if (!$p->horario || !$p->fecha_reserva) continue;

            $hora = $p->horario->hora;
            $fecha = $p->fecha_reserva->format('Y-m-d');

            if (!isset($cuadricula[$hora][$fecha])) continue;
            if ($cuadricula[$hora][$fecha]['estado'] === 'inhabil') continue;

            $horario = $p->horario;
            $esPropia = $horario->id_maestro === $user->id;

            // 🔒 Si NO es admin y la reserva es de otro docente → mostrar como "reservada"
            if (!$esAdmin && !$esPropia) {
                $cuadricula[$hora][$fecha] = [
                    'estado' => 'reservada',
                    'docente' => null,
                    'materia' => null,
                    'grupo' => null,
                ];
                continue;
            }

            // Admin o dueño de la reserva → mostrar todos los datos
            $cuadricula[$hora][$fecha] = [
                'estado' => 'ocupado',
                'docente' => $horario->maestro ? $horario->maestro->nombre_completo : '—',
                'materia' => $horario->materia ? $horario->materia->nombre : '—',
                'grupo' => $horario->grupo
                    ? "{$horario->grupo->semestre} {$horario->grupo->grupo} - {$horario->grupo->carrera}"
                    : '—',
            ];
        }

        return $cuadricula;
    }

    private function nombreMes($numeroMes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $meses[$numeroMes] ?? '';
    }
}