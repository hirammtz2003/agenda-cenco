<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secundaria_procedencia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->enum('tipo', ['General', 'Técnica', 'Telesecundaria', 'Abierta', 'Privada', 'Otro']);
            $table->string('localidad', 30);
            $table->string('municipio', 30);
            $table->string('estado', 20);
            $table->string('pais', 15);
            
            $table->unique(['nombre', 'localidad', 'municipio', 'estado'], 'unique_secundaria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secundaria_procedencia');
    }
};