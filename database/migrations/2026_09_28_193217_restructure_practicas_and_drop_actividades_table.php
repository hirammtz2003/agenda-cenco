<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 0️⃣ Actualizar valores existentes de estatus al nuevo enum antes de modificarlo
        //    Esto evita el "Data truncated". Mapeamos valores viejos a valores nuevos.
        DB::table('practicas')->update(['estatus' => 'Reservada']);

        // 1️⃣ Eliminar foreign keys existentes antes de modificar la tabla
        Schema::table('practicas', function (Blueprint $table) {
            try { $table->dropForeign(['id_autorizante']); } catch (\Exception $e) {}
            try { $table->dropForeign(['id_solicitante']); } catch (\Exception $e) {}
            try { $table->dropForeign(['id_actividad']); } catch (\Exception $e) {}
            try { $table->dropForeign(['id_grupo']); } catch (\Exception $e) {}
        });

        // 2️⃣ Eliminar columnas de la versión vieja
        Schema::table('practicas', function (Blueprint $table) {
            $columnasAEliminar = [
                'fecha_inicio',
                'fecha_final',
                'horas_requeridas',
                'id_autorizante',
                'id_solicitante',
                'id_actividad',
                'id_grupo',
            ];

            foreach ($columnasAEliminar as $columna) {
                if (Schema::hasColumn('practicas', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });

        // 3️⃣ Eliminar la tabla 'actividades'
        Schema::dropIfExists('actividades');

        // 4️⃣ Agregar las columnas nuevas
        Schema::table('practicas', function (Blueprint $table) {
            if (!Schema::hasColumn('practicas', 'nombre')) {
                $table->string('nombre', 50)->nullable()->after('id');
            }
            if (!Schema::hasColumn('practicas', 'no_actividad')) {
                $table->integer('no_actividad')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('practicas', 'competencia')) {
                $table->string('competencia', 255)->nullable()->after('no_actividad');
            }
            if (!Schema::hasColumn('practicas', 'atributo')) {
                $table->string('atributo', 255)->nullable()->after('competencia');
            }
            if (!Schema::hasColumn('practicas', 'id_horario')) {
                $table->unsignedBigInteger('id_horario')->nullable()->after('notas');
                $table->foreign('id_horario')
                      ->references('id')
                      ->on('horarios')
                      ->onDelete('cascade');
            }
        });

        // 5️⃣ Cambiar el enum de 'estatus' a los nuevos valores (ya limpiamos antes)
        DB::statement("ALTER TABLE practicas MODIFY estatus ENUM('Reservada', 'Cumplida', 'No cumplida') DEFAULT 'Reservada'");
    }

    public function down(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            try { $table->dropForeign(['id_horario']); } catch (\Exception $e) {}
            if (Schema::hasColumn('practicas', 'id_horario')) {
                $table->dropColumn('id_horario');
            }
        });

        Schema::table('practicas', function (Blueprint $table) {
            foreach (['nombre', 'no_actividad', 'competencia', 'atributo'] as $col) {
                if (Schema::hasColumn('practicas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        DB::statement("ALTER TABLE practicas MODIFY estatus ENUM('Pendiente', 'Autorizada', 'Rechazada', 'Concluida') DEFAULT 'Pendiente'");

        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->integer('numero')->nullable();
            $table->string('competencia', 255)->nullable();
            $table->string('atributo', 255)->nullable();
            $table->string('materia', 50)->nullable();
            $table->timestamps();
        });

        Schema::table('practicas', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_final')->nullable();
            $table->integer('horas_requeridas')->nullable();
            $table->unsignedBigInteger('id_autorizante')->nullable();
            $table->unsignedBigInteger('id_solicitante')->nullable();
            $table->unsignedBigInteger('id_actividad')->nullable();
            $table->unsignedBigInteger('id_grupo')->nullable();
        });
    }
};