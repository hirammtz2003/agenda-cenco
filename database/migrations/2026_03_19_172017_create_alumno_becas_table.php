<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno_beca', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_beca');
            $table->boolean('activa')->default(true);
            
            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('id_beca')->references('id')->on('becas')->onDelete('cascade');
            $table->unique(['id_alumno', 'id_beca'], 'unique_alumno_beca');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_beca');
    }
};