<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    use HasFactory;

    protected $table = 'familiares';

    protected $fillable = [
        'vive',
        'nombre',
        'apellido1',
        'apellido2',
        'fecha_nacimiento',
        'telefono_celular',
        'id_domicilio',
        'escolaridad',
        'ocupacion',
        'lugar_trabajo',
        'horario_laboral',
        'domicilio_trabajo',
        'telefono_trabajo',
    ];

    protected $casts = [
        'vive' => 'boolean',
        'fecha_nacimiento' => 'date',
    ];

    public $timestamps = false;

    public function domicilio()
    {
        return $this->belongsTo(Domicilio::class, 'id_domicilio');
    }

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_familiar', 'id_familiar', 'id_alumno')
                    ->withPivot('parentesco', 'tutor', 'contacto_emergencia');
    }

    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido1 . ' ' . ($this->apellido2 ?? ''));
    }
}