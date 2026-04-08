<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('empresa');
            $table->string('area');
            $table->string('turno');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('cantidad_dias');
            $table->integer('presentismo')->default(0); // porcentaje o valor según uses
            $table->integer('dias_restantes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licencias');
    }
};
