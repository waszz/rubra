<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diario_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('proyecto_recurso_id')->nullable()->constrained('proyecto_recursos')->nullOnDelete();
            $table->date('fecha');
            $table->float('avance_fisico')->default(0);   // % de avance
            $table->float('cantidad_hoy')->default(0);
            $table->float('costo_hoy')->default(0);
            $table->text('notas')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diario_obras');
    }
};