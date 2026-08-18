<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para MySQL, necesitamos modificar la columna enum
        DB::statement("ALTER TABLE secundaria_procedencia MODIFY tipo ENUM('General', 'Técnica', 'Telesecundaria', 'Abierta', 'Privada', 'Otro') NOT NULL");
    }

    public function down(): void
    {
        // Si necesitas revertir, puedes volver al tipo original
        DB::statement("ALTER TABLE secundaria_procedencia MODIFY tipo VARCHAR(255) NOT NULL");
    }
};