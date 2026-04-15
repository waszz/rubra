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
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->foreignId('depends_on_id')->nullable()->after('parent_id')
                  ->constrained('proyecto_recursos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proyecto_recursos', function (Blueprint $table) {
            $table->dropForeign(['depends_on_id']);
            $table->dropColumn('depends_on_id');
        });
    }
};
