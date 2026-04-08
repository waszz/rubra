<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up()
{
    Schema::table('proyectos', function (Blueprint $table) {
        // Usamos decimal para precisión en porcentajes (ej: 75.50)
        $table->decimal('carga_social', 5, 2)->default(0)->after('beneficio');
    });
}

public function down()
{
    Schema::table('proyectos', function (Blueprint $table) {
        $table->dropColumn('carga_social');
    });
}
};
