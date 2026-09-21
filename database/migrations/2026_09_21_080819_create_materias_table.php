<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->enum('modulo', ['I', 'II', 'III', 'IV', 'V', 'No aplica'])->nullable();
            $table->enum('submodulo', ['1', '2', '3', 'No aplica'])->nullable();
            $table->unsignedBigInteger('id_laboratorio')->nullable();
            
            $table->foreign('id_laboratorio')
                  ->references('id')
                  ->on('laboratorios')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};