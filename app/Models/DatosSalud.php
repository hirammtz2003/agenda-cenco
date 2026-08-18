<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosSalud extends Model
{
    use HasFactory;

    protected $table = 'datos_salud';

    protected $fillable = [
        'id_alumno',
        'estatura',
        'peso',
        'tipo_sangre',
        'frecuencia_dentista',
        'graduacion_anteojos',
        'cuadro_basico_vacunas',
        'cirugia',
        'alergia',
        'limitante_fisico',
        'problema_auditivo',
        'adiccion',
        'padecimiento_emocional',
        'enfermedad_actual',
        'sintomas_cuales',
        'otro_sintoma',
        'medicamento_controlado',
        'alergia_medicamento',
        'diabetes',
        'hipertension',
        'motivo_hospitalizacion',
        'dolores_cabeza',
        'dolores_estomago',
        'frecuencia_medico',
    ];

    protected $casts = [
        'estatura' => 'integer',
        'peso' => 'float',
        'graduacion_anteojos' => 'float',
        'usa_anteojos' => 'boolean',
        'cuadro_basico_vacunas' => 'boolean',
        'diabetes' => 'boolean',
        'hipertension' => 'boolean',
        'dolores_cabeza' => 'boolean',
        'dolores_estomago' => 'boolean',
        'frecuencia_dentista' => 'integer',
        'frecuencia_medico' => 'integer',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    /**
     * Convierte los síntomas seleccionados a binario (5 opciones)
     */
    public static function sintomasToBinario($sintomas)
    {
        if (empty($sintomas)) {
            return null;
        }
        
        $binario = '';
        for ($i = 1; $i <= 5; $i++) {
            $binario .= in_array($i, $sintomas) ? '1' : '0';
        }
        return rtrim($binario, '0') ?: null;
    }

    /**
     * Convierte la cadena binaria a array de síntomas
     */
    public static function binarioToSintomas($binario)
    {
        if (empty($binario)) {
            return [];
        }
        
        $sintomas = [];
        $binarioCompleto = str_pad($binario, 5, '0', STR_PAD_RIGHT);
        
        for ($i = 0; $i < strlen($binarioCompleto); $i++) {
            if ($binarioCompleto[$i] === '1') {
                $sintomas[] = $i + 1;
            }
        }
        
        return $sintomas;
    }
}