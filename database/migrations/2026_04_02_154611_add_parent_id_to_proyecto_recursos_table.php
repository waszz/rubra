<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('proyecto_recursos', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('proyecto_id');
        $table->foreign('parent_id')->references('id')->on('proyecto_recursos')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('proyecto_recursos', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn('parent_id');
    });
}
};
