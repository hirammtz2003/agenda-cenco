<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_inhabiles', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_inhabiles');
    }
};