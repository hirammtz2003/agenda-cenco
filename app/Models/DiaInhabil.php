<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaInhabil extends Model
{
    use HasFactory;

    protected $table = 'dias_inhabiles';
    public $timestamps = false;

    protected $fillable = ['fecha'];

    protected $casts = [
        'fecha' => 'date'
    ];
}