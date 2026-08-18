<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosAcademicos extends Model
{
    use HasFactory;

    protected $table = 'datos_academicos';

    protected $fillable = [
        'id_alumno',
        'dispositivos',
        'otro_dispositivo',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    /**
     * Convierte los dispositivos seleccionados a binario
     */
    public static function dispositivosToBinario($dispositivos)
    {
        if (empty($dispositivos)) {
            return null;
        }
        
        $binario = '';
        for ($i = 1; $i <= 4; $i++) {
            $binario .= in_array($i, $dispositivos) ? '1' : '0';
        }
        return rtrim($binario, '0') ?: null;
    }

    /**
     * Convierte la cadena binaria a array de dispositivos
     */
    public static function binarioToDispositivos($binario)
    {
        if (empty($binario)) {
            return [];
        }
        
        $dispositivos = [];
        $binarioCompleto = str_pad($binario, 4, '0', STR_PAD_RIGHT);
        
        for ($i = 0; $i < strlen($binarioCompleto); $i++) {
            if ($binarioCompleto[$i] === '1') {
                $dispositivos[] = $i + 1;
            }
        }
        
        return $dispositivos;
    }
}