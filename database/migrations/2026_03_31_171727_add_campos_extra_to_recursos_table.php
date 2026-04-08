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
    Schema::table('recursos', function (Blueprint $table) {
        $table->string('moneda')->default('USD')->after('precio_usd');
        $table->string('region')->nullable()->after('moneda');
        $table->string('vendedor')->nullable()->after('region');
        $table->boolean('precio_estimativo')->default(false)->after('vendedor');
        $table->string('marca_modelo')->nullable()->after('precio_estimativo');
        $table->text('observaciones')->nullable()->after('marca_modelo');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recursos', function (Blueprint $table) {
            //
        });
    }
};
