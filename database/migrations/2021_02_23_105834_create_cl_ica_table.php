<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClIcaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_ica', function (Blueprint $table) {
            $table->id('id_ica');
            $table->string('codigo_act');
            $table->string('ciudad_tributacion');
            $table->string('tarifa');
            $table->integer('formulario_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_icas');
    }
}
