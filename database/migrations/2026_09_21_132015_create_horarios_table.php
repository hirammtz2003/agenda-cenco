<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('hora')->nullable();
            $table->enum('dia', ['L', 'M', 'X', 'J', 'V'])->nullable();
            $table->boolean('hora_fija')->default(false);
            
            $table->unsignedBigInteger('id_grupo')->nullable();
            $table->unsignedBigInteger('id_maestro')->nullable();
            $table->unsignedBigInteger('id_materia')->nullable();
            
            $table->foreign('id_grupo')->references('id')->on('grupos')->onDelete('set null');
            $table->foreign('id_maestro')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_materia')->references('id')->on('materias')->onDelete('set null');
            
            // Índice único para evitar duplicados (hora + día + grupo)
            $table->unique(['hora', 'dia', 'id_grupo'], 'horario_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};