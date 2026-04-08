<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();

            // Relación
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Info básica
            $table->string('nombre_proyecto');
            $table->text('descripcion')->nullable();
            $table->text('notas')->nullable();
            $table->string('cliente')->nullable();

            // Ubicación
            $table->string('ubicacion')->nullable();
            $table->decimal('ubicacion_lat', 10, 6)->nullable();
            $table->decimal('ubicacion_lng', 10, 6)->nullable();

            // Configuración regional
            $table->string('mercado')->nullable();
            $table->string('moneda_base')->default('USD');
            $table->unsignedSmallInteger('horas_jornal')->default(8);

            // Métricas
            $table->decimal('metros_cuadrados', 10, 2)->default(0);
            $table->decimal('impuestos', 5, 2)->default(0);
            $table->decimal('beneficio', 5, 2)->default(0);
            $table->decimal('presupuesto_total', 15, 2)->default(0);
            $table->decimal('ganancia_estimada', 15, 2)->default(0);

            // Fechas
            $table->date('fecha_inicio')->nullable();

            // Estado
            $table->string('estado_obra')->default('en_revision');
            $table->string('estado_autorizacion')->default('pendiente');
            $table->string('plantilla_base')->default('en_blanco');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};