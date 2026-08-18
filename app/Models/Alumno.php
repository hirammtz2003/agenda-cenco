<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';

    protected $fillable = [
        'foto',
        'num_control',
        'nombre',
        'apellido1',
        'apellido2',
        'id_lugar_nacimiento',
        'id_domicilio',
        'id_secundaria_procedencia',
        'id_grupo',
        'telefono_celular',
        'email_personal',
        'email_institucional',
        'curp',
        'nss',
        'estatus',
    ];

    protected $casts = [
        'estatus' => 'boolean',
    ];

    public $timestamps = false;

    // Relaciones
    public function lugarNacimiento()
    {
        return $this->belongsTo(LugarNacimiento::class, 'id_lugar_nacimiento');
    }

    public function domicilio()
    {
        return $this->belongsTo(Domicilio::class, 'id_domicilio');
    }

    public function secundaria()
    {
        return $this->belongsTo(SecundariaProcedencia::class, 'id_secundaria_procedencia');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    // Relaciones para consulta individual
    public function trabajo()
    {
        return $this->hasOne(TrabajoAlumno::class, 'id_alumno');
    }

    public function datosFamiliares()
    {
        return $this->hasOne(DatosFamiliares::class, 'id_alumno');
    }

    public function infoSocioeco()
    {
        return $this->hasOne(InfoSocioeco::class, 'id_alumno');
    }

    public function datosAcademicos()
    {
        return $this->hasOne(DatosAcademicos::class, 'id_alumno');
    }

    public function problemaAprendizaje()
    {
        return $this->hasOne(ProblemaAprendizaje::class, 'id_alumno');
    }

    public function datosSalud()
    {
        return $this->hasOne(DatosSalud::class, 'id_alumno');
    }

    public function actividadesRecreativas()
    {
        return $this->hasOne(ActividadesRecreativas::class, 'id_alumno');
    }

    public function becas()
    {
        return $this->hasMany(AlumnoBeca::class, 'id_alumno');
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido1 . ' ' . ($this->apellido2 ?? ''));
    }

    // En Alumno.php
    public function familiares()
    {
        return $this->belongsToMany(Familiar::class, 'alumno_familiar', 'id_alumno', 'id_familiar')
                    ->withPivot('parentesco', 'tutor', 'contacto_emergencia');
    }
}