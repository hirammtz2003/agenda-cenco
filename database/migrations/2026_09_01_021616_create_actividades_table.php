<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->integer('numero')->nullable();
            $table->string('competencia', 255)->nullable();
            $table->string('atributo', 255)->nullable();
            $table->string('materia', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};