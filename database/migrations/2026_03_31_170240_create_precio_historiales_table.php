<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precio_historiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurso_id')->constrained('recursos')->onDelete('cascade');
            $table->decimal('precio_anterior', 12, 2)->nullable();
            $table->decimal('precio_nuevo', 12, 2);
            $table->text('razon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precio_historiales');
    }
};
