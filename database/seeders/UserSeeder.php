<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'nombre' => 'Admin',
            'apellido1' => 'Sistema',
            'apellido2' => null,
            'email_personal' => 'hiram.isay.2014gg@gmail.com',
            'email_institucional' => 'hiram.isay.2014gg@gmail.com',
            'telefono' => '4981239726',
            'num_empleado' => 1001,
            'password' => Hash::make('123456'),
            'pw_temporal' => 0,
            'tipo' => 'Administrador',
            'estatus' => 1
        ]);
    }
}