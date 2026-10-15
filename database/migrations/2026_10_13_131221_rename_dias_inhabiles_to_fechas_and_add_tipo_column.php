<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Renombrar tabla
        Schema::rename('dias_inhabiles', 'fechas');

        // 2. Agregar columna tipo
        Schema::table('fechas', function (Blueprint $table) {
            $table->enum('tipo', ['Día inhábil', 'Inicio de semestre', 'Fin de semestre'])
                  ->default('Día inhábil')
                  ->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('fechas', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
        Schema::rename('fechas', 'dias_inhabiles');
    }
};