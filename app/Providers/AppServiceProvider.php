<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Validación personalizada para verificar contraseña actual
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, auth()->user()->password);
        }, 'La contraseña actual es incorrecta.');
    }
}