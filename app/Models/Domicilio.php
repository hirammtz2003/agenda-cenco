<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    use HasFactory;

    protected $table = 'domicilio';

    protected $fillable = [
        'calle',
        'num_ext',
        'num_int',
        'colonia',
        'localidad',
        'municipio',
        'cp',
        'estado',
        'telefono_domicilio',
    ];

    public $timestamps = false;
}