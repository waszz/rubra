<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planillas', function (Blueprint $table) {
            $table->string('area')->nullable()->after('cargo'); // Agrega columna área después de cargo
        });
    }

    public function down(): void
    {
        Schema::table('planillas', function (Blueprint $table) {
            $table->dropColumn('area'); // Para revertir
        });
    }
};
