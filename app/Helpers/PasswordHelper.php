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
}