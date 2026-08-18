<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('becas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_beca', 20);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
            
            $table->unique('tipo_beca', 'unique_beca');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('becas');
    }
};