<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('user_id')      // <- cambiar el nombre de la columna
                  ->nullable()                // <- importante si ya hay registros
                  ->after('id')
                  ->constrained('users')      // <- referenciar la tabla users
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // <- cambiar también aquí
            $table->dropColumn('user_id');
        });
    }
};
