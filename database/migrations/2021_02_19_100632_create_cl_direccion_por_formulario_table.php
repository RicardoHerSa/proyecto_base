<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClDireccionPorFormularioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_direccion_por_formulario', function (Blueprint $table) {
            $table->id('id_ubicacion');
            $table->string('codigo_postal', 10);
            $table->string('telefono', 15);
            $table->string('direccion', 100);
            $table->integer('tipo_direccion');
            $table->string('nombre_colonia_provincia');
            $table->integer('municipio_id');
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
        Schema::dropIfExists('cl_direccion_por_formulario');
    }
}
