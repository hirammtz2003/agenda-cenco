<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemaAprendizaje extends Model
{
    use HasFactory;

    protected $table = 'problema_aprendizaje';

    protected $fillable = [
        'id_alumno',
        'caracteristicas_especificas',
        'otro_problema',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    /**
     * Convierte los problemas seleccionados a binario (12 opciones)
     */
    public static function problemasToBinario($problemas)
    {
        if (empty($problemas)) {
            return null;
        }
        
        $binario = '';
        for ($i = 1; $i <= 12; $i++) {
            $binario .= in_array($i, $problemas) ? '1' : '0';
        }
        return rtrim($binario, '0') ?: null;
    }

    /**
     * Convierte la cadena binaria a array de problemas
     */
    public static function binarioToProblemas($binario)
    {
        if (empty($binario)) {
            return [];
        }
        
        $problemas = [];
        $binarioCompleto = str_pad($binario, 12, '0', STR_PAD_RIGHT);
        
        for ($i = 0; $i < strlen($binarioCompleto); $i++) {
            if ($binarioCompleto[$i] === '1') {
                $problemas[] = $i + 1;
            }
        }
        
        return $problemas;
    }
}