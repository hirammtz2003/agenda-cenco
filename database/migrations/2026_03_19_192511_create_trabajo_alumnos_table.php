<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajo_alumno', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->string('lugar_trabajo', 30)->nullable();
            $table->string('horario_laboral', 50)->nullable();
            $table->string('domicilio_trabajo', 100)->nullable();
            $table->string('telefono_trabajo', 10)->nullable();
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajo_alumno');
    }
};