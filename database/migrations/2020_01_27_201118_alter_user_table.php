<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->after('name');
            $table->string('block')->after('remember_token');
            $table->string('sendEmail')->after('block');
            $table->string('registerDate')->after('sendEmail');
            $table->string('lastvisitDate')->after('registerDate');
            $table->string('activation')->after('lastvisitDate');
            $table->string('params')->after('activation');
            $table->string('lastResetTime')->after('params');
            $table->string('resetCount')->after('lastResetTime');
            $table->string('otpKey')->after('resetCount');
            $table->string('otep')->after('otpKey');
            $table->string('requireReset')->after('otep');
            $table->string('id_organizacion')->after('requireReset');
            $table->string('id_empleado')->after('id_organizacion');
            $table->string('ordinal_empleado')->after('id_empleado');
            $table->string('fecha_inicio')->after('ordinal_empleado');
            $table->string('fecha_fin')->after('fecha_inicio');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username')->nullable(); 
            $table->dropColumn('block')->nullable();
            $table->dropColumn('sendEmail')->nullable();
            $table->dropColumn('registerDate')->nullable();
            $table->dropColumn('lastvisitDate')->nullable();
            $table->dropColumn('activation')->nullable();
            $table->dropColumn('params')->nullable();
            $table->dropColumn('lastResetTime')->nullable();
            $table->dropColumn('resetCount')->nullable();
            $table->dropColumn('otpKey')->nullable();
            $table->dropColumn('otep')->nullable();
            $table->dropColumn('requireReset')->nullable();
            $table->dropColumn('id_organizacion')->nullable();
            $table->dropColumn('id_empleado')->nullable();
            $table->dropColumn('ordinal_empleado')->nullable();
            $table->dropColumn('fecha_inicio')->nullable();
            $table->dropColumn('fecha_fin')->nullable();

        });
    }
}
