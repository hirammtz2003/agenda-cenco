<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadesRecreativas extends Model
{
    use HasFactory;

    protected $table = 'actividades_recreativas';

    protected $fillable = [
        'id_alumno',
        'pasatiempo_favorito',
        'horas_pasatiempo_dedicadas',
        'deporte_practicado',
        'horas_deporte_dedicadas',
        'horas_dia_tv',
        'horas_dia_compu',
        'uso_frecuente_compu',
        'temas_quien_chat',
    ];

    protected $casts = [
        'deporte_practicado' => 'array', // Convierte JSON a array automáticamente
        'horas_pasatiempo_dedicadas' => 'integer',
        'horas_deporte_dedicadas' => 'integer',
        'horas_dia_tv' => 'integer',
        'horas_dia_compu' => 'integer',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}