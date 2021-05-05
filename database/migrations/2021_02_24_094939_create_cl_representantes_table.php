<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClRepresentantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_representantes', function (Blueprint $table) {
            $table->id('id_repre');
            $table->string('nombre_apellido', 100);
            $table->string('identificacion', 15);
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('pais_origen');
            $table->char('pep', 1);
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
        Schema::dropIfExists('cl_representantes');
    }
}
