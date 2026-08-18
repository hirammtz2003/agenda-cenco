<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domicilio', function (Blueprint $table) {
            $table->id();
            $table->string('calle', 40);
            $table->string('num_ext', 10); // Cambiado a string porque pueden tener letras
            $table->string('num_int', 10)->nullable();
            $table->string('colonia', 40);
            $table->string('localidad', 30);
            $table->string('municipio', 30);
            $table->string('cp', 5);
            $table->string('estado', 20);
            $table->string('telefono_domicilio', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domicilio');
    }
};