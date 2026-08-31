
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->enum('semestre', ['1°', '2°', '3°', '4°', '5°', '6°']);
            $table->enum('grupo', ['A', 'B']);
            $table->enum('carrera', [
                'Soporte y Mantenimiento de Equipo de Cómputo',
                'Soporte y Gestión de Tecnologías Informáticas',
                'Enfermería General',
                'Ventas',
                'Diseño Gráfico Digital'
            ]);
            
            // Índice único para evitar duplicados
            $table->unique(['semestre', 'grupo', 'carrera'], 'grupo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};