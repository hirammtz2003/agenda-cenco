<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    use HasFactory;

    protected $table = 'laboratorios';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_laboratorio');
    }
}