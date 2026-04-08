<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('planillas', function (Blueprint $table) {
        $table->string('empresa')->nullable()->after('apellido');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planillas', function (Blueprint $table) {
            //
        });
    }
};
