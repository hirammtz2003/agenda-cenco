<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_final')->nullable();
            $table->integer('horas_requeridas')->nullable();
            $table->json('materiales')->nullable();
            $table->json('herramientas')->nullable();
            $table->enum('estatus', ['Pendiente', 'Autorizada', 'Rechazada', 'Concluida'])->default('Pendiente');
            $table->datetime('fecha_solicitud')->nullable();
            $table->string('notas', 255)->nullable();
            
            $table->unsignedBigInteger('id_autorizante')->nullable();
            $table->unsignedBigInteger('id_solicitante')->nullable();
            $table->unsignedBigInteger('id_actividad')->nullable();
            $table->unsignedBigInteger('id_grupo')->nullable();
            
            $table->foreign('id_autorizante')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_solicitante')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_actividad')->references('id')->on('actividades')->onDelete('set null');
            $table->foreign('id_grupo')->references('id')->on('grupos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practicas');
    }
};