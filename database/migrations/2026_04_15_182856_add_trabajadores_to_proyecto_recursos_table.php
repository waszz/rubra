<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->unsignedTinyInteger('trabajadores')->default(1)->after('fecha_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->dropColumn('trabajadores');
        });
    }
};
