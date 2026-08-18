<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_familiares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->string('estado_civil_padres', 15)->nullable();
            $table->integer('ingreso_familiar_aprox')->nullable();
            $table->integer('gasto_familiar_aprox')->nullable();
            $table->boolean('casa_propia')->default(false);
            $table->string('servicios_casa', 4)->nullable();
            $table->boolean('auto_propio_familia')->default(false);
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_familiares');
    }
};