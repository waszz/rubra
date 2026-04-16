<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gantt_fecha_historiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_recurso_id')->constrained('proyecto_recursos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('accion', ['guardado', 'eliminado', 'arrastrado'])->default('guardado');
            $table->date('fecha_inicio_anterior')->nullable();
            $table->date('fecha_fin_anterior')->nullable();
            $table->date('fecha_inicio_nueva')->nullable();
            $table->date('fecha_fin_nueva')->nullable();
            $table->unsignedTinyInteger('trabajadores_anterior')->nullable();
            $table->unsignedTinyInteger('trabajadores_nueva')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gantt_fecha_historiales');
    }
};
