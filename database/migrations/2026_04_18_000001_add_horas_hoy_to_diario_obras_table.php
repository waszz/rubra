<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diario_obras', function (Blueprint $table) {
            $table->float('horas_hoy')->default(0)->after('mano_de_obra')
                ->comment('Horas-hombre totales trabajadas hoy en este rubro (obreros × horas)');
        });
    }

    public function down(): void
    {
        Schema::table('diario_obras', function (Blueprint $table) {
            $table->dropColumn('horas_hoy');
        });
    }
};
