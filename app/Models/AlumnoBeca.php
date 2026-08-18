<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoBeca extends Model
{
    use HasFactory;

    protected $table = 'alumno_beca';

    protected $fillable = [
        'id_alumno',
        'id_beca',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function beca()
    {
        return $this->belongsTo(Beca::class, 'id_beca');
    }
}