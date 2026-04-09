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
            $table->unsignedInteger('orden')->default(0)->after('categoria');
        });

        // Inicializar orden único por grupo de hermanos basado en id
        \DB::statement('UPDATE proyecto_recursos SET orden = id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};
