<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->enum('semestre', ['1°', '2°', '3°', '4°', '5°', '6°']);
            $table->enum('grupo', ['A', 'B']);
            $table->enum('carrera', [
                'Soporte y Mantenimiento de Equipo de Cómputo',
                'Soporte y Gestión de Tecnologías Informáticas',
                'Enfermería General',
                'Ventas',
                'Diseño Gráfico Digital'
            ]);
            $table->unsignedBigInteger('id_asesor')->nullable()->unique();
            $table->unsignedBigInteger('id_tutor')->nullable();
            $table->timestamps();
            
            $table->foreign('id_asesor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_tutor')->references('id')->on('users')->onDelete('set null');
            
            // Un grupo único por semestre, grupo y carrera
            $table->unique(['semestre', 'grupo', 'carrera'], 'grupo_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};