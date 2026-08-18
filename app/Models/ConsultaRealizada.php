<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaRealizada extends Model
{
    protected $table = 'consultas_realizadas';
    
    protected $fillable = [
        'folio', 'alumno_id', 'usuario_id', 'consultado_en'
    ];
    
    protected $casts = [
        'consultado_en' => 'datetime',
    ];
    
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public static function generarFolio()
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? intval($ultimo->folio) + 1 : 1;
        return str_pad($numero, 10, '0', STR_PAD_LEFT);
    }
}