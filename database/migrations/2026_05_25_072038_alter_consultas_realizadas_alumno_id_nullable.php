<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas_realizadas', function (Blueprint $table) {
            // Hacer que alumno_id pueda ser NULL
            $table->unsignedBigInteger('alumno_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('consultas_realizadas', function (Blueprint $table) {
            $table->unsignedBigInteger('alumno_id')->nullable(false)->change();
        });
    }
};