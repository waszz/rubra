<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diario_obras', function (Blueprint $table) {
            $table->unsignedInteger('mano_de_obra')->default(0)->after('cantidad_hoy')
                ->comment('Cantidad de obreros/jornales utilizados en el día');
        });
    }

    public function down(): void
    {
        Schema::table('diario_obras', function (Blueprint $table) {
            $table->dropColumn('mano_de_obra');
        });
    }
};
