<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('becas', function (Blueprint $table) {
            $table->dropTimestamps(); // Elimina created_at y updated_at
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
