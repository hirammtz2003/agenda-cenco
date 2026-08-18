<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('foto', 7)->nullable();
            $table->string('num_control', 14)->unique()->nullable();
            
            // Datos personales
            $table->string('nombre', 30);
            $table->string('apellido1', 20);
            $table->string('apellido2', 20)->nullable();
            
            // Llaves foráneas a tablas ya creadas
            $table->unsignedBigInteger('id_lugar_nacimiento')->nullable();
            $table->unsignedBigInteger('id_domicilio')->nullable();
            $table->unsignedBigInteger('id_secundaria_procedencia')->nullable();
            $table->unsignedBigInteger('id_grupo')->nullable();
            
            // Datos de contacto
            $table->string('telefono_celular', 10)->nullable();
            $table->string('email_personal', 50)->nullable();
            $table->string('email_institucional', 50)->nullable();
            $table->string('curp', 18)->unique()->nullable();
            $table->string('nss', 11)->nullable();
            
            $table->boolean('estatus')->default(true);
            
            // Foreign keys
            $table->foreign('id_lugar_nacimiento')->references('id')->on('lugar_nacimiento')->onDelete('set null');
            $table->foreign('id_domicilio')->references('id')->on('domicilio')->onDelete('set null');
            $table->foreign('id_secundaria_procedencia')->references('id')->on('secundaria_procedencia')->onDelete('set null');
            $table->foreign('id_grupo')->references('id')->on('grupos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};