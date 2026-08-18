<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoAlumno extends Model
{
    use HasFactory;

    protected $table = 'trabajo_alumno';

    protected $fillable = [
        'id_alumno',
        'lugar_trabajo',
        'horario_laboral',
        'domicilio_trabajo',
        'telefono_trabajo',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}