<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas_realizadas', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 10)->unique();
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamp('consultado_en');
            $table->timestamps();
            
            $table->foreign('alumno_id')->references('id')->on('alumnos');
            $table->foreign('usuario_id')->references('id')->on('users');
            
            $table->index('folio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas_realizadas');
    }
};