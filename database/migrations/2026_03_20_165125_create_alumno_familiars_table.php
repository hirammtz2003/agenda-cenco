<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno_familiar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_familiar');
            $table->string('parentesco', 20);
            $table->boolean('tutor')->default(false);
            $table->tinyInteger('contacto_emergencia')->default(0);
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('id_familiar')->references('id')->on('familiares')->onDelete('cascade');
            $table->unique(['id_alumno', 'id_familiar'], 'unique_alumno_familiar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_familiar');
    }
};