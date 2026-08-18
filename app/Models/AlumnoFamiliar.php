<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'alumno_familiar';

    protected $fillable = [
        'id_alumno',
        'id_familiar',
        'parentesco',
        'tutor',
        'contacto_emergencia',
    ];

    protected $casts = [
        'tutor' => 'boolean',
        'contacto_emergencia' => 'integer',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function familiar()
    {
        return $this->belongsTo(Familiar::class, 'id_familiar');
    }
}