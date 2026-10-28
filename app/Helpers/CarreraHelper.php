<?php

namespace App\Helpers;

class CarreraHelper
{
    public static function abreviar($texto)
    {
        if (!$texto) return $texto;
        
        $mapa = [
            'Soporte y Mantenimiento de Equipo de Cómputo' => 'SMEC',
            'Soporte y Gestión de Tecnologías Informáticas' => 'SyGTI',
            'Enfermería General' => 'EG',
            'Ventas' => 'Ventas',
            'Diseño Gráfico Digital' => 'DGD',
        ];
        
        foreach ($mapa as $largo => $corto) {
            $texto = str_replace($largo, $corto, $texto);
        }
        return $texto;
    }
}