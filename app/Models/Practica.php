<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practica extends Model
{
    use HasFactory;

    protected $table = 'practicas';

    protected $fillable = [
        'nombre',
        'no_actividad',
        'competencia',
        'atributo',
        'materiales',
        'herramientas',
        'estatus',
        'fecha_solicitud',
        'notas',
        'id_horario',
    ];

    protected $casts = [
        'materiales' => 'array',
        'herramientas' => 'array',
        'fecha_solicitud' => 'datetime',
    ];

    // Relación con horario
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario');
    }

    public $timestamps = false;

    // Relaciones
    public function autorizante()
    {
        return $this->belongsTo(User::class, 'id_autorizante');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'id_solicitante');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'id_actividad');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }
}