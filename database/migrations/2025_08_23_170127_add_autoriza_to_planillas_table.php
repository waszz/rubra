<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('planillas', function (Blueprint $table) {
        $table->string('autoriza')->nullable()->after('solicita');
    });
}

public function down()
{
    Schema::table('planillas', function (Blueprint $table) {
        $table->dropColumn('autoriza');
    });
}
};
