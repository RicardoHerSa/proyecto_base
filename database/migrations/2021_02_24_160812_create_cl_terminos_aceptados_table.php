<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClTerminosAceptadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_terminos_aceptados', function (Blueprint $table) {
            $table->id('id_termino');
            $table->string('ip');
            $table->string('cl_id_var_1');
            $table->string('cl_id_var_2');
            $table->string('contrato_1');
            $table->string('contrato_2');
            $table->string('contrato_3');
            $table->string('lat');
            $table->string('lon');
            $table->timestamp('fecha_registro');
            $table->integer('id_user');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_terminos_aceptados');
    }
}
