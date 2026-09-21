<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'modulo',
        'submodulo',
        'id_laboratorio'
    ];

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }
}