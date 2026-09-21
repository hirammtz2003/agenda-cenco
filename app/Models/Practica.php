<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practica extends Model
{
    use HasFactory;

    protected $table = 'practicas';

    protected $fillable = [
        'fecha_inicio',
        'fecha_final',
        'horas_requeridas',
        'materiales',
        'herramientas',
        'estatus',
        'fecha_solicitud',
        'notas',
        'id_autorizante',
        'id_solicitante',
        'id_actividad',
        'id_grupo'
    ];

    protected $casts = [
        'materiales' => 'array',
        'herramientas' => 'array',
        'fecha_inicio' => 'date',
        'fecha_final' => 'date',
        'fecha_solicitud' => 'datetime'
    ];

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