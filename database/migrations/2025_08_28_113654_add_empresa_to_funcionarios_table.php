<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->string('empresa')->after('cargo'); // Agrega la columna después de 'cargo'
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn('empresa');
        });
    }
};
