<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosFamiliares extends Model
{
    use HasFactory;

    protected $table = 'datos_familiares';

    protected $fillable = [
        'id_alumno',
        'estado_civil_padres',
        'ingreso_familiar_aprox',
        'gasto_familiar_aprox',
        'casa_propia',
        'servicios_casa',
        'auto_propio_familia',
    ];

    protected $casts = [
        'casa_propia' => 'boolean',
        'auto_propio_familia' => 'boolean',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    /**
     * Convierte los servicios seleccionados a binario
     * @param array $servicios Array de servicios seleccionados (0-indexado)
     * @return string Cadena binaria (ej: "1101" para servicios 1,2,4)
     */
    public static function serviciosToBinario($servicios)
    {
        if (empty($servicios)) {
            return null;
        }
        
        // Servicios en orden: 1=Energía eléctrica, 2=Agua corriente, 3=Drenaje, 4=Alumbrado público
        $binario = '';
        for ($i = 1; $i <= 4; $i++) {
            $binario .= in_array($i, $servicios) ? '1' : '0';
        }
        // Eliminar ceros a la derecha
        return rtrim($binario, '0') ?: null;
    }

    /**
     * Convierte la cadena binaria a array de servicios
     * @param string $binario Cadena binaria (ej: "1101")
     * @return array Array de servicios seleccionados
     */
    public static function binarioToServicios($binario)
    {
        if (empty($binario)) {
            return [];
        }
        
        $servicios = [];
        $binarioCompleto = str_pad($binario, 4, '0', STR_PAD_RIGHT);
        
        for ($i = 0; $i < strlen($binarioCompleto); $i++) {
            if ($binarioCompleto[$i] === '1') {
                $servicios[] = $i + 1;
            }
        }
        
        return $servicios;
    }

    // Obtiene la descripción de los servicios para mostrar
    public function getServiciosDescripcionAttribute()
    {
        $serviciosLista = [
            1 => 'Energía eléctrica',
            2 => 'Agua corriente',
            3 => 'Drenaje',
            4 => 'Alumbrado público'
        ];
        
        $serviciosSeleccionados = self::binarioToServicios($this->servicios_casa);
        $descripciones = [];
        
        foreach ($serviciosSeleccionados as $servicio) {
            $descripciones[] = $serviciosLista[$servicio];
        }
        
        return implode(', ', $descripciones);
    }
}