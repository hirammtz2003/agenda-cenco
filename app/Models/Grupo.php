<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    protected $fillable = [
        'semestre',
        'grupo',
        'carrera',
        'id_asesor',
        'id_tutor',
    ];

    protected $casts = [
        'id_asesor' => 'integer',
        'id_tutor' => 'integer',
    ];

    public $timestamps = false;

    // Relación con el asesor (usuario tipo Docente)
    public function asesor()
    {
        return $this->belongsTo(User::class, 'id_asesor');
    }

    // Relación con el tutor (usuario tipo Trabajo Social)
    public function tutor()
    {
        return $this->belongsTo(User::class, 'id_tutor');
    }

    // Accessor para obtener nombre completo del asesor
    public function getNombreAsesorAttribute()
    {
        return $this->asesor ? $this->asesor->getNombreCompletoAttribute() : 'No asignado';
    }

    // Accessor para obtener nombre completo del tutor
    public function getNombreTutorAttribute()
    {
        return $this->tutor ? $this->tutor->getNombreCompletoAttribute() : 'No asignado';
    }

    // Agrega esta relación (si no existe)
    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_grupo');
    }

    // Método para obtener cantidad de alumnos
    public function getCantidadAlumnosAttribute()
    {
        return $this->alumnos()->where('estatus', true)->count();
    }
}