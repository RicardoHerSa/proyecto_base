<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsergroupViewlevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usergroup_viewlevel', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('usergroup_id')->unsigned();
            $table->integer('viewlevel_id')->unsigned();
            $table->timestamps();

            $table->foreign('usergroup_id')->references('id')->on('usergroups');
            $table->foreign('viewlevel_id')->references('id')->on('viewlevels');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usergroup_viewlevel');
    }
}
