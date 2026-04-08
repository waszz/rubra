<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('SO')->unique(); // Letras y números
            $table->string('descripcion');
            $table->enum('estado', ['compras', 'proveedor', 'en stock'])->default('compras');
            $table->date('fechaSO'); // Fecha escrita manualmente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
