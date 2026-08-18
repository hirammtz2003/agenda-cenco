<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_socioeco', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->boolean('auto_propio_alumno')->default(false);
            $table->json('transporte')->nullable();
            $table->integer('traslado_horas')->nullable();
            $table->integer('traslado_minutos')->nullable();
            $table->string('estado_civil_alumno', 15)->nullable();
            $table->integer('num_hijos')->default(0);
            $table->string('edades_hijos', 20)->nullable();
            $table->integer('monto_apoyo')->nullable();
            $table->integer('gasto_comida_transporte')->nullable();
            $table->integer('comidas_diarias')->default(3);
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_socioeco');
    }
};