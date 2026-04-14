<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->boolean('imported')->default(false)->after('costo_real');
        });
    }

    public function down(): void
    {
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->dropColumn('imported');
        });
    }
};
