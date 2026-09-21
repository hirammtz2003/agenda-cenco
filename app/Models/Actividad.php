<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'numero',
        'competencia',
        'atributo',
        'materia'
    ];

    public $timestamps = false;

    // Relación con prácticas
    public function practicas()
    {
        return $this->hasMany(Practica::class, 'id_actividad');
    }
}