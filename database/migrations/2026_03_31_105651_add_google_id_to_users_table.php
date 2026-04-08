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
        Schema::table('users', function (Blueprint $table) {
            // Agregamos el campo google_id después del id original
            $table->string('google_id')->nullable()->after('id')->unique();
            
            // Hacemos que la contraseña sea nullable (opcional, pero recomendado 
            // si solo van a usar Google y no registro tradicional)
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminamos el campo si hacemos rollback
            $table->dropColumn('google_id');
            
            // Revertimos el cambio de password a obligatorio
            $table->string('password')->nullable(false)->change();
        });
    }
};