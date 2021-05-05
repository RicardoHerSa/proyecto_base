<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClServicioEtiquetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_servicio_etiquetas', function (Blueprint $table) {
            $table->id('id_etiqueta');
            $table->string('dia_horario_revision');
            $table->string('datos_documentos');
            $table->string('dia_horario_pagos');
            $table->string('nombre_socio');
            $table->string('num_proveedor');
            $table->char('etiquetas');
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
        Schema::dropIfExists('cl_servicio_etiquetas');
    }
}
