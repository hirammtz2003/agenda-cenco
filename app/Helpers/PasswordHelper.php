<?php

namespace App\Helpers;

class PasswordHelper
{
    /**
     * Genera una contraseña temporal de 5 dígitos numéricos
     */
    public static function generateTemporaryPassword(): string
    {
        return str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Obtiene los privilegios por defecto según el tipo de usuario
     */
    public static function getDefaultPrivilegios(string $tipo): string
    {
        return match($tipo) {
            'Administrador' => 'GNNNN',
            'Directivo' => 'NCCCC',
            'Docente' => 'NNNCC',
            'Trabajo Social' => 'NNGGG',
            default => 'NNNNN'
        };
    }

    /**
     * Valida que los privilegios tengan el formato correcto
     */
    public static function validatePrivilegios(string $privilegios): bool
    {
        return preg_match('/^[GNC]{5}$/', $privilegios) === 1;
    }
}