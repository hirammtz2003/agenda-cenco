<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familiares', function (Blueprint $table) {
            $table->id();
            $table->boolean('vive')->default(true);
            $table->string('nombre', 30);
            $table->string('apellido1', 20);
            $table->string('apellido2', 20)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('telefono_celular', 10)->nullable();
            $table->unsignedBigInteger('id_domicilio')->nullable();
            $table->enum('escolaridad', ['No tiene', 'Primaria', 'Secundaria', 'Bachillerato', 'Educación Superior', 'Otro'])->nullable();
            $table->string('ocupacion', 30)->nullable();
            $table->string('lugar_trabajo', 30)->nullable();
            $table->string('horario_laboral', 50)->nullable();
            $table->string('domicilio_trabajo', 100)->nullable();
            $table->string('telefono_trabajo', 10)->nullable();
            
            $table->foreign('id_domicilio')->references('id')->on('domicilio')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familiares');
    }
};