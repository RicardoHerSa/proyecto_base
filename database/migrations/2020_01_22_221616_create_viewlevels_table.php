<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateViewlevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viewlevels', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('title')->nullable();
            $table->string('ordering')->nullable();
            $table->string('rules');
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('viewlevels');
    }
}
