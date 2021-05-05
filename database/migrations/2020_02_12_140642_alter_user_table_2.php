<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTable2 extends Migration
{
    /**
     * Run the migrations_
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_orgcountry')->after('sendEmail');
            $table->string('profile_externalid')->after('profile_orgcountry');
            $table->string('profile_ordinal')->after('profile_externalid');
            $table->string('gestor_externo')->after('profile_ordinal');
        });
    }

    /**
     * Reverse the migrations_
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_orgcountry')->nullable(); 
            $table->dropColumn('profile_externalid')->nullable(); 
            $table->dropColumn('profile_ordinal')->nullable(); 
            $table->dropColumn('gestor_externo')->nullable(); 
        });
    }
}
