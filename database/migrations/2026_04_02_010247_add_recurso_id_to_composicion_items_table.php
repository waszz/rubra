<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('composicion_items', function (Blueprint $table) {
        $table->foreignId('recurso_id')->nullable()->constrained('recursos')->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('composicion_items', function (Blueprint $table) {
            //
        });
    }
};
