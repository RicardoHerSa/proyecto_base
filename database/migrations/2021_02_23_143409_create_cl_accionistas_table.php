<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClAccionistasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cl_accionistas', function (Blueprint $table) {
            $table->id('id_accion');
            $table->integer('tipo_persona');
            $table->string('nombre_razon', 100);
            $table->string('identificacion', 15);
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('pais_origen');
            $table->char('pep', 1);
            $table->char('participacion', 2);
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
        Schema::dropIfExists('cl_accionistas');
    }
}
