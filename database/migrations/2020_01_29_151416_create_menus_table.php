<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('menutype');
            $table->string('title')->unique();
            $table->string('alias')->nullable();
            $table->string('node')->nullable();
            $table->string('path')->nullable();
            $table->string('link');
            $table->string('type')->nullable();
            $table->string('published')->nullable();
            $table->string('parent_id')->nullable();
            $table->string('level')->nullable();
            $table->string('component_id')->nullable();
            $table->string('checked_out')->nullable();
            $table->string('checked_out_time')->nullable();
            $table->string('browserNav')->nullable();
            $table->string('access')->nullable();
            $table->string('img')->nullable();
            $table->string('template_style_id')->nullable();
            $table->string('params')->nullable();
            $table->string('lft')->nullable();
            $table->string('rgt')->nullable();
            $table->string('home')->nullable();
            $table->string('language')->nullable();
            $table->string('client_id')->nullable();

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
        Schema::dropIfExists('menus');
    }
}
