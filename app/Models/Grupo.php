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
    ];

    public $timestamps = false;

    public function getNombreCompletoAttribute()
    {
        return "{$this->semestre} {$this->grupo} - {$this->carrera}";
    }
}