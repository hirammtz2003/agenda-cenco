<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades_recreativas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->string('pasatiempo_favorito', 100)->nullable();
            $table->integer('horas_pasatiempo_dedicadas')->nullable();
            $table->json('deporte_practicado')->nullable();
            $table->integer('horas_deporte_dedicadas')->nullable();
            $table->integer('horas_dia_tv')->nullable();
            $table->integer('horas_dia_compu')->nullable();
            $table->string('uso_frecuente_compu', 50)->nullable();
            $table->string('temas_quien_chat', 255)->nullable();
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades_recreativas');
    }
};