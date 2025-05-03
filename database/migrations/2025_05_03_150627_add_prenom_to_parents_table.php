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
        Schema::table('parents', function (Blueprint $table) {
            $table->string('prenom')->nullable(); // إضافة عمود prénom
        });
    }
    
    public function down()
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('prenom'); // إزالة العمود إذا تم التراجع عن الترحيل
        });
    }
    
};
