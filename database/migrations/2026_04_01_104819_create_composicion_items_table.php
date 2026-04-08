<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('composicion_items', function (Blueprint $table) {
            $table->id();

            // Relación con recursos (la composición)
            $table->foreignId('composicion_id')
                ->constrained('recursos')
                ->cascadeOnDelete();

            // Nombre del material / recurso
            $table->string('nombre');

            // Cantidad necesaria
            $table->decimal('cantidad', 10, 3);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('composicion_items');
    }
};