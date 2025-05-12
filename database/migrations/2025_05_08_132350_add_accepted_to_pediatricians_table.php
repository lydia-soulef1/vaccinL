<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('pediatricians', function (Blueprint $table) {
        $table->boolean('accepted')->default(false);
    });
}

public function down()
{
    Schema::table('pediatricians', function (Blueprint $table) {
        $table->dropColumn('accepted');
    });
}

};
