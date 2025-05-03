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
    Schema::table('vaccinations', function (Blueprint $table) {
        // إزالة المفتاح الأجنبي القديم إن وجد
        $table->dropForeign(['pediatrician_id']);

        // ربطه مع جدول users
        $table->foreign('pediatrician_id')->references('id')->on('users')->onDelete('cascade');
    });
}

};
