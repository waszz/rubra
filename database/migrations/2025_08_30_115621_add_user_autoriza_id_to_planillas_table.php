<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('planillas', function (Blueprint $table) {
        $table->unsignedBigInteger('user_autoriza_id')->nullable()->after('estado_autorizacion');
        $table->foreign('user_autoriza_id')->references('id')->on('users')->onDelete('set null');
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
