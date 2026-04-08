<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('planillas', function (Blueprint $table) {
        $table->id();
        $table->string('horario_habitual');
        $table->string('numero_funcionario');
        $table->string('nombre');
        $table->string('apellido');
        $table->text('registro_faltas')->nullable();
        $table->date('fecha');
        $table->string('horario_a_realizar');
        $table->string('motivos');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planillas');
    }
};
