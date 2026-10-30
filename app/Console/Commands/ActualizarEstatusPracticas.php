<?php

namespace App\Console\Commands;

use App\Models\Practica;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActualizarEstatusPracticas extends Command
{
    protected $signature = 'practicas:actualizar-estatus';
    protected $description = 'Cambia estatus de "Reservada" a "Cumplida" para prácticas cuyo horario ya terminó.';

    public function handle()
    {
        $ahora = Carbon::now();
        $hoy = $ahora->format('Y-m-d');
        $minutosAhora = $ahora->hour * 60 + $ahora->minute;

        // Horas de fin de cada bloque
        $horasFin = [
            1 => 8 * 60 + 50,   // 08:50
            2 => 9 * 60 + 40,   // 09:40
            3 => 10 * 60 + 30,  // 10:30
            4 => 11 * 60 + 50,  // 11:50
            5 => 12 * 60 + 40,  // 12:40
            6 => 13 * 60 + 30,  // 13:30
            7 => 14 * 60 + 20,  // 14:20
            8 => 15 * 60 + 0,   // 15:00
        ];

        // 1. Prácticas de días anteriores → Cumplidas
        $anteriores = Practica::where('estatus', 'Reservada')
            ->where('fecha_reserva', '<', $hoy)
            ->update(['estatus' => 'Cumplida']);

        // 2. Prácticas de hoy cuya hora ya terminó → Cumplidas
        $deHoy = Practica::where('estatus', 'Reservada')
            ->where('fecha_reserva', $hoy)
            ->with('horario')
            ->get();

        $actualizadasHoy = 0;
        foreach ($deHoy as $p) {
            if (!$p->horario) continue;
            $horaFin = $horasFin[$p->horario->hora] ?? null;
            if ($horaFin === null) continue;

            if ($minutosAhora >= $horaFin) {
                $p->estatus = 'Cumplida';
                $p->save();
                $actualizadasHoy++;
            }
        }

        $this->info("Actualizadas: {$anteriores} de días anteriores, {$actualizadasHoy} de hoy.");
    }
}