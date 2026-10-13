<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->date('fecha_reserva')->nullable()->after('id_horario');
            $table->index(['fecha_reserva', 'id_horario'], 'practica_fecha_horario_index');
        });
    }

    public function down(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->dropIndex('practica_fecha_horario_index');
            $table->dropColumn('fecha_reserva');
        });
    }
};