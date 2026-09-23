<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';
    public $timestamps = false;

    protected $fillable = [
        'hora',
        'dia',
        'hora_fija',
        'id_grupo',
        'id_maestro',
        'id_materia',
        'id_laboratorio'
    ];

    protected $casts = [
        'hora_fija' => 'boolean'
    ];

    // Relaciones
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function maestro()
    {
        return $this->belongsTo(User::class, 'id_maestro');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function laboratorio()  // ← nueva relación
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    // Accessors para mostrar datos legibles
    public function getDiaLegibleAttribute()
    {
        return match($this->dia) {
            'L' => 'Lunes',
            'M' => 'Martes',
            'X' => 'Miércoles',
            'J' => 'Jueves',
            'V' => 'Viernes',
            default => '—'
        };
    }

    public function getHoraLegibleAttribute()
    {
        $horas = [
            1 => '1 (8:00-8:50)',
            2 => '2 (8:50-9:40)',
            3 => '3 (9:40-10:30)',
            4 => '4 (11:00-11:50)',
            5 => '5 (11:50-12:40)',
            6 => '6 (12:40-13:30)',
            7 => '7 (13:30-14:20)',
            8 => '8 (14:20-15:00)',
        ];
        return $horas[$this->hora] ?? '—';
    }
}