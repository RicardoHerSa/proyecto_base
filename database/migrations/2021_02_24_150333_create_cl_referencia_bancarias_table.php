<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClReferenciaBancariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_referencia_bancarias', function (Blueprint $table) {
            $table->id('id_referencia');
            $table->string('nombre_banco');
            $table->string('sucursal');
            $table->string('numero_cuenta');
            $table->string('correo');
            $table->integer('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_referencia_bancarias');
    }
}
