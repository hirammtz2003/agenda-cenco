<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoSocioeco extends Model
{
    use HasFactory;

    protected $table = 'info_socioeco';

    protected $fillable = [
        'id_alumno',
        'auto_propio_alumno',
        'transporte',
        'traslado_horas',
        'traslado_minutos',
        'estado_civil_alumno',
        'num_hijos',
        'edades_hijos',
        'monto_apoyo',
        'gasto_comida_transporte',
        'comidas_diarias',
    ];

    protected $casts = [
        'auto_propio_alumno' => 'boolean',
        'transporte' => 'array', // Automáticamente convierte JSON a array
        'num_hijos' => 'integer',
        'traslado_horas' => 'integer',
        'traslado_minutos' => 'integer',
        'monto_apoyo' => 'integer',
        'gasto_comida_transporte' => 'integer',
        'comidas_diarias' => 'integer',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}