<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['material', 'labor', 'equipment', 'composition']);
            $table->string('unidad');
            $table->decimal('precio_usd', 10, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};