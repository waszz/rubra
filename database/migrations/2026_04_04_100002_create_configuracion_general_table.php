<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_general', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa')->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('pagina_web', 500)->nullable();
            $table->string('redes_sociales')->nullable();
            $table->string('telefonos')->nullable();
            $table->string('correo')->nullable();
            $table->decimal('latitud', 17, 14)->nullable();
            $table->decimal('longitud', 17, 14)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_general');
    }
};