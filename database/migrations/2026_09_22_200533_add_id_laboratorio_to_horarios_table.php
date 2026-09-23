<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_laboratorio')->nullable()->after('id_materia');
            $table->foreign('id_laboratorio')
                  ->references('id')
                  ->on('laboratorios')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropForeign(['id_laboratorio']);
            $table->dropColumn('id_laboratorio');
        });
    }
};