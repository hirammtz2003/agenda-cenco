<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecundariaProcedencia extends Model
{
    use HasFactory;

    protected $table = 'secundaria_procedencia';

    public $timestamps = false; // ¡DESACTIVA created_at Y updated_at!

    protected $fillable = [
        'nombre',
        'tipo',
        'localidad',
        'municipio',
        'estado',
        'pais',
    ];
}