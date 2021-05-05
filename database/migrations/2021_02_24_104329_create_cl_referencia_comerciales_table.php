<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClReferenciaComercialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_referencia_comerciales', function (Blueprint $table) {
            $table->id('id_referen_comer');
            $table->string('nombre_empresa');
            $table->string('nombre_contacto');
            $table->string('cupo_credito');
            $table->string('telefono');
            $table->string('correo');
            $table->string('plazo_venta');
            $table->string('ciudad');
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
        Schema::dropIfExists('cl_referencia_comerciales');
    }
}
