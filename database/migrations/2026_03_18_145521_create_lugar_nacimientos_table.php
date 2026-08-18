<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lugar_nacimiento', function (Blueprint $table) {
            $table->id();
            $table->string('localidad', 30);
            $table->string('municipio', 30);
            $table->string('estado', 20);
            $table->string('pais', 15);
            
            $table->unique(['localidad', 'municipio', 'estado', 'pais'], 'unique_lugar_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lugar_nacimiento');
    }
};