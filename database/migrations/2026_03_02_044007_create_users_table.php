<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->string('apellido1', 20);
            $table->string('apellido2', 20)->nullable();
            $table->string('email_personal', 50)->unique();
            $table->string('email_institucional', 50)->unique();
            $table->string('telefono', 10);
            $table->integer('num_empleado')->unique();
            $table->string('password');
            $table->boolean('pw_temporal')->default(true);
            $table->enum('tipo', ['Administrador', 'Directivo', 'Docente', 'Trabajo Social']);
            $table->string('privilegios', 5)->nullable();
            $table->boolean('estatus')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};