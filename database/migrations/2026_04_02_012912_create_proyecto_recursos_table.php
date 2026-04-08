<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('proyecto_recursos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
        $table->foreignId('recurso_id')->constrained('recursos')->cascadeOnDelete();
        $table->string('nombre');
        $table->string('unidad');
        $table->float('cantidad');
        $table->float('precio_usd');
        $table->string('categoria')->nullable(); // ej: "Estructura", "Terminaciones"
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('proyecto_recursos');
}
};
