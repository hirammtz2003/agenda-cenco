<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_salud', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            
            // Datos básicos
            $table->integer('estatura');
            $table->float('peso');
            $table->enum('tipo_sangre', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->integer('frecuencia_dentista')->nullable();
            $table->float('graduacion_anteojos')->nullable();
            $table->boolean('cuadro_basico_vacunas')->default(false);
            
            // Padecimientos físicos
            $table->string('cirugia', 255)->nullable();
            $table->string('alergia', 255)->nullable();
            $table->string('limitante_fisico', 255)->nullable();
            $table->string('problema_auditivo', 255)->nullable();
            $table->string('adiccion', 255)->nullable();
            $table->string('padecimiento_emocional', 255)->nullable();
            $table->string('enfermedad_actual', 255)->nullable();
            
            // Salud mental
            $table->string('sintomas_cuales', 5)->nullable(); // Binario para 5 opciones
            $table->string('otro_sintoma', 255)->nullable();
            
            // Datos específicos
            $table->string('medicamento_controlado', 255)->nullable();
            $table->string('alergia_medicamento', 255)->nullable();
            $table->string('motivo_hospitalizacion', 255)->nullable();
            $table->boolean('diabetes')->default(false);
            $table->boolean('hipertension')->default(false);
            $table->boolean('dolores_cabeza')->default(false);
            $table->boolean('dolores_estomago')->default(false);
            $table->integer('frecuencia_medico')->nullable(); // -1: solo enfermo, 0: nunca, >0: meses
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_salud');
    }
};