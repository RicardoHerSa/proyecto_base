<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClCalidadTributariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_calidad_tributaria', function (Blueprint $table) {
            $table->id('id_calidad');
            $table->string('tipo_obligacion', 25)->nullable();
            $table->string('respons_fiscal', 50);
            $table->string('regimen_tributario', 50)->nullable();
            $table->string('clase_empresa', 10)->nullable();
            $table->integer('formulario_id');
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
        Schema::dropIfExists('cl_calidad_tributaria');
    }
}
