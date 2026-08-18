<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    use HasFactory;

    protected $table = 'becas';

    protected $fillable = [
        'tipo_beca',
        'descripcion',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public $timestamps = false;
}